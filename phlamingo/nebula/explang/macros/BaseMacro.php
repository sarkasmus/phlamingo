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

namespace Phlamingo\Nebula\ExpLang\Macros;

use Phlamingo\Core\Object;
    use Phlamingo\Nebula\ExpLang\Compiler;

    /**
     * Represents Nebula macro.
     */
    abstract class BaseMacro extends Object
    {
        /**
         * Pattern which identificates macro first token.
         *
         * @const array PATTERN
         */
        const PATTERN = [];

        /**
         * List of tokens of macro.
         *
         * @var array
         */
        public $macro = [];

        /**
         * [Unused].
         *
         * @var string
         */
        public $content;

        /**
         * Constructor.
         *
         * @param array       $macroTokenRow
         * @param string|null $content
         */
        public function __construct(array $macroTokenRow, string $content = null)
        {
            parent::__construct();
            $this->macro = $macroTokenRow;
            $this->content = $content;
        }

        /**
         * Checks if syntax of macro is valid.
         *
         * @param Compiler $compiler
         *
         * @throws CompileException When syntax is not valid
         *
         * @return true If syntax is valid
         */
        abstract public function check(Compiler &$compiler);

        /**
         * Compiles macro to native PHP.
         *
         * @param Compiler $compiler
         *
         * @return string Code
         */
        abstract public function compile(Compiler &$compiler) : string;
    }
