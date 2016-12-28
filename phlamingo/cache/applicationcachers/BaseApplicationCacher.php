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

    use Phlamingo\Core\Object;


    /**
     * {Description}
     */
    abstract class BaseApplicationCacher extends Object
    {
        /**
         * Caches all classes with annotations @Factory and @Service
         */
        public abstract function Cache();

        /**
         * Returns if DI was already cached
         */
        public abstract function Cached() : bool;

        /**
         * Returns cached data
         */
        public abstract function Get();
    }