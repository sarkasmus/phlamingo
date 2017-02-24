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
    use Phlamingo\Nebula\Exceptions\CompileException;
    use Phlamingo\Nebula\ExpLang\Compiler;
    use Phlamingo\Nebula\ExpLang\TokenList;

    /**
     * Represents Nebula macro.
     */
    class LinkMacro extends BaseMacro
    {
        /**
         * Router service.
         *
         * @Service Router
         *
         * @var Router
         */
        public $router;

        /**
         * Pattern which identificates macro first token.
         *
         * @const array PATTERN
         */
        const PATTERN = [
            TokenList::T_LINK,
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
                $event = explode('.', $this->macro[1]['value']);
                if (class_exists($event[0])) {
                    $reflection = new \ReflectionClass($event[0]);
                    if ($reflection->hasMethod($event[1])) {
                        return true;
                    } else {
                        throw new CompileException("Class {$event[0]} hasn't method {$event[1]} and can't be used in link macro");
                    }
                } else {
                    throw new CompileException("Class {$event[0]} doesn't exist and can't be used in link macro");
                }
            } else {
                $given = TokenList::DICTIONARY[$this->macro[1]['token']];
                throw new CompileException("Link macro expects T_STRING as a controller event, $given given");
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
            $event = explode('.', array_shift($this->macro)['value']);
            $params = [];

            if (isset($this->macro)) {
                foreach ($this->macro as $macro) {
                    $params[] = $macro['value'];
                }
            }

            $event = ['controller' => $event[0], 'action' => $event[1]];

            return $this->router->GenerateURL($event, ...$params);
        }
    }
