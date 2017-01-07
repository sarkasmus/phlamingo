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

    namespace Phlamingo\Tests\Config\Parsers;


    use Phlamingo\Config\Parsers\YamlConfigParser;


    class YamlConfigParserTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @var YamlConfigParser
         */
        protected $Parser;

        public function setUp()
        {
            $this->Parser = new YamlConfigParser();
        }

        public function testYaml()
        {
            $this->assertEquals("foo: bar\n", $this->Parser->Dump(array("foo" => "bar")));
            $this->assertEquals(array("foo" => "bar"), $this->Parser->Parse("foo: bar"));
        }
    }
