<?php


namespace App\FeedImporter;


use App\Entity\Feed;
use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;

class XmlParser
{
    private $em;
    private $xmlReader;
    private $feed;

    private $importCounter;

    public function __construct(EntityManagerInterface $em, Feed $feed)
    {
        $this->em = $em;
        $this->feed = $feed;
        $this->xmlReader = new \XMLReader();
        $this->importCounter = 0;
    }

    /**
     * Parse row by row for performance improvement
     *
     * @return
     */
    public function parse()
    {
        $this->xmlReader->open($this->feed->getUrl());
        $xmlRootElement = self::getXmlRootElement($this->feed->getXmlText());

        while ($this->xmlReader->read() && $this->xmlReader->name != $xmlRootElement) {
            ;
        }

        while ($this->xmlReader->name == $xmlRootElement) {
            $xmlItem = self::getArrayFromXmlString($this->xmlReader->readOuterXML());
            $xmlItemsToLoop = array_filter($this->feed->getMapper());

            $job = new Job();
            $job->setCompany($this->feed->getCompany());

            foreach ($xmlItemsToLoop as $mapKey => $mapItem) {
                if (!empty($xmlItemsToLoop[$mapKey])) {
                    $method = 'set' . ucfirst($mapKey);
                    if (method_exists($job, $method)) {
                        $job->$method($xmlItem[$mapItem]);
                    }
                }
            }

            /* Auto activation */
            if ($this->feed->getActivate()) {
                $job->activateJob();
            }

            /* For identification purposes */
            $job->setFeedId($this->feed->getSlug());
            $this->em->persist($job);
            $this->em->flush($job);

            $this->xmlReader->next($xmlRootElement);
            unset($element);
            $this->importCounter++;
        }
    }

    public function getImportedNum()
    {
        return $this->importCounter;
    }

    public static function getXmlFields($xmlString): ?array
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
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

        return $xml->getName();
    }
}