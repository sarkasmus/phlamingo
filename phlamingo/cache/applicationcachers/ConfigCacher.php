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
    use Symfony\Component\Yaml\Yaml;
    use Phlamingo\Cache\Cache;

    /**
     * {Description}
     */
    class ConfigCacher extends Object
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
            $this->Cache = new Cache("ConfigCache");
        }

        /**
         * Returns if config was already cached
         */
        public function Cached() : bool
        {
            return false;//$this->Cache->IsCacheDefined();
        }

        public function Cache()
        {
            // TODO IMPLEMENT OTHER EXTENSIONS (yml)
            $defaultConfig = Yaml::parse(file_get_contents(PHLAMINGO . "/config.yaml"));
            $appConfig = Yaml::parse(file_get_contents(APP . "/main/appconfig.yaml"));

            $json = json_encode([$defaultConfig, $appConfig]);
            $this->Cache->Content = $json;
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