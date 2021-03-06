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

use Phlamingo\Core\Object;

    /**
     * ConfigValue represents entry in configuration of application.
     */
    class ConfigValue extends Object
    {
        const DEFAULT_PRIORITY = -1;

        /**
         * Name of the entry.
         *
         * @var string
         */
        protected $name;

        /**
         * Value of configuration entry.
         *
         * @var string|mixed
         */
        protected $value;

        /**
         * Default value when value is clear.
         *
         * @var string|mixed
         */
        protected $defaultValue;

        /** Getter for $DefaultValue */
        public function getDefaultValue()
        {
            return $this->defaultValue;
        }

        /**
         * Highest priority set of this value.
         *
         * @var int
         */
        protected $Priority = self::DEFAULT_PRIORITY;

        /**
         * Constructor.
         *
         * @param string       $name         Name of the entry
         * @param string|mixed $value        Value of the entry
         * @param string|mixed $defaultValue Default value when value is clear
         */
        public function __construct(string $name, $value, $defaultValue)
        {
            parent::__construct();
            $this->name = $name;
            $this->value = $value;
            $this->defaultValue = $defaultValue;
        }

        /**
         * Sets the value if priority is higher or equal than last priority set.
         *
         * @param string|mixed $value    Value to set
         * @param int          $priority Priority
         *
         * @return bool If value was changed
         */
        public function changeValue($value, int $priority = 0) : bool
        {
            if ($this->Priority <= $priority) {
                $this->value = $value;

                return true;
            } else {
                return false;
            }
        }

        /**
         * Getter for property Value.
         *
         * @return mixed Value
         */
        public function getValue()
        {
            return $this->value;
        }

        /**
         * Setter for self::$Priority property.
         *
         * @param int $priority Priority to set
         *
         * @throws \InvalidArgumentException When priority is lower than lowest allowed
         */
        public function setPriority(int $priority)
        {
            if ($priority >= self::DEFAULT_PRIORITY) {
                $this->Priority = $priority;
            } else {
                throw new \InvalidArgumentException("Priority {$priority} is invalid for ConfigValue");
            }
        }

        /**
         * Returns value as array in format with keys as path e.g: $array['database']['mysql']['host'] = "localhost".
         *
         * @return array ConfigValue as array
         */
        public function getAsArray()
        {
            $keys = explode('/', $this->name);
            $array = [];
            $code = '$array';

            foreach ($keys as $key) {
                $code .= "['$key']";
            }

            if (is_string($this->value)) {
                $code .= " = '$this->value';";
            } else {
                $code .= " = $this->value;";
            }
            eval($code);

            return $array;
        }
    }
