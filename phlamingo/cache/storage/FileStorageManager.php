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
    use Phlamingo\Cache\Exceptions\CacheException;


    /**
     * {Description}
     */
    class FileStorageManager extends BaseStorageManager
    {
        /**
         * Saves cache
         *
         * @param Cache $cache Instance of cache to save
         */
        public function save(Cache $cache)
        {
            file_put_contents(TEMP . "/cache_" . $cache->Name, $cache->Content);
        }

        /**
         * Clears all cache files
         */
        public function clearCache()
        {
            foreach (glob(TEMP . "/cache_*") as $cacheFile)
            {
                unlink($cacheFile);
            }
        }

        /**
         * Returns if cache is saved in file
         *
         * @param Cache $cache Cache
         * @return bool
         */
        public function isPullable(Cache $cache): bool
        {
            if (file_exists(TEMP . "/cache_" . $cache->Name))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * Pulls content from file
         *
         * @param Cache $cache Cache to pull to
         * @return string Content
         * @throws CacheException When cache is not saved and can't be pulled
         */
        public  function pull(Cache $cache): string
        {
            if ($this->isPullable($cache))
            {
                $content = file_get_contents(TEMP . "/cache_" . $cache->Name);
                return $content;
            }
            else
            {
                throw new CacheException("Cache with name {$cache->Name} is not saved and can't be pulled");
            }
        }
    }