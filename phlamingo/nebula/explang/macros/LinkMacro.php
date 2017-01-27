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

    use Phlamingo\Core\MVC\Router;
    use Phlamingo\Nebula\ExpLang\Compiler;
    use Phlamingo\Nebula\ExpLang\TokenList;

    /**
     * Represents Nebula macro.
     */
    class LinkMacro extends BaseMacro
    {
        /**
         * Router service
         * @Service Router
         * @var Router
         */
        public $router;

        /**
         * Pattern which identificates macro first token.
         * @const array PATTERN
         */
        const PATTERN = [
            TokenList::T_LINK
        ];

        /**
         * Compiles macro to native PHP
         *
         * @param Compiler $compiler
         * @return string Code
         */
        public  function compile(Compiler &$compiler): string
        {
            unset($this->macro[0]);
            $event = explode(".", array_shift($this->macro)['value']);
            $params = [];

            foreach ($this->macro as $macro) {
                $params[] = $macro['value'];

            }

            $event = ["controller" => $event[0], "action" => $event[1]];

            return $this->router->GenerateURL($event, ...$params);
        }
    }