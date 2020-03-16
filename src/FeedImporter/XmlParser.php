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

        return  array_map(create_function('$n', 'return null;'), $xml);
    }

    public static function getXmlFields($xmlString): ?array
    {
        $xml = self::getArrayFromXmlString($xmlString);

        return  array_keys($xml);
    }
}