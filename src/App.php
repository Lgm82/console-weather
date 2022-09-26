<?php

namespace ConsoleWeather;

/**
 * AppController 
 * Request Control Class
 * 
 * @author     Luis Gómez Melgarejo <luis.gomelg@gmail.com>
 */
class App
{
    private $command_validator;
    private $request_handler;

    /**
     * @method __construct
     * @param object $CommandValidator
     * @param object $RequestHandler
     * 
     * @return void
     */
    public function __construct()
    {
        $this->command_validator = new CommandValidator();
        $this->request_handler = new RequestHandler();
    }

    /**
     * Perform an API request throug a console command
     * 
     * @param int $nargs Number of arguments of the command
     * @param array $command Console line argument array
     * 
     * @return string $response
     */
    public function makeRequest($nargs = 0, $command = [])
    {
        if ($this->command_validator->validateCommand($nargs, $command)) {
            $city_data = $this->request_handler->getCity($this->command_validator->getCity(), $this->command_validator->getCountryCode());
            $weather_data = $this->request_handler->getWeather($city_data, $this->command_validator->getMode(), $this->command_validator->getOptions());

            echo ucfirst($this->command_validator->getCity()) . ' (' . $this->command_validator->getCountryCode() . ')' . "\n";
            echo $weather_data . "\n";
        }
    }
}
