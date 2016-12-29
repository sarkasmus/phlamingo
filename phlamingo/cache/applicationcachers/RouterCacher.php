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

    namespace Phlamingo\Cache\ApplicationCachers;

    use DocBlockReader\Reader;
    use Phlamingo\Cache\Cache;


    /**
     * {Description}
     */
    class RouterCacher extends BaseApplicationCacher
    {
        /**
         * Instance of cache with name ConfigCache
         */
        protected $Cache;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->Cache = new Cache("RouterCache");
        }

        /**
         * Returns if config was already cached
         */
        public function Cached() : bool
        {
            return false;//$this->Cache->IsCacheDefined();
        }

        public  function Cache()
        {
            $roots = [PHLAMINGO . "/", APP . "/"];
            $paths = [];
            foreach ($roots as $root)
            {
                $iter = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($root, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST,
                    \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
                );

                foreach ($iter as $path => $dir)
                {
                    if ($dir->isFile() and $dir->getExtension() == "php" and empty(strpos($path, "tests")))
                    {
                        $paths[] = $path;
                    }
                }
            }

            foreach ($paths as $path)
            {
                require_once($path);
            }

            $classes = get_declared_classes();

            $routes = [];
            foreach ($classes as $class)
            {
                $reflection = new \ReflectionClass($class);
                $methods = $reflection->getMethods();

                foreach ($methods as $method)
                {
                    if ($method->isPublic())
                    {
                        $reader = new Reader($class, $method->getName(), "method");
                        $route = $reader->getParameter("Route");

                        if (isset($route))
                        {
                            $routes[$route] = ["controller" => $class, "action" => $method->getName()];
                        }
                    }
                }
            }

            $this->Cache->Content = json_encode($routes);
            $this->Cache->Save();
        }

        /**
         * Returns cached data
         */
        public function Get()
        {
            $content = $this->Cache->Content;
            return json_decode($content, true);
        }
    }