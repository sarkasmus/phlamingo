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

namespace Phlamingo\Nebula\ExpLang\Cache;

use DocBlockReader\Reader;
    use Phlamingo\Cache\Cache;
    use Phlamingo\Core\Object;

    /**
     * {Description}.
     *
     * @Service MacroCacher
     */
    class MacroCacher extends Object
    {
        /**
         * Instance of cache with name MacroCache.
         */
        protected $Cache;

        /**
         * Constructor.
         */
        public function __construct()
        {
            parent::__construct();
            $this->Cache = new Cache('MacroCache');
        }

        /**
         * Returns if DI was already cached.
         */
        public function Cached() : bool
        {
            return $this->Cache->IsCacheDefined();
        }

        /**
         * Caches all classes with annotations @Factory and @Service.
         */
        public function Cache()
        {
            $roots = [PHLAMINGO.'/', APP.'/'];
            $paths = [];
            foreach ($roots as $root) {
                $iter = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($root, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST,
                    \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
                );

                foreach ($iter as $path => $dir) {
                    if ($dir->isFile() and $dir->getExtension() == 'php' and empty(strpos($path, 'tests'))) {
                        $paths[] = $path;
                    }
                }
            }

            foreach ($paths as $path) {
                require_once $path;
            }

            $classes = get_declared_classes();

            $macros = [];
            foreach ($classes as $class) {
                $reader = new Reader($class);
                $macro = $reader->getParameter('Macro');

                if (isset($macro)) {
                    $macros[] = $class;
                }
            }

            $cacheContent = json_encode($macros);
            $this->Cache->Content = $cacheContent;
            $this->Cache->Save();
        }

        /**
         * Returns cached data.
         */
        public function Get()
        {
            $content = $this->Cache->Content;

            return json_decode($content, true);
        }
    }
