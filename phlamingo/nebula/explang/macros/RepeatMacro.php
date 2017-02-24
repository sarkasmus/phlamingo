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
    class RepeatMacro extends BaseMacro
    {
        /**
         * Pattern which identificates macro first token.
         *
         * @const array PATTERN
         */
        const PATTERN = [
            TokenList::T_REPEAT,
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
            if ($this->macro[1]['token'] == TokenList::T_STRING) {
                if ($this->macro[2]['token'] == TokenList::T_ARROW) {
                    if ($this->macro[3]['token'] == TokenList::T_INTEGER or $this->macro[3]['token'] == TokenList::T_STRING) {
                        return true;
                    } else {
                        $given = TokenList::DICTIONARY[$this->macro[3]['token']];
                        throw new CompileException("Repeat macro excepts integer or variable name after arrow operator, $given given");
                    }
                } else {
                    $given = TokenList::DICTIONARY[$this->macro[2]['token']];
                    throw new CompileException("Repeat macro excepts T_ARROR after iterator name, $given given");
                }
            } else {
                $given = TokenList::DICTIONARY[$this->macro[1]['token']];
                throw new CompileException("Repeat macro excepts iterator variable name $given given");
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
            $variable = $this->macro[1]['value'];
            $count = $this->macro[3]['value'];

            return "<?php for (\${$variable}=0;\${$variable}<{$count};\${$variable}++) : ?>";
        }
    }
