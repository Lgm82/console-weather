<?php

namespace ConsoleWeather;

use DateTime;

/**
 * ResponseHandler
 * Request Response Handling Class
 * 
 * @author     Luis Gómez Melgarejo <luis.gomelg@gmail.com>
 */
class ResponseHandler {
    /**
     * Print the response of the Forecast Weather Mode
     * 
     * @param array $data
     * 
     * @return string $response
     */
    public function filterForecastWeatherData($data)
    {
        $response = '';

        foreach ($data['DailyForecasts'] as $day) {
            $date = new DateTime($day['Date']);
            $response .= $date->format('F d, Y') . "\n";

            $response .= '> Weather' . "\n";
            $response .= '> Day: ' . $day['Day']['IconPhrase'];
            ($day['Day']['HasPrecipitation'] === true) ? $response .= '. ' . $day['Day']['PrecipitationIntensity'] . ' ' . $day['Day']['PrecipitationType'] . "\n" : '';
            $response .= '> Night: ' . $day['Night']['IconPhrase'];
            ($day['Night']['HasPrecipitation'] === true) ? $response .= '. ' . $day['Night']['PrecipitationIntensity'] . ' ' . $day['Night']['PrecipitationType'] . "\n" : '';

            $response .= '> Temperature' . "\n";
            $response .= '> Min: ' . $day['Temperature']['Minimum']['Value'] . 'º ' . $day['Temperature']['Minimum']['Unit'] . "\n";
            $response .= '> Max: ' . $day['Temperature']['Maximum']['Value'] . 'º ' . $day['Temperature']['Maximum']['Unit'] . "\n" . "\n";
        }

        return $response;
    }

    /**
     * Print the response of the Current Weather Mode
     * 
     * @param array $data
     * 
     * @return string $response
     */
    public function filterCurrentWeatherData($data, $scale)
    {
        $response = '';
    
        foreach ($data as $day) {
            $date = new DateTime($day['Date']);
            $response .= $date->format('F d, Y') . "\n";
    
            $response .= '> Weather: ' . $day['WeatherText'] . "\n";
            ($day['HasPrecipitation'] === true) ? $response .= '. ' . $day['PrecipitationIntensity'] . ' ' . $day['PrecipitationType'] . "\n" : '';
    
            $response .= '> Temperature: ' . $day['Temperature'][$scale]['Value'] . 'º ' . $day['Temperature'][$scale]['Unit'] . "\n";
        }

        return $response;
    }
}
