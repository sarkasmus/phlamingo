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

use Phlamingo\Config\Parsers\JsonConfigParser;

    class JsonConfigParserTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @var JsonConfigParser
         */
        protected $Parser;

        public function setUp()
        {
            $this->Parser = new JsonConfigParser();
        }

        public function testJson()
        {
            $this->assertEquals('{"foo":"bar"}', $this->Parser->dump(['foo' => 'bar']));
            $this->assertEquals(['foo' => 'bar'], $this->Parser->parse('{"foo":"bar"}'));
        }
    }
