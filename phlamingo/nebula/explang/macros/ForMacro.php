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
    class ForMacro extends BaseMacro
    {
        /**
         * Pattern which identificates macro first token.
         * @const array PATTERN
         */
        const PATTERN = [
            TokenList::T_FOR,
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
                TokenList::T_FOR,
                TokenList::T_STRING,
                TokenList::T_EQUAL,
                [TokenList::T_INTEGER, TokenList::T_STRING],
                TokenList::T_STRING,
                [
                    TokenList::T_GREATER_THAN,
                    TokenList::T_GREATER_OR_EQUAL_THAN,
                    TokenList::T_SMALLER_THAN,
                    TokenList::T_SMALLER_OR_EQUAL_THAN
                ],
                [TokenList::T_STRING, TokenList::T_INTEGER],
                TokenList::T_STRING,
                [TokenList::T_INCREMENT, TokenList::T_DECREMENT]
            ];

            foreach ($this->macro as $key => $value) {
                if (is_array($pattern[$key])) {
                    foreach ($pattern[$key] as $patternItem) {
                        if ($value['token'] != $patternItem) {
                            $given = TokenList::DICTIONARY[$value['token']];
                            $second = TokenList::DICTIONARY[$this->macro[$key-1]['token']];
                            throw new CompileException("For macro excepts one of: ".print_r($pattern, true)." after {$second}, $given given");

                        }

                    }

                }
                if ($value['token'] != $pattern[$key]) {
                    $given = TokenList::DICTIONARY[$value['token']];
                    $first = TokenList::DICTIONARY[$pattern[$key]];
                    $second = TokenList::DICTIONARY[$this->macro[$key-1]['token']];
                    throw new CompileException("For macro excepts {$first} after {$second}, $given given");

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
            $return = "<?php for (";
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