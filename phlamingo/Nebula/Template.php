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
         *
         * @var string
         */
        protected $path;

        /**
         * Code of the template.
         *
         * @var string
         */
        protected $code;

        protected $variables = [];

        /**
         * Constructor.
         *
         * @param string $path      Path of the template file.
         * @param array  $variables Variables added into template
         */
        public function __construct(string $path, array $variables = [])
        {
            $this->path = $path;
            $this->variables = $variables;
        }

        /**
         * Returns compiled code of the template.
         *
         * @return string Code
         */
        public function __toString() : string
        {
            // Replace path characters
            $path = strtr($this->path, ['\\' => '_', '/' => '_', ':' => '_']);
            // Create Cache of template code
            $cache = new Cache("TemplatesCache_{$path}");

            // If Cache is defined it returns once compiled code
            if ($cache->isCacheDefined() === true) {
                extract($this->variables);
                eval('?>'.$cache->Content.'<?php');
                $returned = ob_get_contents();
                ob_end_clean();

                return $returned;
            } else { // Compile
                // Find the directory of template for loading other sources
                $path = explode('/', str_replace('\\', '/', $this->path));
                array_pop($path);
                $path = implode('/', $path);

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

                // Save result to Cache
                $cache->Content = $compiler->getCode();
                $cache->save();

                // Run template
                extract($this->variables);
                eval('?>'.$compiler->getCode().'<?php');
                $returned = ob_get_contents();
                ob_end_clean();
                eval('?>'.$returned.'<?php');
                $returned = ob_get_contents();

                return $returned;
            }
        }
    }
