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
    class EndMarkdownMacro extends BaseMacro
    {
        /**
         * Pattern which identificates macro first token.
         *
         * @const array PATTERN
         */
        const PATTERN = [
            TokenList::T_ENDMARKDOWN,
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
            if (!isset($this->macro[1])) {
                return true;
            } else {
                $given = TokenList::DICTIONARY[$this->macro[1]['token']];
                throw new CompileException("Endfor macro doesn't expect any other tokens $given given");
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
            $return = "';?>";
            $return .= "<?php if (!function_exists(\"parseMarkdown\")) {
                function parseMarkdown(string \$text)
                {
                    \$text = str_replace('\\$', '-::-', \$text);
                    \$markdownParser = new \\Parsedown();
                    \$explode = explode(\"\\n\", \$text);
                    \$long = 512;
                     
                    foreach (\$explode as \$num => \$line) {
                        \$match = [];
                        \$text = preg_replace('/\\t/', '    ', \$text);
                        preg_match(\"/([ ]{2,}|[ ]{4,})(?!\\s)/\", \$line, \$match);
                        if (isset(\$match[0]) and strlen(\$match[0]) < \$long) {
                            \$long = strlen(\$match[0]);
                        
                        }

                    }
                     
                    foreach (\$explode as \$num => \$line) {
                        \$explode[\$num] = str_replace(str_repeat(' ', \$long), '', \$line);

                    }

                    \$return = implode(\"\\n\", \$explode);
                    \$return = \$markdownParser->text(\$return);
                    
                    \$return = html_entity_decode(\$return);
                    
                    return \$return;
                    
                }

            } ?>";
            $return .= '<?php echo parseMarkdown($text); ?>';

            return $return;
        }
    }
