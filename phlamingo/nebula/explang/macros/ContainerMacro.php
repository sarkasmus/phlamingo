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

use Phlamingo\Nebula\Exceptions\CompileException;
    use Phlamingo\Nebula\ExpLang\Compiler;
    use Phlamingo\Nebula\ExpLang\TokenList;

    /**
     * Represents Nebula macro.
     *
     * @Macro
     */
    class ContainerMacro extends BaseMacro
    {
        /**
         * Pattern which identificates macro first token.
         *
         * @const array PATTERN
         */
        const PATTERN = [
            TokenList::T_CONTAINER,
        ];

        /**
         * Checks if syntax of macro is valid.
         *
         * @param Compiler $compiler
         *
         * @throws CompileException When syntax is not valid
         *
         * @return true If syntax is valid
         */
        public function check(Compiler &$compiler)
        {
            if (isset($this->macro[1])) {
                if ($this->macro[1] == 'fluid') {
                    return true;
                } else {
                    $given = TokenList::DICTIONARY[$this->macro[1]['token']];
                    throw new CompileException("Container macro excepts as second parameter 'fluid' modificator {$given} given");
                }
            }

            return true;
        }

        /**
         * Compiles macro to native PHP.
         *
         * @param Compiler $compiler
         *
         * @return string Code
         */
        public function compile(Compiler &$compiler): string
        {
            if (isset($this->macro[1])) {
                return "<div class='container-fluid'>";
            } else {
                return "<div class='container'>";
            }
        }
    }
