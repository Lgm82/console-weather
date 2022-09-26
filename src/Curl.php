<?php

namespace ConsoleWeather;

/**
 * Curl
 * Curl Handling Control Class
 * 
 * @author     Luis Gómez Melgarejo <luis.gomelg@gmail.com>
 */
class Curl
{
    public function request($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        curl_close($ch);

        return $result;
    }
}
