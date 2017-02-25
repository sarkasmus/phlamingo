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

    namespace Phlamingo\Cache\Storage;

    use Phlamingo\Cache\Cache;
    use Phlamingo\Core\Object;


    /**
     * {Description}
     */
    abstract class BaseStorageManager extends Object
    {
        /**
         * Saves Cache
         *
         * @param Cache $cache Instance of Cache to save
         */
        public abstract function save(Cache $cache);

        /**
         * Clears all Cache files
         */
        public abstract function clearCache();

        /**
         * Returns if Cache is saved in file
         *
         * @param Cache $cache Cache
         * @return bool
         */
        public abstract function isPullable(Cache $cache) : bool;

        /**
         * Pulls content from file
         *
         * @param Cache $cache Cache to pull to
         * @return string Content
         */
        public abstract function pull(Cache $cache) : string;
    }