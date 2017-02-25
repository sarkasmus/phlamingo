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
     * {Description}.
     */
    class Cache extends Object
    {
        /**
         * Name of the Cache.
         *
         * @var string
         */
        protected $Name;

        /**
         * Content of the Cache.
         *
         * @var string
         */
        protected $Content;

        /**
         * Is Cache saved. If it is, content can't be changed.
         *
         * @var bool
         */
        protected $Saved = false;

        /**
         * Storage manager dependency.
         *
         * @var BaseStorageManager
         */
        protected $StorageManager;

        /**
         * Constructor.
         *
         * @param string             $name           Name of the Cache
         * @param string             $content        Content of the Cache
         * @param BaseStorageManager $storageManager Storage manager
         */
        public function __construct(string $name, string $content = '', BaseStorageManager $storageManager = null)
        {
            parent::__construct();
            if ($storageManager === null) {
                $storageManager = new FileStorageManager();
            }
            $this->StorageManager = $storageManager;

            if (empty($name)) {
                throw new InvalidArgumentException("Cache name can't be empty");
            }

            $this->Name = $name;
            $this->Content = $content;

            $this->pull();
        }

        /**
         * Pulls saved content from Storage manager to Cache.
         */
        public function pull()
        {
            if ($this->StorageManager->IsPullable($this)) {
                $this->Content = $this->StorageManager->Pull($this);
            }
        }

        /**
         * Returns if Cache has saved contrent in file.
         *
         * @return bool
         */
        public function isCacheDefined() : bool
        {
            return $this->StorageManager->IsPullable($this);
        }

        /**
         * Saves content to file by Storage manager.
         *
         * @throws CacheException When content is empty
         */
        public function save()
        {
            if (!empty($this->Content)) {
                $this->StorageManager->Save($this);
                $this->Saved = true;
            } else {
                throw new CacheException("Content of Cache {$this->Name} is empty. Cache can't be saved");
            }
        }

        /**
         * Clears all Cache files.
         */
        public static function clearCache()
        {
            $storageManager = new FileStorageManager();
            $storageManager->clearCache();
        }

        /**
         * Getter for Name property.
         *
         * @return string
         */
        public function getName()
        {
            return $this->Name;
        }

        /**
         * Getter for Content property.
         *
         * @return string
         */
        public function getContent()
        {
            return $this->Content;
        }

        /**
         * Setter for Content property.
         *
         * @param string $content Content to set
         *
         * @throws CacheException When Cache was already saved and can't be changed
         */
        public function setContent(string $content)
        {
            if ($this->Saved !== true) {
                $this->Content = $content;
            } else {
                throw new CacheException("Cache have been saved and can't be edited. Create new Cache to overwrite current");
            }
        }

        /**
         * Getter for Storage manager.
         *
         * @return BaseStorageManager Storage manager
         */
        public function getStorageManager() : BaseStorageManager
        {
            return $this->StorageManager;
        }

        /**
         * Setter for Storage manager.
         *
         * @param BaseStorageManager $storageManager
         */
        public function setStorageManager(BaseStorageManager $storageManager)
        {
            $this->StorageManager = $storageManager;
        }
    }
