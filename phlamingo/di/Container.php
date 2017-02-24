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

use Phlamingo\Di\Exceptions\DIContainerException;

    /**
     * Container is non-static wrapper of StaticContainer for using in classes.
     */
    class Container
    {
        /**
         * List of service factories -> callables (FactoryAbstract, callback).
         *
         * @var array
         */
        private $services = [];

        /**
         * Returns a service.
         *
         * @param string $service Name of service
         *
         * @throws DIContainerException
         *
         * @return mixed Service
         */
        public function get(string $service)
        {
            if (isset($this->services[$service])) {
                return $this->services[$service]($this);
            } else {
                throw new DIContainerException("dsadsadsa $service");
            }
        }

        /**
         * Register a service to container.
         *
         * @param string   $name    Service name
         * @param callable $factory Service factory
         *
         * @throws DIContainerException
         */
        public function addService(string $name, callable $factory)
        {
            if (isset($this->services[$name])) {
                throw new DIContainerException('sadsaddsasda');
            }

            if (!is_callable($factory)) {
                throw new DIContainerException('sdadasdsadsa');
            }

            if ($factory instanceof BaseFactory) {
                $factory->container = $this;
            }

            $this->services[$name] = $factory;
        }

        /**
         * Register an alias for the service.
         *
         * @param string $service Affected service
         * @param string $alias   Alias
         *
         * @throws DIContainerException
         */
        public function addAlias(string $service, string $alias)
        {
            if (!isset($this->services[$alias])) {
                if (isset($this->services[$service])) {
                    $this->services[$alias] = $this->services[$service];
                } else {
                    throw new DIContainerException('');
                }
            } else {
                throw new DIContainerException('');
            }
        }
    }
