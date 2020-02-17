<?php


namespace App\Service;


class XmlProcessor
{

    public static function xmlFieldValues($xml): ?array
    {
        $xml = simplexml_load_string($xml);

        $keys = [];

        foreach ($xml as $key => $value) {
            $keys[] = $key;
        }

        return $keys;
    }
}