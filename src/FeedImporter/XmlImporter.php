<?php


namespace App\FeedImporter;


class XmlImporter extends AbstractImporter
{

    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function run()
    {
        $data = $this->getDataFromUrl();
        var_dump($data);
        exit;
    }

    protected function getDataFromUrl()
    {
        return file_get_contents($this->url);
    }

}