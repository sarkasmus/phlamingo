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
    class DICacher extends BaseApplicationCacher
    {
        /**
         * Instance of cache with name DICache
         */
        protected $Cache;

        /**
         * Constructor
         */
        public function __construct()
        {
            parent::__construct();
            $this->Cache = new Cache("DICache");
        }

        /**
         * Returns if DI was already cached
         */
        public function Cached() : bool
        {
            return $this->Cache->IsCacheDefined();
        }

        /**
         * Caches all classes with annotations @Factory and @Service
         */
        public function Cache()
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

            $services = [];
            $factories = [];
            foreach ($classes as $class)
            {
                $reader = new Reader($class);
                $service = $reader->getParameter("Service");
                $factory = $reader->getParameter("Factory");

                if (isset($service))
                {
                    $services[$service] = $class;
                }
                elseif (isset($factory))
                {
                    $factories[$factory] = $class;
                }
            }

            $couples = [];
            foreach ($services as $service => $class)
            {
                if ($factory = key_exists($service, $factories) === true)
                {
                    $couples[$service] = $factories[$service];
                }
            }

            $cacheContent = json_encode($couples);
            $this->Cache->Content = $cacheContent;
            $this->Cache->Save();
        }

        /**
         * Returns cached data
         */
        public function Get()
        {
            $content = $this->Cache->Content;
            return json_decode($content);
        }
    }