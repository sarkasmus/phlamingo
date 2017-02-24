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
    use DocBlockReader\Reader;
    use Phlamingo\Core\Exceptions\MemberAccessException;
    use Phlamingo\Di\Container;
    use Phlamingo\Di\ContainerSingleton;
    use Phlamingo\Di\EmptyService;

    /**
     * Object is parent of almost all classes in the
     * Phlamingo and can be used in other classes in the project
     * It provides dependency injection logic, properties, events etc.
     */
    abstract class Object
    {
        /**
         * Reflection of this class.
         *
         * @var \ReflectionClass
         */
        public $reflection;

        /**
         * Extension methods list.
         *
         * @var array
         */
        protected $methods = [];

        /**
         * Dependency injection container.
         *
         * @var Container
         */
        public $container;

        private static $enviroment = true;

        /**
         * Constructor.
         *
         * IMPORTANT:
         * Default calls setup automatically if constructor is
         * overridden it has to call ThisSetup method manually
         */
        public function __construct()
        {
            if (self::$enviroment === true) {
                $this->ThisSetup();
            }
        }

        /**
         * Setups the inner environment of class.
         *
         * Initiates reflection and container and sets
         * dependencies of class
         */
        protected function ThisSetup()
        {
            // Setup reflection
            $this->reflection = $reflection = new \ReflectionClass($this);

            // Setup di
            $this->container = ContainerSingleton::getContainer();

            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                $propertyName = $property->getName();
                $docReader = new Reader($reflection->getName(), $propertyName, 'property');
                $service = $docReader->getParameter('Service');

                if (!empty($service) and $service != self::class) {
                    $this->$propertyName = new EmptyService($service);
                }
            }
        }

        /**
         * Calls when its trying to get inaccessible property.
         *
         * @param string $name Name of property
         *
         * @throws MemberAccessException When is not defined getter or property doesn't exist
         *
         * @return mixed Value returned by getter
         */
        public function __get(string $name)
        {
            if (isset($this->reflection)) {
                if ($this->reflection->hasProperty($name)) {
                    if ($this->reflection->hasMethod('get'.ucfirst($name))) {
                        return $this->reflection->getMethod('get'.ucfirst($name))->invoke($this);
                    } else {
                        throw new MemberAccessException("Property {$name} has not defined getter in class {".get_class($this).'}');
                    }
                } else {
                    throw new MemberAccessException("Property {$name} does not exist in class {".get_class($this).'}');
                }
            } else {
                throw new MemberAccessException('Reflection is not defined in instance of {'.get_class($this)."} and properties can't be used");
            }
        }

        /**
         * Calls when its trying to set to inaccessible property.
         *
         * @param string $name  Name of property
         * @param mixed  $value Value to set
         *
         * @throws MemberAccessException When is not defined setter or property doesn't exist
         */
        public function __set(string $name, $value)
        {
            // Implements extension methods
            if ($name === 'extend' and is_callable($value)) {
                $this->methods[$name] = $value;
            }

            // Implements setters
            if (isset($this->reflection)) {
                if ($this->reflection->hasProperty($name)) {
                    if ($this->reflection->hasMethod('set'.ucfirst($name))) {
                        $this->reflection->getMethod('set'.ucfirst($name))->invoke($this, $value);
                    } else {
                        throw new MemberAccessException("Property {$name} has not defined setter in class {".get_class($this).'}');
                    }
                } else {
                    throw new MemberAccessException("Property {$name} does not exist in class {".get_class($this).'}');
                }
            } else {
                throw new MemberAccessException('Reflection is not defined in instance of {'.get_class($this)."} and properties can't be used");
            }
        }

        /**
         * Calls when its trying to call inaccessible static method.
         *
         * @param string $name      Name of the method
         * @param array  $arguments Arguments of method
         *
         * @throws MemberAccessException Throws always
         */
        public static function __callStatic(string $name, array $arguments)
        {
            throw new MemberAccessException("Static method {$name} does not exist in class {".self::class.'}');
        }

        /**
         * Calls when its trying to call inaccessible member method.
         *
         * First it checks if the method is defined in extension method list then it
         * tries if the name can be trigger of event and if the event exists
         *
         * @param string $name      Name of the method
         * @param array  $arguments Arguments of method
         *
         * @throws MemberAccessException When method is not defined in class or in
         *                               extension method list and its not trigger of event
         *
         * @return mixed Method return value or null
         */
        public function __call(string $name, array $arguments)
        {
            if (isset($this->methods[$name])) {
                return $this->methods[$name](...$arguments);
            } elseif (isset($this->reflection)) {
                if (substr($name, 0, 2) === 'on' and $this->reflection->hasProperty(substr($name, 2).'Event')) {
                    $event = substr($name, 2).'Event';
                    foreach ($this->$event as $event) {
                        $event();
                    }
                }
            } else {
                throw new MemberAccessException("Member method {$name} does not exist in class {".get_class($this).'}');
            }
        }

        public static function SetEnviroment(bool $callSetup = true)
        {
            self::$enviroment = $callSetup;
        }

        public function Event()
        {
        }

        public function AddEventCallback()
        {
        }
    }
