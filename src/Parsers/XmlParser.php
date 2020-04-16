<?php


namespace App\Parsers;


use App\Entity\Feed;
use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;

class XmlParser
{
    private $em;
    private $xmlReader;
    private $feed;
    private $counter;

    public function __construct(EntityManagerInterface $em)
    {
        $this->xmlReader = new \XMLReader();
        $this->em = $em;
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
            $job = new Job();
            $job->setCompany($feed->getCompany());

            $job->setActive($feed->getActivate()); // Job Auto activation by Feed setting
            $job->setFeedId($feed->getSlug()); // For identification purposes

            /* Get xml item (job) to import */
            $xmlItem = self::getArrayFromXmlString($this->xmlReader->readOuterXML());
            /* Get fields to set values for job */
            $mapperFields = array_filter($feed->getMapper());

            /* Loop through part of xml and call Job methods for hydration */
            foreach ($mapperFields as $mapKey => $mapItem) {
                if (!empty($mapperFields[$mapKey])) {
                    $method = 'set' . ucfirst($mapKey);
                    if (method_exists($job, $method)) {
                        call_user_func([$job, $method], $xmlItem[$mapItem]);
                    }
                }
            }

            /* Set Country Default Value */
            if (empty($job->getCountry()) && $feed->getDefaultCountry()) {
                $job->setCountry($feed->getDefaultCountry());
            }

            $this->em->persist($job);
            $this->em->flush();

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

    public static function getXmlAsArray($xmlString): ?array
    {
        $xml = self::getArrayFromXmlString($xmlString);

        return  array_keys($xml);
    }



    protected static function getArrayFromXmlString($xmlString)
    {
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

        return json_decode(json_encode($xml), TRUE);
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