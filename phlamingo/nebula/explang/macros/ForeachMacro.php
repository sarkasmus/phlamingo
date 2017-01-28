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
     */
    class ForeachMacro extends BaseMacro
    {
        /**
         * Pattern which identificates macro first token.
         * @const array PATTERN
         */
        const PATTERN = [
            TokenList::T_FOREACH,
        ];

        /**
         * Checks if syntax of macro is valid
         *
         * @param Compiler $compiler
         * @throws CompileException When syntax is not valid
         * @return true If syntax is valid
         */
        public  function check(Compiler &$compiler)
        {
            $pattern = [
                TokenList::T_FOREACH,
                TokenList::T_STRING,
                TokenList::T_AS,
                TokenList::T_STRING,
                TokenList::T_ARROW,
                TokenList::T_STRING
            ];

            foreach ($this->macro as $key => $value) {
                if ($value['token'] != $pattern[$key]) {
                    $given = TokenList::DICTIONARY[$value['token']];
                    $first = TokenList::DICTIONARY[$pattern[$key]];
                    $second = TokenList::DICTIONARY[$pattern[$key-1]];
                    throw new CompileException("Foreach macro excepts {$first} after {$second}, $given given");

                }

            }

            return true;
        }

        /**
         * Compiles macro to native PHP
         *
         * @param Compiler $compiler
         * @return string Code
         */
        public  function compile(Compiler &$compiler): string
        {
            unset($this->macro[0]);
            $return = "<?php foreach (";
            foreach ($this->macro as $key => $value) {
                if ($value['token'] == TokenList::T_STRING) {
                    $return .= "\$" . $value['value'] . " ";

                } else {
                    $return .= $value['value'] . " ";

                }

            }
            $return .= ") : ?>";

            return $return;
        }
    }