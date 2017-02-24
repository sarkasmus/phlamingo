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

namespace Phlamingo\Nebula\ExtensibleTags;

use Phlamingo\Core\Object;

    /**
     * Compiles custom tags to html.
     */
    class Compiler extends Object
    {
        /**
         * List of registered custom tags.
         *
         * @var array
         */
        protected static $tagList = [];

        /**
         * Compiles code with custom tags to HTML.
         *
         * @param string $code Code for compile.
         *
         * @return string Result code
         */
        public function compile(string $code)
        {
            foreach (self::$tagList as $tagName => $translate) {
                $code = preg_replace("/<{$tagName}(?!\w)/i", '<'.$translate, $code);
                $code = preg_replace("/<\/{$tagName}(?!\w)/i", '</'.$translate, $code);
            }

            return $code;
        }

        const BLOCK_DISPLAY = 'div';
        const INLINE_DISPLAY = 'span';

        /**
         * Registers custom tag to compiler.
         *
         * @param string $name    Name of the tag
         * @param string $display Block or Inline display of tag
         */
        public static function registerCustomTag(string $name, string $display)
        {
            self::$tagList['n:'.$name] = $display.' '."data-n-class='n-{$name}'";
        }
    }
