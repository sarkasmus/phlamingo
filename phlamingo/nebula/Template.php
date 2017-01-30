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

    namespace Phlamingo\Nebula;

    use Phlamingo\Cache\Cache;
    use Phlamingo\Nebula\Compile\Compiler;

    /**
     * Represents the Nebula template.
     */
    class Template
    {
        /**
         * Path of template file.
         * @var string $path
         */
        protected $path;

        /**
         * Code of the template.
         * @var string $code
         */
        protected $code;

        /**
         * Constructor.
         *
         * @param string $path Path of the template file.
         */
        public function __construct(string $path)
        {
            $this->path = $path;
        }

        /**
         * Returns compiled code of the template
         *
         * @return string Code
         */
        public function __toString() : string
        {
            // Replace path characters
            $path = strtr($this->path, ["\\" => "_", "/" => "_", ":" => "_"]);
            // Create cache of template code
            $cache = new Cache("TemplatesCache_{$path}");

            // If cache is defined it returns once compiled code
            if ($cache->IsCacheDefined() === true) {
                return $cache->Content;

            } else { // Compile
                // Find the directory of template for loading other sources
                $path = explode("/", str_replace("\\", "/", $this->path));
                array_pop($path);
                $path = implode("/", $path);

                // Get code of template
                $this->code = file_get_contents($this->path);

                // Build template - load other parts of code
                $compiler = new Compiler($this->code);
                $compiler->build($path);

                // Compile Custom Tags
                $compiler->compileTags();

                // Compile Macros
                $compiler->parseMacros();
                $compiler->compileMacros();

                // Save result to cache
                $cache->Content = $compiler->getCode();
                $cache->Save();

                return  $compiler->getCode();

            }
        }
    }