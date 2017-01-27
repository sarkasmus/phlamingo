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

    namespace Phlamingo\Nebula\Compile;

    use Phlamingo\Core\Object;

    /**
     * Loads other sources into code
     */
    class Preprocessor extends Object
    {
        /**
         * Loads other sources into code
         *
         * @param string $code Code
         * @param string $path Path of directory to find other sources
         * @return string Code with loaded sources
         */
        public function preprocess(string $code, string $path)
        {
            $callback = function($matches) use (&$path)
            {
                $match = ltrim($matches[0], "@{extend ");
                $match = rtrim($match, "}");
                return $this->load($path . "/" . $match . ".neb");
            };

            $macroRegex = '/@\{extend [a-zA-Z1-9._]+\}/';

            while (preg_match($macroRegex, $code)) {
                $code = preg_replace_callback($macroRegex, $callback, $code);

            }

            return $code;
        }

        /**
         * Loads the current file and returns it's code
         *
         * @param string $path Path of file
         * @return string Code
         */
        public function load(string $path)
        {
            return file_get_contents($path);
        }
    }