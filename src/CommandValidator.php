<?php

namespace ConsoleWeather;

use Exception;
use SameerShelavale\PhpCountriesArray\CountriesArray;

/**
 * CommandValidator
 * Input Command Line Control Class
 * 
 * @author     Luis Gómez Melgarejo <luis.gomelg@gmail.com>
 */
class CommandValidator
{
    const ALLOWED_OPTIONS = ['days', 'units', 'help'];
    const DEFAULT_OPTIONS_ALLOWED_VALUES = ['days' => [1, 5]];
    const DEFAULT_OPTIONS_VALUES = ['days' => 1, 'units' => 'metric'];
    const MODE_RELATED_OPTIONS = ['forecast' => ['days', 'units'], 'current' => 'units'];
    const MODES = ['forecast', 'current'];

    private $mode;
    private $city;
    private $country_code;
    private $options;

    /**
     * Return the request mode var
     * 
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Return the request city var
     * 
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Return the request country code var
     * 
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * Return the request options
     * 
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Check that the script only run throug command line
     * 
     * @return void
     */
    private function validateSAPI()
    {
        if (php_sapi_name() !== 'cli') {
            throw new Exception('This app only run through command line!');
        }
    }

    /**
     * Check that the number of arguments is at least three
     * 
     * @return void
     */
    private function validateArgsNumber($nargs)
    {
        if ($nargs < 3) {
            throw new Exception('The number of arguments is at least three!');
        }
    }

    /**
     * Validate the mode for the API Request
     * 
     * @param string $mode {current|forecast}
     * 
     * @return void
     */
    private function validateRequestMode($mode)
    {
        if (!in_array(trim($mode), self::MODES)) {
            throw new Exception('The first argument must be equal to current or forecast');
        } else {
            $this->mode = $mode;
        }
    }

    /**
     * Validate the mode for the API Request
     * 
     * @param string $place {city,CountryCode}
     * 
     * @return void
     */
    private function validateRequestPlace($place)
    {
        if (str_contains($place, ',')) {
            $countries = CountriesArray::get(null, 'alpha2');
            $exp_place = explode(',', $place);
            $country_founded = false;

            foreach ($countries as $country) {
                if (trim($exp_place[1]) === $country) {
                    $country_founded = true;
                }
            }

            if ($country_founded === true) {
                $this->city = $exp_place[0];
                $this->country_code = $exp_place[1];
            } else {
                throw new Exception('Country Code ' . $exp_place[1] . ' not found!');
            }
        } else {
            throw new Exception('The second argument must be a string containig a city, a comma sign and a country code');
        }
    }

    /**
     * Validate the option's format
     * 
     * @param array $command Console command array
     * 
     * @return void
     */
    private function validateOption($option)
    {
        if (str_contains($option, '=')) {
            $tmp_opt = explode('=', $option); // [0 => Option Key, 1 => Value]

            if (in_array($tmp_opt[0], self::ALLOWED_OPTIONS)) {
                if (!empty($tmp_opt[1])) {
                    if (!isset(self::DEFAULT_OPTIONS_ALLOWED_VALUES[$tmp_opt[0]])) {
                        $this->options[$tmp_opt[0]] = $tmp_opt[1];
                    } else {
                        if (in_array($tmp_opt[1], self::DEFAULT_OPTIONS_ALLOWED_VALUES[$tmp_opt[0]])) {
                            $this->options[$tmp_opt[0]] = $tmp_opt[1];
                        } else {
                            throw new Exception('The allowed values for the option ' . $tmp_opt[0] . ' are ' . implode(', ', self::DEFAULT_OPTIONS_ALLOWED_VALUES[$tmp_opt[0]]));
                        }
                    }
                } else {
                    throw new Exception('The option must have a value');
                }
            } else {
                throw new Exception('The allowed options are ' . implode(', ', self::ALLOWED_OPTIONS));
            }
        } else {
            if($option === 'help') {
                throw new Exception('Console based weather viewer' . "\n" . 'php index.php [current|forecast] [city,countryCode] [--days=1|5] [--units=metric|imperial]');
            } else {
                throw new Exception('Option string must have an equal sign between the option and value');
            }
        }
    }

    /**
     * Validate the options of the command
     * 
     * @param array $command Console command array
     * 
     * @return void
     */
    private function validateRequestOptions($command)
    {
        foreach ($command as $c) {
            if (substr($c, 0, 2) === '--') {
                $this->validateOption(ltrim($c, '--'));
            }
        }
    }

    /**
     * Set default undeclared request vars
     * 
     * @return void
     */
    private function setDefaultVars()
    {
        if (is_array(self::MODE_RELATED_OPTIONS[$this->mode])) {
            foreach (self::MODE_RELATED_OPTIONS[$this->mode] as $v) {
                if (!isset($this->options[$v])) {
                    $this->options[$v] = self::DEFAULT_OPTIONS_VALUES[$v];
                }
            }
        } else if (!isset($this->options[self::MODE_RELATED_OPTIONS[$this->mode]])) {
            $this->options[self::MODE_RELATED_OPTIONS[$this->mode]] = self::DEFAULT_OPTIONS_VALUES[self::MODE_RELATED_OPTIONS[$this->mode]];
        }
    }

    /**
     * Unset invalid request vars
     * 
     * @return void
     */
    private function unsetVars()
    {
        if (is_array($this->options)) {
            $keys = array_keys($this->options);

            foreach ($keys as $k) {
                if (is_array(self::MODE_RELATED_OPTIONS[$this->mode])) {
                    if (!in_array($k, self::MODE_RELATED_OPTIONS[$this->mode])) {
                        unset($this->options[$k]);
                    }
                } else {
                    if (self::MODE_RELATED_OPTIONS[$this->mode] !== $k) {
                        unset($this->options[$k]);
                    }
                }
            }
        }
    }

    /**
     * Request vars management
     * 
     * @return void
     */
    private function validateVars()
    {
        $this->unsetVars();
        $this->setDefaultVars();
    }

    /**
     * Validate the console command and arguments
     * 
     * @param array $command Console command array
     * 
     * @return void
     */
    private function validateCommandArgs($command)
    {
        $this->validateRequestOptions($command);
        $this->validateRequestMode($command[1]);
        $this->validateRequestPlace($command[2]);
    }

    /**
     * Validate the console command and arguments
     * 
     * @param int $nargs Number of arguments of the command
     * @param array $command Console command array
     * 
     * @return bool $response
     */
    public function validateCommand($nargs, $command)
    {
        $response = false;

        try {
            $this->validateSAPI();
            $this->validateCommandArgs($command);
            $this->validateArgsNumber($nargs);
            $this->validateVars();

            $response = true;
        } catch (Exception $e) {
            die($e->getMessage());
        }

        return $response;
    }
}
