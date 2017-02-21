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
    class ColumnMacro extends BaseMacro
    {
        /**
         * Pattern which identificates macro first token.
         * @const array PATTERN
         */
        const PATTERN = [
            TokenList::T_COLUMN
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
            if (isset($this->macro[1])) {
                if ($this->macro[1]['token'] == TokenList::T_INTEGER and ($this->macro[1]['value'] > 12 or $this->macro[1]['value'] < 1)) {
                    throw new CompileException("Column macro excepts as column size number between 1 and 12 {$this->macro[1]['value']} given");

                } elseif ($this->macro[1]['token'] == TokenList::T_STRING and !in_array($this->macro[1]['value'], ["xs", "sm", "md", "lg", "xl"])) {
                    throw new CompileException("Column macro excepts as column breakpoint one of values: xs, sm, md, lg, xl. {$this->macro[1]['value']} given");

                }

            }

            if (isset($this->macro[2])) {
                if ($this->macro[2]['token'] == TokenList::T_INTEGER and ($this->macro[2]['value'] > 12 or $this->macro[2]['value'] < 1)) {
                    throw new CompileException("Column macro excepts as column size number between 1 and 12 {$this->macro[2]['value']} given");

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

            $class = [];
            foreach ($this->macro as $value) {
                $class[] = $value['value'];

            }

            $class = "col-" . implode("-", $class);

            return "<div class='{$class}'>";
        }
    }