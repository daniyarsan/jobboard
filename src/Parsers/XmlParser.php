<?php


namespace App\Parsers;


use App\Entity\Feed;
use App\Entity\Job;
use App\Repository\FieldRepository;
use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class XmlParser
{
    private $em;
    private $xmlReader;
    private $counter;
    private $fieldRepo;
    private $jobRepository;
    private $disciplinesToAdd = [];
    private $specialtiesToAdd = [];

    public function __construct(
        EntityManagerInterface $em,
        FieldRepository $fieldRepo,
        JobRepository $jobRepository
    )
    {
        $this->xmlReader = new \XMLReader();
        $this->em = $em;
        $this->fieldRepo = $fieldRepo;
        $this->jobRepository = $jobRepository;
    }

    /**
     * Parse row by row for performance improvement
     *
     * @return
     */
    public function parse(Feed $feed)
    {
        /* Initiate counter */
        $this->counter = 0;
        $xmlRootElement = self::getXmlRootElement($feed->getXmlText());

        $this->xmlReader->open($feed->getUrl());
        while ($this->xmlReader->read() && $this->xmlReader->name != $xmlRootElement) {
            ;
        }

        while ($this->xmlReader->name == $xmlRootElement) {
            /* Get fields to set values for job */
            $mapperFields = array_filter($feed->getMapper());
            /* Get xml item (job) to import */
            $xmlItem = self::getArrayFromXmlString($this->xmlReader->readOuterXML());

            /* Remove previously imported jobs that dont have Ref id */
            $this->jobRepository->deleteByFeedId($feed->getSlug());

            $job = new Job();
            if (isset( $mapperFields[ 'refId' ] )) {
                $refId = $xmlItem[ $mapperFields[ 'refId' ] ] ?? false;
                $job = $this->jobRepository->findOneBy(['refId' => $refId]);
            }

            $job->setCompany($feed->getCompany());
            $job->setActive($feed->getActivate()); // Job Auto activation by Feed setting
            $job->setFeedId($feed->getSlug()); // For identification purposes
            /* Set Country Default Value */
            if (empty($job->getCountry()) && $feed->getDefaultCountry()) {
                $job->setCountry($feed->getDefaultCountry());
            }

            /* Loop through part of xml and call Job methods for hydration */
            foreach ($mapperFields as $mapKey => $mapItem) {
                $value = $xmlItem[ $mapItem ];

                if (!empty($mapperFields[ $mapKey ])) {
                    $method = 'set' . ucfirst($mapKey);
                    if (method_exists($job, $method)) {
                        call_user_func([$job, $method], $value);
                    } else {
                        $job->{$mapKey} = $value;
                    }
                }
            }

            $category = $job->getCategoryString();
            $categories = $this->em->getRepository('App:Category')->findCategoryByKeyword($category);
            if ($categories == null) {
                if (!in_array($category, $this->specialtiesToAdd)) {
                    $this->specialtiesToAdd[] = $category;
                }
            } else {
                $job->setCategoriesCollection(new ArrayCollection($categories));
            }

            $discipline = $job->getDiscipline();
            $disciplineEntity = $this->em->getRepository('App:Discipline')->findDisciplineByKeyword($discipline);

            if ($disciplineEntity == null) {
                if (!in_array($discipline, $this->disciplinesToAdd)) {
                    $this->disciplinesToAdd[] = $discipline;
                }
            } else {
                $job->setDiscipline($disciplineEntity);
                $this->em->persist($job);
                $this->em->flush();
            }

            /* Prepare next loop */
            $this->xmlReader->next($xmlRootElement);
            unset($xmlItem); // Clean Memory and iterate counter
            $this->counter++;
        }
    }

    public function getCounter()
    {
        return $this->counter;
    }

    public function getSpecialtiesToAdd()
    {
        return $this->specialtiesToAdd;
    }

    public function getDisciplinesToAdd()
    {
        return $this->disciplinesToAdd;
    }

    public static function getXmlAsArray($xmlString): ?array
    {
        $xml = self::getArrayFromXmlString($xmlString);

        return array_keys($xml);
    }

    protected static function getArrayFromXmlString($xmlString)
    {
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

        $fieldSet = json_decode(json_encode($xml), TRUE);

        foreach ($fieldSet as &$elem) {
            if (is_array($elem) && empty($elem)) {
                $elem = '';
            }
        }
        return $fieldSet;
    }

    protected static function getXmlRootElement($xmlString)
    {
        preg_match('/<(.*?)>/i', $xmlString, $mathc);
        if (isset($mathc[ 1 ]) && strlen($mathc[ 1 ]) > 0)
            return $mathc[ 1 ];

        return false;
        /*$xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $xml->getName();*/
    }
}