<?php

namespace App\Service;


class GlassDoor
{

    public $urlApi = "http://api.glassdoor.com/api/api.htm";
    private $publisherID = '53483';
    private $publisherKey = 'd1JJqb9N42k';
    //public static $url = "http://api.glassdoor.com/api/api.htm?v=1&format=json&t.p=53483&t.k=d1JJqb9N42k&action=employers&q=pharmaceuticals&userip=128.127.80.167&useragent=Mozilla/%2F4.0";
    public $limit = 25;
    public $radius = 200;




    public function urlConfig($values)
    {
        $params = array(
            "v" => "1",                // The API version. The current version is 1 except for jobs, which is currently version 1.1
            "format" => "json",       // Either xml or json as you prefer
            "t.p" => $this->publisherID,                 // Your partner id, as assigned by Glassdoor
            "t.k" => $this->publisherKey,             // Your partner key, as assigned by Glassdoor
            "userip" => $this->makeIP(),              // The IP address of the end user to whom the API results will be shown
            "useragent" => $this->makeAgent(),              // Job type. Allowed values: "fulltime", "parttime", "contract", "internship", "temporary".
            "callback" => "",         // If json is the requested format, you may specify a jsonp callback here, allowing you to make cross-domain calls to the glassdoor API from your client-side javascript. See the JSONP wikipedia entry for more information on jsonp.
            "action" => "employers",          // Must be set to employers
            'q' => '',   // Query phrase to search for - can be any combination of employer or occupation, but location should be in l param.
            'l' => '',  // Scope the search to a specific location by specifying it here - city, state, or country.
            "city" => "",         // Scope the search to a specific city by specifying it here.
            "state" => "",      // Scope the search to a specific state by specifying it here.
            "country" => "",              // Scope the search to a specific country by specifying it here.
            "pn" => "",           // Page number to retrieve. Default is 1.
            "ps" => "",   // Page size, i.e. the number of jobs returned on each page of results. Default is 20.
        );

        foreach ($values as $key => $value) {
            if (isset($params[ $key ])) {
                $params[ $key ] = urlencode($value);
            }
        }

        foreach ($params as $key => $param) {
            if ($param === "")
                unset($params[ $key ]);
        }

        return $this->makeConfigAsParams($params);
    }


    public function getCompany($companyName = "", $city = "", $country = "")
    {
        //http://api.indeed.com/ads/apisearch?publisher=3615316307742383&q=&l=HE&sort=&radius=&st=&jt=&start=&limit=&fromage=&filter=&latlong=1&co=de&chnl=&userip=1.2.3.4&useragent=Mozilla/%2F4.0%28Firefox%29&v=2&format=json

        $employer = false;
        $config = array(
            "q" => $companyName,
        );

        if ($city)
            $config[ "city" ] = $city;

        if ($country)
            $config[ "country" ] = $country;

        $result = $this->getResults($config);

        if (isset($result[ "response" ])) {
            $responce = $result[ "response" ];
            $employers = $responce[ "employers" ];
            $employer = isset($employers[ 0 ]) ? $employers[ 0 ] : false;
        }

        return $employer;
    }

    public function getRating($profile)
    {
        //http://api.indeed.com/ads/apisearch?publisher=3615316307742383&q=&l=HE&sort=&radius=&st=&jt=&start=&limit=&fromage=&filter=&latlong=1&co=de&chnl=&userip=1.2.3.4&useragent=Mozilla/%2F4.0%28Firefox%29&v=2&format=json

        $employer = false;
        $config = array(
            "q" => $profile->getCompanyName(),
        );

        if ($city = $profile->getAddressRegion())
            $config[ "city" ] = $city;

        if ($country = $profile->getCountry())
            $config[ "country" ] = $country;

        $result = $this->getResults($config);

        if (isset($result[ "totalRecordCount" ]) && $result[ "totalRecordCount" ] == 0) {
            unset($config[ "city" ]);
            unset($config[ "country" ]);
            $result = $this->getResults($config);
        }

        if (isset($result[ "response" ])) {
            $responce = $result[ "response" ];
            $employers = $responce[ "employers" ];
            $employer = isset($employers[ 0 ]) ? $employers[ 0 ] : false;
        }

        return $employer;
    }

    public function makeConfigAsParams($params)
    {
        $paramUrl = "";

        $i = 0;
        foreach ($params as $key => $value) {
            $i++;
            $startUrl = "&";

            if ($i == 1)
                $startUrl = "?";

            $paramUrl .= $startUrl . $key . '=' . $value;
        }

        return $paramUrl;
    }

    public function makeIP($default = false)
    {
        $ip = "";
        if ($default)
            return "128.127.80.167";


        if (isset($_SERVER[ 'REMOTE_ADDR' ])) {
            $ip = urlencode($_SERVER[ 'REMOTE_ADDR' ]);
        } elseif (isset($_SERVER[ 'SERVER_ADDR' ])) {
            $ip = urlencode($_SERVER[ 'SERVER_ADDR' ]);
        } else {
            $ip = urlencode('1.2.3.4');
        }
        return $ip;
    }

    public function makeAgent()
    {
        return (isset($_SERVER[ 'HTTP_USER_AGENT' ]) ? urlencode($_SERVER[ 'HTTP_USER_AGENT' ]) : urlencode('Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5'));
    }

    public function getResults($config)
    {
        $urlApi = $this->urlApi . $this->urlConfig($config);

        return json_decode(
            $this->getContent($urlApi), true
        );
    }

    private function getContent($url)
    {
        return file_get_contents($url);
    }
}
