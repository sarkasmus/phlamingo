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


    /**
     * Parse macros to tokens by defined rules.
     */
    class Lexer extends Object
    {
        /**
         * List of defined tokens.
         * @var array $tokens
         */
        protected $tokens = [];

        /**
         * List of callbacks to tokens
         * @var array $callbacks
         */
        protected $callbacks;

        /**
         * Defines tokens and callbacks to current lexer
         *
         * @param array $tokens
         * @param array $callbacks
         */
        public function define(array $tokens, array $callbacks = [])
        {
            $this->tokens = $tokens;
            $this->callbacks = $callbacks;
        }

        /**
         * Parses expression by defined macros and callbacks
         */
        public function lex(string $macro)
        {
            $matches = [];

            // Browse the tokens and find it matches
            foreach ($this->tokens as $pattern => $token) {
                $fmatches = [];
                preg_match_all($pattern, $macro, $fmatches, PREG_OFFSET_CAPTURE);

                // If token has defined callback it filters matches by it
                if (isset($this->callbacks[$token])) {
                    foreach ($fmatches[0] as $key => $fmatch) {
                        if ($this->callbacks[$token]($fmatch[0]) !== true) {
                            unset($fmatches[0][$key]);

                        }

                    }

                }
                $matches[$token] = $fmatches;

            }

            // Set format of matches to [offset] => [token, value] - offset is offset of match in string
            $results = [];
            foreach ($matches as $token => $match) {
                if (empty($match[0])) {
                    continue;
                }

                foreach ($match[0] as $oneMatch) {
                    $results[$oneMatch[1]] = ['token' => $token, 'value' => $oneMatch[0]];

                }

            }

            // Sort tokens to their right row by offset
            ksort($results);

            // Set sequential keys
            $i = 0;
            $newResults = [];
            foreach ($results as $result) {
                $newResults[$i] = $result;
                $i++;

            }

            return $newResults;
        }
    }