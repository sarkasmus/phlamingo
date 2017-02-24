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

namespace Phlamingo\Config\Parsers;

use Phlamingo\Config\Exceptions\ConfigException;

    /**
     * JsonConfigParser converts json to php arrays and conversely.
     */
    class JsonConfigParser implements ConfigParserInterface
    {
        /**
         * Parses json to array.
         *
         * @param string $code Json code
         *
         * @return array Array
         */
        public function Parse(string $code) : array
        {
            $result = json_decode($code, true);

            return $result;
        }

        /**
         * Parses json from file to array.
         *
         * @param string $path Path to file
         *
         * @throws ConfigException When file doesn't exists
         *
         * @return array Array
         */
        public function ParseFile(string $path) : array
        {
            if (file_exists($path)) {
                $result = json_decode(file_get_contents($path));

                return $result;
            } else {
                throw new ConfigException("File {$path} doesn't exist");
            }
        }

        /**
         * Converts array to json.
         *
         * @param array $data Array
         *
         * @return string Json code
         */
        public function Dump(array $data) : string
        {
            $result = json_encode($data);

            return $result;
        }

        /**
         * Converts array to json and saves to file.
         *
         * @param array  $data Array
         * @param string $path Path to file
         */
        public function DumpToFile(array $data, string $path)
        {
            $result = json_encode($data);

            $file = fopen($path, 'w');
            fwrite($file, $result);
            fclose($file);
        }
    }
