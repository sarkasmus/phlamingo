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

    /**
     * Finds macros in code.
     */
    class MacroParser extends Object
    {
        /**
         * Finds macros in code.
         *
         * @param string $code Code
         *
         * @return array Macros found
         */
        public function parse(string $code) : array
        {
            $code = str_replace("\n", ' ', $code);
            $regex1 = '/@\{[\s\w<>=-_\+\*\/\.\[\]()?\d"\'\$]+}/';

            $macros = null;
            preg_match_all($regex1, $code, $macros);

            $result = [];
            if ($macros !== null) {
                foreach ($macros[0] as $macro) {
                    $result[$macro] = ['macro' => trim($macro, '@{}'), 'content' => ''];
                }
            } else {
                return [];
            }

            return $result;
        }
    }
