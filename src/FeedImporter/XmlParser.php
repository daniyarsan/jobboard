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

    public function __construct(EntityManagerInterface $em, Feed $feed)
    {
        $this->em = $em;
        $this->feed = $feed;
        $this->xmlReader = new \XMLReader();
    }


    /**
     * Parse row by row for performance improvement
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

            $job = new Job();
            $job->setCompany($this->feed->getCompany());

            foreach ($this->feed->getMapper() as $mapKey => $mapItem) {
                if ($mapItem) {
                    $method = 'set' . ucfirst($mapItem);
                    if (method_exists($job, $method)) {
                        $job->$method($xmlItem[ $mapKey ]);
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

            $this->xmlReader->next('job');
            unset($element);
        }
    }

    public static function getArrayFromXmlString($xmlString)
    {
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

        return json_decode(json_encode($xml), TRUE);
    }

    public static function getXmlRootElement($xmlString)
    {
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

        return $xml->getName();
    }

    public static function getXmlFieldNames($xmlString): ?array
    {
        $xml = self::getArrayFromXmlString($xmlString);

        $keys = [];

        foreach ($xml as $key => $value) {
            $keys[] = $key;
        }

        return array_map(function ($v) {
            return (!is_null($v)) ? "" : $v;
        }, array_flip($keys));
    }
}