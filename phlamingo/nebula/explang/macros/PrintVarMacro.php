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

    use Phlamingo\Nebula\ExpLang\Compiler;
    use Phlamingo\Nebula\ExpLang\TokenList;

    /**
     * Represents Nebula macro.
     */
    class PrintVarMacro extends BaseMacro
    {
        /**
         * Pattern which identificates macro first token.
         * @const array PATTERN
         */
        const PATTERN = [
            TokenList::T_STRING
        ];

        /**
         * Compiles macro to native PHP
         *
         * @param Compiler $compiler
         * @return string Code
         */
        public  function compile(Compiler &$compiler): string
        {
            if ($compiler->hasVariable($this->macro[0]['value'])) {
                return $compiler->getVariable($this->macro[0]['value']);

            }

            $this->macro[0]['value'] = strtr($this->macro[0]['value'], ["." => "->"]);

            if (isset($this->macro[1]['token'])) {
                if ($this->macro[1]['token'] == TokenList::T_NOESCAPE) {
                    return "<?= \${$this->macro[0]['value']}; ?>";

                }

            }

            return "<?= htmlspecialchars(\${$this->macro[0]['value']});?>";
        }
    }