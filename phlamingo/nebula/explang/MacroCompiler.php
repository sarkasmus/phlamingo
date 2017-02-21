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
    use Phlamingo\Nebula\ExpLang\Macros\BaseMacro;
    use Phlamingo\Nebula\ExpLang\Macros\BlockMacro;
    use Phlamingo\Nebula\ExpLang\Macros\ElseIfMacro;
    use Phlamingo\Nebula\ExpLang\Macros\ElseMacro;
    use Phlamingo\Nebula\ExpLang\Macros\EndBlockMacro;
    use Phlamingo\Nebula\ExpLang\Macros\EndForeachMacro;
    use Phlamingo\Nebula\ExpLang\Macros\EndForMacro;
    use Phlamingo\Nebula\ExpLang\Macros\EndIfMacro;
    use Phlamingo\Nebula\ExpLang\Macros\EndMarkdownMacro;
    use Phlamingo\Nebula\ExpLang\Macros\EndRepeatMacro;
    use Phlamingo\Nebula\ExpLang\Macros\EndWhileMacro;
    use Phlamingo\Nebula\ExpLang\Macros\ForeachMacro;
    use Phlamingo\Nebula\ExpLang\Macros\ForMacro;
    use Phlamingo\Nebula\ExpLang\Macros\IfMacro;
    use Phlamingo\Nebula\ExpLang\Macros\KeyForeachMacro;
    use Phlamingo\Nebula\ExpLang\Macros\LinkMacro;
    use Phlamingo\Nebula\ExpLang\Macros\MarkdownMacro;
    use Phlamingo\Nebula\ExpLang\Macros\MixinMacro;
    use Phlamingo\Nebula\ExpLang\Macros\PrintVarMacro;
    use Phlamingo\Nebula\ExpLang\Macros\RepeatMacro;
    use Phlamingo\Nebula\ExpLang\Macros\TranslateMacro;
    use Phlamingo\Nebula\ExpLang\Macros\WhileMacro;


    /**
     * Compiles macro from token row
     */
    class MacroCompiler extends Object
    {
        /**
         * @Service MacroCacher
         */
        public $cacher;

        /**
         * List of Macros patterns
         */
        public $patternList = [
            MarkdownMacro::class => MarkdownMacro::PATTERN,
            BlockMacro::class => BlockMacro::PATTERN,
            ElseIfMacro::class => ElseIfMacro::PATTERN,
            ElseMacro::class => ElseMacro::PATTERN,
            EndBlockMacro::class => EndBlockMacro::PATTERN,
            EndForeachMacro::class => EndForeachMacro::PATTERN,
            EndForMacro::class => EndForMacro::PATTERN,
            EndIfMacro::class => EndIfMacro::PATTERN,
            EndWhileMacro::class => EndWhileMacro::PATTERN,
            ForeachMacro::class => ForeachMacro::PATTERN,
            ForMacro::class => ForMacro::PATTERN,
            IfMacro::class => IfMacro::PATTERN,
            LinkMacro::class => LinkMacro::PATTERN,
            RepeatMacro::class => RepeatMacro::PATTERN,
            EndRepeatMacro::class => EndRepeatMacro::PATTERN,
            PrintVarMacro::class => PrintVarMacro::PATTERN,
            WhileMacro::class => WhileMacro::PATTERN,
            EndMarkdownMacro::class => EndMarkdownMacro::PATTERN,
        ];

        /**
         * Constructor.
         */
        public function __construct()
        {
            parent::__construct();
            if ($this->cacher->Cached()) {
                cache:
                foreach ($this->cacher->Get() as $macro) {
                    $this->patternList[$macro] = $macro::PATTERN;

                }

            } else {
                $this->cacher->Cache();
                goto cache;

            }
        }

        /**
         * Finds macro and return it's instance
         *
         * @param array $tokenRow Row of tokens
         * @param string $content [Unused]
         * @return BaseMacro Macro
         * @throws CompileException When macro wasn't found
         */
        public function compile(array $tokenRow, string $content)
        {
            $match = null;
            foreach ($this->patternList as $macro => $pattern) {
                foreach ($tokenRow as $key => $token) {
                    if ($pattern[$key] != $token['token']) {
                        $match = null;
                        break;

                    } else {
                        $match = $macro;
                        break;

                    }

                }

                if (isset($match)) {
                    break;

                }
            }

            if (isset($match)) {
                var_dump($tokenRow);
                return new $match($tokenRow, $content);

            }
            else
            {
                throw new CompileException("");
            }
        }
    }