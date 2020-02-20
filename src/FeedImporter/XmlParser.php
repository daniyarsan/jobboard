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

    public function parse()
    {
        $this->xmlReader->open($this->feed->getUrl());

        while ($this->xmlReader->read() && $this->xmlReader->name != 'job') {
            ;
        }

        while ($this->xmlReader->name == 'job') {
            $xmlItem = $this->getArrayFromXmlString($this->xmlReader->readOuterXML());

            $job = new Job();
            foreach ($this->feed->getMapper() as $mapKey => $mapItem) {
                if ($mapItem) {
                    $method = 'set' . $mapItem;
                    if (method_exists($job, $method)) {
                        $job->$method($xmlItem[$mapKey]);
                    }
                }
            }

            dump($job);
            exit;

            $job = new Job();

            $this->xmlReader->next('job');
            unset($element);
        }
    }

    protected function xmlToArray($xmlObject, $out = array())
    {
        foreach ((array)$xmlObject as $index => $node)
            $out[ $index ] = (is_object($node)) ? $this->xmlToArray($node) : $node;

        return $out;
    }

    protected function getArrayFromXmlString($xmlString)
    {
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

        return json_decode(json_encode($xml), TRUE);
    }
}