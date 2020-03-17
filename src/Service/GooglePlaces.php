<?php


namespace App\Service;


class GooglePlaces
{
    private $sleep = 3;
    private $response;
    private $query;
    private $reference;
    private $photoreference;
    private $key = 'AIzaSyDlbUY4VxaUAdbKAf1GvFkPzo9v_QO3f3c';
    //private $key       = 'AIzaSyDStTN8a_u13sM93xKHInZp4mmX4nQf7Hs';
    private $base_url = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
    private $details_url = 'https://maps.googleapis.com/maps/api/place/details/json';
    private $image_url = 'https://maps.googleapis.com/maps/api/place/photo';
    private $nearby_url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';
    private $screenshotUrl = 'https://www.googleapis.com/pagespeedonline/v1/runPagespeed';

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function setReference($refrence)
    {
        $this->reference = $refrence;
    }

    public function setPhotoReference($reference)
    {
        $this->photoreference = $reference;
    }

    public function getPhotoResult($width = false)
    {
        $params = array(
            'maxwidth' => $width,
            'photoreference' => $this->photoreference,
            'key' => $this->key
        );
        return $this->queryPhotoGoogle($this->image_url, $params);
    }

    public function getScreenshotResult($websiteUrl)
    {
        $params = array(
            'url' => $websiteUrl,
            'photoreference' => rand(1, 999999),
            'screenshot' => 'true',
            'key' => $this->key
        );

        return $this->queryScreenshotGoogle($this->screenshotUrl, $params);
    }

    public function getNearbyResult($location, $radius)
    {
        $companiesInfo = array();
        $params = array(
            'location' => $location,
            'radius' => $radius,
            'key' => $this->key
        );

        $pagetoken = true;

        while ($pagetoken) {
            $results = $this->queryGoogle($this->nearby_url, $params);
            $companiesInfo = array_merge($companiesInfo, $results[ 'results' ]);

            if (isset($this->response[ 'next_page_token' ])) {
                $this->pagetoken = $this->response[ 'next_page_token' ];
            } else {
                $this->pagetoken = null;
                $pagetoken = false;
            }
        }
        return $companiesInfo;
    }

    public function getDetailsResult()
    {
        $params = array(
            'reference' => $this->reference,
            'key' => $this->key
        );
        return $this->queryGoogle($this->details_url, $params);
    }

    public function getResult()
    {
        $companiesInfo = array();
        $params = array(
            'query' => $this->query,
            'key' => $this->key
        );

        $pagetoken = true;
        $this->pagetoken = null;
        while ($pagetoken) {
            $results = $this->queryGoogle($this->base_url, $params);
            $companiesInfo = array_merge($companiesInfo, $results[ 'results' ]);

            if (isset($this->response[ 'next_page_token' ])) {
                $this->pagetoken = $this->response[ 'next_page_token' ];
            } else {
                $this->pagetoken = null;
                $pagetoken = false;
            }
        }
        return $companiesInfo;

    }

    private function queryPhotoGoogle($url, $parameters)
    {

        $querystring = '';
        foreach ($parameters as $variable => $value) {
            if ($querystring != '') {
                $querystring .= '&';
            }
            $querystring .= $variable . '=' . $value;
        }

        return $this->getImage($url . '?' . $querystring, $parameters[ 'photoreference' ]);
    }

    private function queryScreenshotGoogle($url, $parameters)
    {

        $data = $this->queryGoogle($url, $parameters);

        return $this->getScreenshot($data[ 'screenshot' ][ 'data' ], $parameters[ 'photoreference' ]);
    }

    private function queryGoogle($url, $parameters)
    {
        if ($this->pagetoken !== null) {
            $parameters = null;
            $parameters[ 'pagetoken' ] = $this->pagetoken;
            $parameters[ 'key' ] = $this->key;
            sleep($this->sleep);
        }

        $querystring = '';
        foreach ($parameters as $variable => $value) {
            if ($querystring != '') {
                $querystring .= '&';
            }
            $querystring .= $variable . '=' . $value;
        }

        $response = $this->get($url . '?' . $querystring);

        $response = json_decode($response, true);
        if ($response === null) {
            throw new \Exception('The returned JSON was malformed or nonexistent.');
        }

        $this->response = $response;
        return $this->response;
    }

    private function get($url)
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
        );
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        if ($error = curl_error($curl)) {
            throw new \Exception('CURL Error: ' . $error);
        }
        curl_close($curl);
        return $response;
    }

    private function getImage($url, $ref)
    {
        $saveto = 'assets/uploads/' . sha1($ref) . '.png';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $raw = curl_exec($ch);
        curl_close($ch);

        if (file_exists('./web/' . $saveto)) {
            unlink('./web/' . $saveto);
        }
        $fp = fopen('./web/' . $saveto, 'x');
        fwrite($fp, $raw);
        fclose($fp);

        return $saveto;
    }

    private function getScreenshot($data, $ref)
    {

        $data = str_replace('_', '/', $data);
        $data = str_replace('-', '+', $data);
        $decoded = base64_decode($data);

        $filename = sha1($ref) . '.png';
        $saveto = 'assets/uploads/' . 'test.png';

        if (file_exists('./web/' . $saveto)) {
            unlink('./web/' . $saveto);
        }
        file_put_contents('./web/' . $saveto, $decoded);

        return $filename;
    }
}