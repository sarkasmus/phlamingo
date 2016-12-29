<?php

    /**
     * @author Michal Doubek <michal@doubkovi.cz>
     * @license LGPL 3
     *
     * This code is distributed under LGPL license version 3
     * For full license information view LICENSE file which is
     * Distributed with this source code
     *
     * This source code is part of Phlamingo project
     */

    namespace Phlamingo\Config;

    // Exceptions
    use Phlamingo\Config\Exceptions\ConfigException;

    use Phlamingo\Core\Object;

    /**
     * Configurator manages config of application
     */
    class Configurator extends Object implements \IteratorAggregate
    {
        /**
         * List of config entries
         * @var array|ConfigValue $ConfigValues
         */
        protected $ConfigValues = [];

        /**
         * Inserts new config entry
         *
         * @param string $name Name of the entry
         * @param string|mixed $value Value
         * @param string|mixed $defaultValue Default value
         * @throws ConfigException When ConfigValue with name $name already exists
         */
        public function AddConfigValue(string $name, $value, $defaultValue = null)
        {
            if ($defaultValue === null)
            {
                $defaultValue = $value;
            }

            if (!isset($this->ConfigValues[$name]))
            {
                $this->ConfigValues[$name] = new ConfigValue($name, $value, $defaultValue);
            }
            else
            {
                throw new ConfigException("Config value {$name} cannot be added because it already exists");
            }
        }

        /**
         * Sets config value
         *
         * @param string $name Name of the entry
         * @param string|mixed $value Value
         * @param int $priority Priority
         * @throws ConfigException If entry is not defined
         */
        public function SetConfigValue(string $name, $value, int $priority)
        {
            if (isset($this->ConfigValues[$name]))
            {
                $this->ConfigValues[$name]->ChangeValue($value, $priority);
            }
            else
            {
                throw new ConfigException("ConfigValue with name {$name} which your are trying to write to is not defined");
            }
        }

        /**
         * Returns value of ConfigValue with name $name
         *
         * @param string $name Name of the entry
         * @return string|mixed Value of entry
         * @throws ConfigException When entry doesn't exist
         */
        public function GetConfigValue(string $name)
        {
            if (isset($this->ConfigValues[$name]))
            {
                return $this->ConfigValues[$name]->Value;
            }
            else
            {
                $valuesMatches = [];
                foreach ($this->ConfigValues as $key => $value)
                {
                    if (strstr($key, $name, true) !== false and empty(strstr($key, $name, true)))
                    {
                        $valuesMatches[$key] = $this->ConfigValues[$key]->Value;
                    }
                }

                if (!empty($valuesMatches))
                {
                    return $valuesMatches;
                }
                else
                {
                    throw new ConfigException("ConfigValue with name {$name} is not defined");
                }
            }
        }

        /**
         * Clears all config entries, resets priority and sets value for default
         */
        public function ClearAllConfigurations()
        {
            foreach ($this->ConfigValues as $configValue)
            {
                $configValue->Priority = ConfigValue::DEFAULT_PRIORITY;
                $configValue->Value = $configValue->DefaultValue;
            }
        }

        /**
         * Clears config entry with name $name
         *
         * @param string $name Name of entry
         * @throws ConfigException When entry is not defined
         */
        public function ClearValue(string $name)
        {
            if (isset($this->ConfigValues[$name]))
            {
                $this->ConfigValues[$name]->Priority = ConfigValue::DEFAULT_PRIORITY;
                $this->ConfigValues[$name]->Value = $this->ConfigValues[$name]->DefaultValue;
            }
            else
            {
                throw new ConfigException("ConfigValue with name {$name} is not defined");
            }
        }

        /**
         * Resets priority of entry with name $name
         *
         * @param string $name Name of entry
         * @throws ConfigException When entry is not defined
         */
        public function ResetPriorityOfValue(string $name)
        {
            if (isset($this->ConfigValues[$name]))
            {
                $this->ConfigValues[$name]->Priority = ConfigValue::DEFAULT_PRIORITY;
            }
            else
            {
                throw new ConfigException("ConfigValue with name {$name} is not defined");
            }
        }

        /**
         * Resets priority of all entries
         */
        public function ResetPriorities()
        {
            foreach ($this->ConfigValues as $configValue)
            {
                $configValue->Priority = ConfigValue::DEFAULT_PRIORITY;
            }
        }

        /**
         * Returns all entries as array with JSON/YAML structure
         *
         * @return array Entries
         */
        public function PullAll() : array
        {
            return $this->getIterator();
        }

        public function getIterator()
        {
            $return = [];
            foreach ($this->ConfigValues as $key => $value)
            {
                $return[$key] = $value->Value;
            }
            return $return;
        }

        /**
         * Pushes entries from array in format of JSON/YAML
         *
         * @param array $parsedValues Entries
         * @param int $priority Priority to write
         */
        public function Push(array $parsedValues, int $priority)
        {
            $results = [];
            $i = 0;
            while(!empty($parsedValues))
            {
                $i++;
                foreach ($parsedValues as $parsedKey => $parsedValue)
                {
                    if (is_array($parsedValue))
                    {
                        unset($parsedValues[$parsedKey]);
                        foreach ($parsedValue as $valueKey => $value)
                        {
                            $parsedValues[$parsedKey . "/" . $valueKey] = $value;
                        }
                    }

                    if (is_scalar($parsedValue))
                    {
                        unset($parsedValues[$parsedKey]);
                        $results[$parsedKey] = $parsedValue;
                    }
                }
            }

            foreach ($results as $key => $result)
            {
                try
                {
                    $this->SetConfigValue($key, $result, $priority);
                }
                catch (ConfigException $e)
                {
                    $this->AddConfigValue($key, $result);
                }
            }
        }
    }