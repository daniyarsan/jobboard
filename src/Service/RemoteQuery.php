<?php

namespace App\Service;

use App\Service;

class RemoteQuery
{

    public function get($url)
    {
        if ($this->get_http_response_code($url) != '200') {
            throw new \Exception("File {$url} not found");
        }

        return file_get_contents($url);
    }

    public function getCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function get_http_response_code($url)
    {
        $headers = get_headers($url);
        return substr($headers[ 0 ], 9, 3);
    }
}