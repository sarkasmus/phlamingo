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

    namespace Phlamingo\Di;

    class EmptyService
    {
        /**
             * Service name and instance.
             *
             * @var string|object
             */
            protected $_service;

            /**
             * Constructor.
             *
             * @param string $serviceName Service name
             */
            public function __construct(string $serviceName)
            {
                $this->_service = $serviceName;
            }

            /**
             * Checks if service has been instanced and if it has not makes instance of it.
             */
            protected function check()
            {
                if (is_string($this->_service)) {
                    $container = ContainerSingleton::getContainer();
                    $this->_service = $container->get($this->_service);
                }
            }

            /**
             * Glove get method.
             *
             * @param string $name Name of the service's property
             *
             * @return mixed Value of property
             */
            public function __get(string $name)
            {
                $this->check();

                return $this->_service->$name;
            }

            /**
             * Glove set method.
             *
             * @param string $name  Name of the service's property
             * @param mixed  $value Value to set
             */
            public function __set(string $name, $value)
            {
                $this->check();
                $this->_service->$name = $value;
            }

            /**
             * Glove call method.
             *
             * @param string $name      Name of the service's method
             * @param array  $arguments Arguments of method
             *
             * @return mixed Call result
             */
            public function __call(string $name, $arguments)
            {
                $this->check();

                return $this->_service->$name(...$arguments);
            }

            /**
             * Glove isset method.
             *
             * @param string $name Name of the service's property
             *
             * @return bool
             */
            public function __isset($name)
            {
                $this->check();

                return $this->_service->__isset($name);
            }

            /**
             * Glove invoke method.
             *
             * @param array $arguments Arguments of callback
             */
            public function __invoke(...$arguments)
            {
                $this->check();

                return $this->_service->__invoke(...$arguments);
            }
    }
