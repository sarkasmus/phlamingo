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

    // Exceptions
    use Phlamingo\Di\Exceptions\DIContainerException;


    /**
     * StaticContainer keeps list of services with their factories
     */
    class StaticContainer
    {
        /**
         * List of service factories -> callables (FactoryAbstract, callback)
         * @var array $services
         */
        private static $services = [];

        /**
         * Returns service (service name is name of the called inaccessible method)
         * e.g: StaticContainer::session(); returns session service
         *
         * @param string $name Name of the service
         * @param array $arguments Method arguments (unused)
         * @return mixed Service
         * @throws DIContainerException When service doesn't exist
         */
        public static function __callStatic(string $name, array $arguments)
        {
            if (!empty(self::$services[$name]) and is_callable(self::$services[$name]))
            {
                return self::$services[$name]();
            }
            else
            {
                throw new DIContainerException("Service {$name} doesn't exist in the container");
            }
        }

        /**
         * Adds service to service list
         *
         * @param string $name Name of the service
         * @param callable $callback Factory of the service
         */
        public static function AddService(string $name, callable $callback)
        {
            self::$services[$name] = $callback;
        }

        /**
         * Adds alias to service list
         *
         * @param string $service Affected service
         * @param string $alias Alias
         * @throws DIContainerException When service doesn't exists or when alias
         * name is reserved by alias or another service
         */
        public static function AddAlias(string $service, string $alias)
        {
            if (!isset(self::$services[$alias]))
            {
                if (!empty(self::$services[$service]))
                {
                    self::$services[$alias] = self::$services[$service];
                }
                else
                {
                    throw new DIContainerException("Service {$service} doesn't exists");
                }
            }
            else
            {
                throw new DIContainerException("Alias name {$alias} of service {$service} is invalid");
            }
        }
    }