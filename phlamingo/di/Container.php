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

    use Phlamingo\Core\Object;


    /**
     * Container is non-static wrapper of StaticContainer for using in classes
     */
    class Container
    {
        /**
         * Returns a service
         *
         * @param string $service Name of service
         * @return mixed Service
         */
        public function Get(string $service)
        {
            return StaticContainer::$service();
        }

        /**
         * Register a service to container
         *
         * @param string $name Service name
         * @param callable $factory Service factory
         */
        public function AddService(string $name, callable $factory)
        {
            StaticContainer::AddService($name, $factory);
        }

        /**
         * Register an alias for the service
         *
         * @param string $service Affected service
         * @param string $alias Alias
         */
        public function AddAlias(string $service, string $alias)
        {
            StaticContainer::AddAlias($service, $alias);
        }
    }