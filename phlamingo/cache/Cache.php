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

    namespace Phlamingo\Cache;

    use Doctrine\Instantiator\Exception\InvalidArgumentException;
    use Phlamingo\Cache\Exceptions\CacheException;
    use Phlamingo\Cache\Storage\BaseStorageManager;
    use Phlamingo\Cache\Storage\FileStorageManager;
    use Phlamingo\Core\Object;


    /**
     * {Description}
     */
    class Cache extends Object
    {
        /**
         * Name of the cache
         * @var string $Name
         */
        protected $Name;

        /**
         * Content of the cache
         * @var string $Content
         */
        protected $Content;

        /**
         * Is cache saved. If it is content can't be changed
         * @var bool $Saved
         */
        protected $Saved = false;

        /**
         * Storage manager dependency
         * @var BaseStorageManager $StorageManager
         */
        protected $StorageManager;

        /**
         * Constructor
         *
         * @param string $name Name of the cache
         * @param string $content Content of the cache
         * @param BaseStorageManager $storageManager Storage manager
         */
        public function __construct(string $name, string $content = "", BaseStorageManager $storageManager = null)
        {
            parent::__construct();
            if ($storageManager === null)
            {
                $storageManager = new FileStorageManager();
            }
            $this->StorageManager = $storageManager;

            if (empty($name))
            {
                throw new InvalidArgumentException("Cache name can't be empty");
            }

            $this->Name = $name;
            $this->Content = $content;

            $this->Pull();
        }

        /**
         * Pulls saved content from storage manager to cache
         */
        public function Pull()
        {
            if ($this->StorageManager->IsPullable($this))
            {
                $this->Content = $this->StorageManager->Pull($this);
            }
        }

        /**
         * Returns if cache has saved contrent in file
         *
         * @return bool
         */
        public function IsCacheDefined() : bool
        {
            return $this->StorageManager->IsPullable($this);
        }

        /**
         * Saves content to file by storage manager
         *
         * @throws CacheException When content is empty
         */
        public function Save()
        {
            if (!empty($this->Content))
            {
                $this->StorageManager->Save($this);
                $this->Saved = true;
            }
            else
            {
                throw new CacheException("Content of cache {$this->Name} is empty. Cache can't be saved");
            }
        }

        /**
         * Clears all cache files
         */
        public static function ClearCache()
        {
            $storageManager = new FileStorageManager();
            $storageManager->ClearCache();
        }

        /**
         * Getter for Name property
         *
         * @return string
         */
        public function getName()
        {
            return $this->Name;
        }

        /**
         * Getter for Content property
         *
         * @return string
         */
        public function getContent()
        {
            return $this->Content;
        }

        /**
         * Setter for Content property
         *
         * @param string $content Content to set
         * @throws CacheException When cache was already saved and can't be changed
         */
        public function setContent(string $content)
        {
            if ($this->Saved !== true)
            {
                $this->Content = $content;
            }
            else
            {
                throw new CacheException("Cache have been saved and can't be edited. Create new cache to overwrite current");
            }
        }

        /**
         * Getter for storage manager
         *
         * @return BaseStorageManager Storage manager
         */
        public function getStorageManager() : BaseStorageManager
        {
            return $this->StorageManager;
        }

        /**
         * Setter for storage manager
         *
         * @param BaseStorageManager $storageManager
         */
        public function setStorageManager(BaseStorageManager $storageManager)
        {
            $this->StorageManager = $storageManager;
        }
    }