<?php

namespace ConsoleWeather;

use Exception;

/**
 * RequestHandler
 * Request Handling Control Class
 * 
 * @author     Luis Gómez Melgarejo <luis.gomelg@gmail.com>
 */
class RequestHandler
{
    const API_KEY = 'FQQbfEnM9qIQmt0kLPLxITjaDkzrdMAR'; //AccuWeather API Key
    const MODE_URLS = [
        'current' => 'http://dataservice.accuweather.com/currentconditions/v1/',
        'forecast' => 'http://dataservice.accuweather.com/forecasts/v1/daily/',
        'city' => 'http://dataservice.accuweather.com/locations/v1/cities/search'
    ];
    const LANG = 'en-us';

    private $days;
    private $forecast;
    private $metric;
    private $scale;
    private $url;

    /**
     * @method __construct
     * @param object $curl
     * @param object $response
     * 
     * @return void
     */
    public function __construct()
    {
        $this->curl = new Curl();
        $this->response = new ResponseHandler();
    }

    /**
     * Set the API URL Endpoint
     * 
     * @param string $url
     * @param string $options
     * @param string $city
     * @param string $metric
     * 
     * @return void
     */
    private function setGetUrl($url, $options = null, $city = null, $metric = null)
    {
        if (!is_null($options)) {
            $this->url = $url . '?apikey=' . self::API_KEY . '&q=' . $options . '&language=' . self::LANG;
        } else if (!is_null($this->days)) {
            $this->url = $url . $this->days . $city . '?apikey=' . self::API_KEY . '&language=' . self::LANG . '&metric=' . $metric;
        } else {
            $this->url = $url . $city . '?apikey=' . self::API_KEY . '&language=' . self::LANG;
        }
    }

    /**
     * Request maker
     * 
     * @return string API Response
     */
    public function sendRequest()
    {
        return $this->curl->request($this->url);
    }

    /**
     * Request maker
     * 
     * @param string $city
     * @param string $country
     * 
     * @return string City Code API Response
     */
    private function filterCity($cities, $country)
    {
        $city_code = null;

        if (count($cities) > 0) {
            foreach ($cities as $city) {
                if (isset($city['Country']['ID']) && trim($city['Country']['ID']) === $country) {
                    $city_code = $city['Key'];
                }
            }
        }

        if (!is_null($city_code)) {
            return $city_code;
        } else {
            throw new Exception('City not found');
        }
    }

    /**
     * Return the API Response to print on the screen
     * 
     * @param array $data
     * @param string $scale
     * 
     * @return string City Code API Response
     */
    private function filterWeatherData($data, $scale)
    {
        if ($this->forecast === true) {
            return $this->response->filterForecastWeatherData($data);
        } else {
            return $this->response->filterCurrentWeatherData($data, $scale);
        }
    }

    /**
     * Get the data of the Request City on the AccuWeather API
     * 
     * @param string $place 
     * @param string $country
     * 
     * @return string API response
     */
    public function getCity($city, $country)
    {
        try {
            $this->setGetUrl(self::MODE_URLS['city'], $city);

            $cities = json_decode($this->sendRequest(), true);

            return $this->filterCity($cities, $country);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Set the mode of the API request
     * 
     * @return void
     */
    private function setForecast($mode)
    {
        ($mode === 'forecast') ? $this->forecast = true : $this->forecast = false;
    }

    /**
     * Set the days number for forecast API request
     * 
     * @param array $options
     * 
     * @return void
     */
    private function setForecastDays($options)
    {
        ($this->forecast === true) ? $this->days = $options['days'] . 'day/' : null;
    }

    /**
     * Set the unit scale for API request
     * 
     * @param array $options
     * 
     * @return void
     */
    private function setMetricUnits($options)
    {
        ($options['units'] === 'imperial') ? $this->metric = 'false' : $this->metric = 'true';
    }

    /**
     * Set the scale for API request
     * 
     * @param array $options
     * 
     * @return void
     */
    private function setScale($options)
    {
        ($options['units'] === 'imperial') ? $this->scale = 'imperial' : $this->scale = 'metric';
    }

    /**
     * Get the data of the Request City on the AccuWeather API
     * 
     * @param string $place 
     * @param string $country
     * 
     * @return string API response
     */
    public function getWeather($city, $mode, $options)
    {
        try {
            $this->setForecast($mode);
            $this->setForecastDays($options);
            $this->setMetricUnits($options);
            $this->setScale($options);
            $this->setGetUrl(self::MODE_URLS[$mode], null, $city, $this->metric);

            $weather_data = json_decode($this->sendRequest(), true);

            return $this->filterWeatherData($weather_data, ucfirst($this->scale));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
