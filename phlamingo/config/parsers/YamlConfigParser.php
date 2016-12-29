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
    use Symfony\Component\Yaml\Yaml;
    
    
    /**
     * YamlConfigParser converts yaml to php arrays and conversely
     */
    class YamlConfigParser implements ConfigParserInterface
    {
        /**
         * Parses yaml to array
         *
         * @param string $code Yaml code
         * @return array Array
         */
        public function Parse(string $code) : array
        {
            $result = Yaml::parse($code);
            return $result;
        }
    
        /**
         * Parses yaml from file to array
         *
         * @param string $path Path to file
         * @return array Array
         * @throws ConfigException When file doesn't exists
         */
        public function ParseFile(string $path) : array
        {
            if (file_exists($path))
            {
                $result = Yaml::parse(file_get_contents($path));
                return $result;
            }
            else
            {
                throw new ConfigException("File {$path} doesn't exist");
            }
        }
    
        /**
         * Converts array to yaml
         *
         * @param array $data Array
         * @return string Yaml code
         */
        public function Dump(array $data) : string
        {
            $result = Yaml::dump($data);
            return $result;
        }
    
        /**
         * Converts array to yaml and saves to file
         *
         * @param array $data Array
         * @param string $path Path to file
         */
        public function DumpToFile(array $data, string $path)
        {
            $result = Yaml::dump($data);
    
            $file = fopen($path, "w");
            fwrite($file, $result);
            fclose($file);
        }
    }
