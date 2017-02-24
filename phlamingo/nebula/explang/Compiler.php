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

namespace Phlamingo\Nebula\ExpLang;

use Phlamingo\Core\Object;
    use Phlamingo\Nebula\Exceptions\CompileException;

    /**
     * Compiles current macro.
     */
    class Compiler extends Object
    {
        /**
         * Variable list.
         *
         * @var array
         */
        protected $variables = [];

        /**
         * Token list containing regexps for all nebula tokens.
         *
         * @var array
         */
        protected $tokens = [
            '/(?<!\w)foreach(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                           => 0, // FOREACH
            '/(?<!\w)for(?!each)(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                       => 1, // FOR
            '/(?<!\w)if(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                                => 2, // IF
            '/(?<!\w)elseif(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                            => 3, // ELSEIF
            '/(?<!\w)as(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                                => 4, // AS
            '/[a-zA-Z0-9_]{1}["\'$a-zA-Z0-9_.\[\]\/\\\]*/i'                             => 5, // STRING
            '/(?<![a-zA-Z0-9_\-.<>*+\/\\\?|])(and|&&)(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'  => 6, // LOGICAL AND
            '/(?<![a-zA-Z0-9_\-.<>*+\/\\\?|])(or|\|\|)(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i' => 7, // LOGICAL OR
            '/(?<!\w)block(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                             => 8, // BLOCK
            '/(?<!\w)while(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                             => 9, // WHILE
            '/(?<!\w)endforeach(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                        => 10, // ENDFOREACH
            '/(?<!\w)endfor(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                            => 11, // ENDFOR
            '/(?<!\w)endif(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                             => 12, // ENDIF
            '/(?<![=><!*\/+\-.\\\?_])=>(?![=><!*\/+\-.\\\?_])/'                         => 13, // ARROW OPERATOR =>
            '/(?<!\w\d)(true|false)(?!\w\d)/i'                                          => 14, // BOOL VALUE
            '/--/'                                                                      => 15, // DECREMENT OPERATOR --
            '/\//'                                                                      => 16, // DIVIDE OPERATOR /
            '/\*/'                                                                      => 17, // MULTIPLY OPERATOR *
            '/-/'                                                                       => 18, // SUBTRACT OPERATOR -
            '/\+/'                                                                      => 19, // ADD OPERATOR +
            '/(?<!\w)else(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                              => 20, // ELSE
            '/(?<!\w)endwhile(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                          => 21, // ENDWHILE
            '/(?<![=><!*\/+\-.\\\?_])=(?![=><!*\/+\-.\\\?_])/'                          => 22, // EQUAL OPERATOR =
            '/(?<![=><!*\/+\-.\\\?_])>(?![=><!*\/+\-.\\\?_])/'                          => 23, // GREATER THAN OPERATOR >
            '/(?<![=><!*\/+\-.\\\?_])>=(?![=><!*\/+\-.\\\?_])/'                         => 24, // GREATER OR EQUAL THAN OPERATOR >=
            '/(?<![=><!*\/+\-.\\\?_])<(?![=><!*\/+\-.\\\?_])/'                          => 25, // SMALLER THAN OPERATOR <
            '/(?<![=><!*\/+\-.\\\?_])<=(?![=><!*\/+\-.\\\?_])/'                         => 26, // SMALLER OR EQUAL THAN OPERATOR <=
            '/(?<![a-zA-Z])[0-9]+(?![a-zA-Z])/'                                         => 27, // INTEGER
            '/(?<![=><!*\/+\-.\\\?_])==(?![=><!*\/+\-.\\\?_])/'                         => 28, // IS EQUAL
            '/(?<![=><!*\/+\-.\\\?_])===(?![=><!*\/+\-.\\\?_])/'                        => 29, // IS SAME
            '/(?<![=><!*\/+\-.\\\?_])!=(?![=><!*\/+\-.\\\?_])/'                         => 30, // NOT EQUAL
            '/(?<![=><!*\/+\-.\\\?_])!==(?![=><!*\/+\-.\\\?_])/'                        => 31, // NOT SAME
            '/(?<![=><!*\/+\-.\\\?_])%(?![=><!*\/+\-.\\\?_])/'                          => 32, // MODULO OPERATOR %
            '/(?<![=><!*\/+\-.\\\?_])!(?![=><!*\/+\-.\\\?_])/'                          => 33, // NEGATE OPERATOR !
            '/(?<!\w)endblock(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                          => 34, // ENDFOREACH
            '/(?<![a-zA-Z0-9_\-.<>*+\/\\\?|])noescape(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'  => 35, // NOESCAPE
            '/(?<![a-zA-Z0-9_\-.<>*+\/\\\?|])repeat(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'    => 36, // REPEAT
            '/(?<![a-zA-Z0-9_\-.<>*+\/\\\?|])link(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'      => 37, // LINK
            '/(?<![a-zA-Z0-9_\-.<>*+\/\\\?|])endrepeat(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i' => 38, // ENDREPEAT
            '/\+\+/'                                                                    => 39, // INCREMENT OPERATOR ++
            '/;/'                                                                       => 40, // SEMICOLON
            '/\(/'                                                                      => 41, // LEFT BRACKET
            '/\)/'                                                                      => 42, // RIGHT BRACKET
            '/(?<!.)\[(?!.)/'                                                           => 43, // LEFT SQUARE BRACKET
            '/(?<!.)\](?!.)/'                                                           => 44, // RIGHT SQUARE BRACKET
            '/(?<!\w)markdown(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                          => 45, // MARKDOWN
            '/(?<!\w)endmarkdown(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                       => 46, // ENDMARKDOWN
            '/(?<!\w)icon(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                              => 47, // ICON
            '/(?<!\w)load(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                              => 48, // LOAD
            '/(?<!\w)container(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                         => 49, // CONTAINER
            '/(?<!\w)endcontainer(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                      => 50, // ENDCONTAINER
            '/(?<!\w)row(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                               => 51, // ROW
            '/(?<!\w)endrow(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                            => 52, // ENDROW
            '/(?<!\w)column(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                            => 53, // COLUMN
            '/(?<!\w)endcolumn(?![a-zA-Z0-9_\-.<>*+\/\\\?|])/i'                         => 54, // ENDCOLUMN
        ];

        /**
         * Adds variable to list.
         *
         * @param string $name  Name of the variable to add.
         * @param string $value Value.
         *
         * @throws CompileException
         * @throws \InvalidArgumentException
         */
        public function addVariable(string $name, $value)
        {
            if (!empty($name)) {
                if (!isset($this->variables[$name])) {
                    $this->variables[$name] = $value;
                } else {
                    throw new CompileException();
                }
            } else {
                throw new \InvalidArgumentException();
            }
        }

        /**
         * Returns if compiler has variable with name in list.
         *
         * @param string $name Name of the variable.
         *
         * @return bool
         */
        public function hasVariable(string $name) : bool
        {
            if (isset($this->variables[$name])) {
                return true;
            }

            return false;
        }

        /**
         * Returns value of variable with name $name.
         *
         * @param string $name Name of the variable.
         *
         * @throws CompileException
         *
         * @return mixed Value of the variable.
         */
        public function getVariable(string $name)
        {
            if ($this->hasVariable($name)) {
                return $this->variables[$name];
            } else {
                throw new CompileException("Variable with name {$name} is not defined in Compiler");
            }
        }

        /**
         * Compiles macros.
         *
         * @param array $macros List of the macros
         *
         * @return array Parsed macros
         */
        public function compile(array $macros) : array
        {
            $lexer = new Lexer();
            $lexer->define($this->tokens,
                [
                    5 => function ($match) {
                        $keywords = [
                            'foreach',
                            'as',
                            'for',
                            'if',
                            'elseif',
                            'else',
                            'while',
                            'and',
                            'or',
                            'link',
                            'repeat',
                            'block',
                            'endif',
                            'endfor',
                            'endforeach',
                            'endwhile',
                            'endrepeat',
                            'endblock',
                            'noescape',

                        ];
                        if (in_array($match, $keywords)) {
                            return false;
                        }

                        return true;
                    },
                ]);
            $compiler = new MacroCompiler();

            $result = [];
            foreach ($macros as $key => $macro) {
                $tokenRow = $lexer->lex($macro['macro']);
                $result[$key] = $compiler->compile($tokenRow, $macro['content']);
                if ($result[$key] !== null) {
                    if ($result[$key]->check($this)) {
                        $result[$key] = $result[$key]->compile($this);
                    }
                }
            }

            return $result;
        }
    }
