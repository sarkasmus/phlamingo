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

    namespace Phlamingo\Core;

    // Exceptions
    use Phlamingo\Core\Exceptions\MemberAccessException;

    use DocBlockReader\Reader;
    use Phlamingo\Di\Container;


    /**
     * Object is parent of almost all classes in the
     * Phlamingo and can be used in other classes in the project
     * It provides dependency injection logic, properties, events etc
     */
    abstract class Object
    {
        /**
         * Reflection of this class
         * @var \ReflectionClass $Reflection
         */
        public $Reflection;

        /**
         * Extension methods list
         * @var array $methods
         */
        protected $methods = [];

        /**
         * Session service
         * @Service session
         */
        public $Session;

        /**
         * Dependency injection container
         * @var Container $Container
         */
        public $Container;

        /**
         * Constructor
         *
         * IMPORTANT:
         * Default calls setup automatically if constructor is
         * overridden it has to call ThisSetup method manually
         */
        public function __construct()
        {
            $this->ThisSetup();
        }

        /**
         * Setups the inner environment of class
         *
         * Initiates reflection and container and sets
         * dependencies of class
         */
        protected final function ThisSetup()
        {
            // Setup reflection
            $this->Reflection = $reflection = new \ReflectionClass($this);

            // Setup di
            $this->Container = new Container();

            $properties = $reflection->getProperties();
            foreach ($properties as $property)
            {
                $propertyName = $property->getName();
                $docReader = new Reader($reflection->getName(), $propertyName, "property");
                $service = $docReader->getParameter("Service");

                if (!empty($doc))
                {
                    $this->$propertyName = $this->Container->Get($service);
                }
            }
        }

        /**
         * Calls when its trying to get inaccessible property
         *
         * @param string $name Name of property
         * @return mixed Value returned by getter
         * @throws MemberAccessException When is not defined getter or property doesn't exist
         */
        public function __get(string $name)
        {
            if ($this->Reflection->hasProperty($name))
            {
                if ($this->Reflection->hasMethod("get" . ucfirst($name)))
                {
                    return $this->Reflection->getMethod("get" . ucfirst($name))->invoke($this);
                }
                else
                {
                    throw new MemberAccessException("Property {$name} has not defined getter in class {" . get_class($this) . "}");
                }
            }
            else
            {
                throw new MemberAccessException("Property {$name} does not exist in class {" . get_class($this) . "}");
            }
        }

        /**
         * Calls when its trying to set to inaccessible property
         *
         * @param string $name Name of property
         * @param mixed $value Value to set
         * @throws MemberAccessException When is not defined setter or property doesn't exist
         */
        public function __set(string $name, $value)
        {
            // Implements extension methods
            if ($name === "extend" and is_callable($value))
            {
                $this->methods[$name] = $value;
            }

            // Implements setters
            if ($this->Reflection->hasProperty($name))
            {
                if ($this->Reflection->hasMethod("set" . ucfirst($name)))
                {
                    $this->Reflection->getMethod("set" . ucfirst($name))->invoke($this, $value);
                }
                else
                {
                    throw new MemberAccessException("Property {$name} has not defined setter in class {" . get_class($this) . "}");
                }
            }
            else
            {
                throw new MemberAccessException("Property {$name} does not exist in class {" . get_class($this) . "}");
            }
        }

        /**
         * Calls when its trying to call inaccessible static method
         *
         * @param string $name Name of the method
         * @param array $arguments Arguments of method
         * @throws MemberAccessException Throws always
         */
        public static function __callStatic(string $name, array $arguments)
        {
            throw new MemberAccessException("Static method {$name} does not exist in class {" . self::class . "}");
        }

        /**
         * Calls when its trying to call inaccessible member method
         *
         * First it checks if the method is defined in extension method list then it
         * tries if the name can be trigger of event and if the event exists
         *
         * @param string $name Name of the method
         * @param array $arguments Arguments of method
         * @return mixed Method return value or null
         * @throws MemberAccessException When method is not defined in class or in
         * extension method list and its not trigger of event
         */
        public function __call(string $name, array $arguments)
        {
            if (isset($this->methods[$name]))
            {
                return $this->methods[$name](...$arguments);
            }
            elseif (substr($name, 0, 2) === "on" and $this->Reflection->hasProperty(substr($name, 2) . "Event"))
            {
                $event = substr($name, 2) . "Event";
                foreach ($this->$event as $event)
                {
                    $event();
                }
            }
            else
            {
                throw new MemberAccessException("Member method {$name} does not exist in class {" . get_class($this) . "}");
            }

            return null;
        }
    }