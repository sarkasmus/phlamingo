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
    class WhileMacro extends BaseMacro
    {
        /**
         * Pattern which identificates macro first token.
         *
         * @const array PATTERN
         */
        const PATTERN = [
            TokenList::T_WHILE,
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
            $macro = $this->macro;
            unset($macro[0]);

            $allowedTokens = [
                TokenList::T_STRING,
                TokenList::T_ADD,
                TokenList::T_AND,
                TokenList::T_OR,
                TokenList::T_GREATER_OR_EQUAL_THAN,
                TokenList::T_GREATER_THAN,
                TokenList::T_SMALLER_OR_EQUAL_THAN,
                TokenList::T_SMALLER_THAN,
                TokenList::T_INTEGER,
                TokenList::T_IS_EQUAL,
                TokenList::T_IS_SAME,
                TokenList::T_NOT_EQUAL,
                TokenList::T_NOT_SAME,
                TokenList::T_MULTIPLY,
                TokenList::T_DIVIDE,
                TokenList::T_SUBSTRACT,
                TokenList::T_BOOL,
                TokenList::T_DECREMENT,
                TokenList::T_INCREMENT,
                TokenList::T_EQUAL,
                TokenList::T_NEGATE,
                TokenList::T_MODULO,
                TokenList::T_LEFT_BRACKET,
                TokenList::T_RIGHT_BRACKET,
            ];

            foreach ($macro as $token) {
                if (!in_array($token['token'], $allowedTokens)) {
                    throw new CompileException('While macro: The logic expression does contain bad tokens - logic expression is invalid in while macro');
                }
            }
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
            unset($this->macro[0]);
            $return = '<?php while (';
            foreach ($this->macro as $key => $value) {
                if ($value['token'] == TokenList::T_STRING) {
                    $return .= '$'.$value['value'].' ';
                } else {
                    $return .= $value['value'].' ';
                }
            }
            $return .= ') : ?>';

            return $return;
        }
    }
