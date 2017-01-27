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

    namespace Phlamingo\Nebula\Compile;

    use Phlamingo\Core\Object;
    use Phlamingo\Nebula\ExpLang\Compiler as ExpCompiler;

    /**
     * Manages compiling of template.
     */
    class Compiler extends Object
    {
        /**
         * Code of the template.
         * @var string $code
         */
        protected $code;

        /**
         * Macro list.
         * @var array $macros
         */
        protected $macros = [];

        /**
         * Constructor.
         *
         * @param string $code Code of the template.
         */
        public function __construct(string $code)
        {
            // Call Object constructor for init DI
            parent::__construct();
            $this->code = $code;
        }

        /**
         * Builds template - load other files into template
         *
         * @param string $path Path of the source directory
         * @return string Code
         */
        public function build(string $path) : string
        {
            $preprocessor = new Preprocessor();
            return $this->code = $preprocessor->preprocess($this->code, $path);
        }

        /**
         * Parses macros - find macros in code and parses them into tokens
         */
        public function parseMacros()
        {
            $macroParser = new MacroParser();
            $this->macros = $macroParser->parse($this->code);
        }

        /**
         * Compiles macros - replace macros for native php code
         */
        public function compileMacros()
        {
            $macroCompiler = new ExpCompiler();
            $this->macros = $macroCompiler->compile($this->macros);

            $this->code = strtr($this->code, $this->macros);
        }

        public function compileTags()
        {
            // Will be implemented
        }

        /**
         * Getter for code
         */
        public function getCode() : string
        {
            return $this->code;
        }
    }