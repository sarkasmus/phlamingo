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
     * {Description}.
     */
    abstract class BaseApplicationCacher extends Object
    {
        public function __construct()
        {
            parent::__construct();
        }

        /**
         * Caches all classes with annotations @Factory and @Service.
         */
        abstract public function Cache();

        /**
         * Returns if DI was already cached.
         */
        abstract public function Cached() : bool;

        /**
         * Returns cached data.
         */
        abstract public function Get();
    }
