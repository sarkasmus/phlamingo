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
     * Container is non-static wrapper of StaticContainer for using in classes
     */
    class Container
    {
        /**
         * List of service factories -> callables (FactoryAbstract, callback)
         * @var array $services
         */
        private $Services = [];

        public $Singletons = [];

        /**
         * Returns a service
         *
         * @param string $service Name of service
         * @return mixed Service
         * @throws DIContainerException
         */
        public function Get(string $service)
        {
            if (isset($this->Services[$service]))
            {
                return $this->Services[$service]($this);
            }
            else
            {
                throw new DIContainerException("$service");
            }
        }

        /**
         * Register a service to container
         *
         * @param string $name Service name
         * @param callable $factory Service factory
         * @throws DIContainerException
         */
        public function AddService(string $name, callable $factory)
        {
            if (isset($this->Services[$name]))
            {
                throw new DIContainerException("sadsaddsasda");
            }

            if (!is_callable($factory))
            {
                throw new DIContainerException("sdadasdsadsa");
            }

            $this->Services[$name] = $factory;
        }

        /**
         * Register an alias for the service
         *
         * @param string $service Affected service
         * @param string $alias Alias
         * @throws DIContainerException
         */
        public function AddAlias(string $service, string $alias)
        {
            if (!isset($this->Services[$alias]))
            {
                if (isset($this->Services[$service]))
                {
                    $this->Services[$alias] = $this->Services[$service];
                }
                else
                {
                    throw new DIContainerException("");
                }
            }
            else
            {
                throw new DIContainerException("");
            }
        }
    }