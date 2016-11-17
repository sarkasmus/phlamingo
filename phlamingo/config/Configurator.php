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
    use Phlamingo\Config\Exceptions\ConfigExceptions;

    use Phlamingo\Core\Object;

    /**
     * Configurator manages config of application
     */
    class Configurator extends Object
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
         * @throws ConfigExceptions When ConfigValue with name $name already exists
         */
        public function AddConfigValue(string $name, $value, $defaultValue)
        {
            if (!isset($this->ConfigValues[$name]))
            {
                $this->ConfigValues[$name] = new ConfigValue($name, $value, $defaultValue);
            }
            else
            {
                throw new ConfigExceptions("Config value {$name} cannot be added because it already exists");
            }
        }

        /**
         * Sets config value
         *
         * @param string $name Name of the entry
         * @param string|mixed $value Value
         * @param int $priority Priority
         * @throws ConfigExceptions If entry is not defined
         */
        public function SetConfigValue(string $name, $value, int $priority)
        {
            if (isset($this->ConfigValues[$name]))
            {
                $this->ConfigValues[$name]->ChangeValue($value, $priority);
            }
            else
            {
                throw new ConfigExceptions("ConfigValue with name {$name} which your are trying to write to is not defined");
            }
        }

        /**
         * Returns value of ConfigValue with name $name
         *
         * @param string $name Name of the entry
         * @return string|mixed Value of entry
         * @throws ConfigExceptions When entry doesn't exist
         */
        public function GetConfigValue(string $name)
        {
            if (isset($this->ConfigValues[$name]))
            {
                return $this->ConfigValues[$name]->Value;
            }
            else
            {
                throw new ConfigExceptions("ConfigValue with name {$name} is not defined");
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
         * @throws ConfigExceptions When entry is not defined
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
                throw new ConfigExceptions("ConfigValue with name {$name} is not defined");
            }
        }

        /**
         * Resets priority of entry with name $name
         *
         * @param string $name Name of entry
         * @throws ConfigExceptions When entry is not defined
         */
        public function ResetPriorityOfValue(string $name)
        {
            if (isset($this->ConfigValues[$name]))
            {
                $this->ConfigValues[$name]->Priority = ConfigValue::DEFAULT_PRIORITY;
            }
            else
            {
                throw new ConfigExceptions("ConfigValue with name {$name} is not defined");
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
            $return = [];
            foreach ($this->ConfigValues as $configValue)
            {
                array_merge_recursive($return, $configValue->GetAsArray());
            }

            return $return;
        }

        /**
         * Moves cursor to next entry
         *
         * @return bool True if entry is not last False if current entry is last
         */
        public function Pull() : bool
        {
            $i = $this->Current[1];
            if ($i <= count($this->ConfigValues))
            {
                $this->Current = [array_slice($this->ConfigValues, $i, $i), $i + 1];
                return true;
            }

            $this->Current = [null, 1];
            return false;
        }

        /**
         * Current entry cursor
         * @var array $Current
         */
        protected $Current = [null, 1];

        /**
         * Returns current entry value
         *
         * @return string|mixed Value of current entry
         * @throws ConfigExceptions When current is not loaded by Pull()
         */
        public function Current()
        {
            if (isset($this->Current[0]))
            {
                return $this->Current[0][0]->Value;
            }
            else
            {
                throw new ConfigExceptions("Can't return current because current is not loaded by Pull() yet");
            }
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
            while(!empty($parsedValues))
            {
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

                    if (is_string($parsedValue))
                    {
                        $results[$parsedKey] = $parsedValue;
                    }
                }
            }

            foreach ($results as $key => $result)
            {
                $this->SetConfigValue($key, $result, $priority);
            }
        }
    }