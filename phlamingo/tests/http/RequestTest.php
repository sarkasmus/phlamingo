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

namespace Phlamingo\Tests\HTTP;

use Phlamingo\HTTP\Request;

    /**
     * Unit test case of class Phlamingo\HTTP\Request.
     */
    class RequestTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Tested class.
         *
         * @var Request
         */
        private $Request;

        /**
         * Instance the tested class.
         */
        protected function setUp()
        {
            $this->Request = new Request(
                '/article/54',
                'GET',
                '1.1',
                 ['Id' => 54],
                [
                    'Accept-Languages' => 'cs,en;q=0.8,sk',
                    'Accept-Charset'   => 'UTF-8',
                    'Accept-Encoding'  => 'compress,qzip;q=0.5',
                ],
                [],
                [],
                ''
            );
        }

        /**
         * Tests that all Request values are correct.
         */
        public function testRequestValues()
        {
            // Tests that values given within constructor are correct
            $this->assertEquals('/article/54', $this->Request->URI);
            $this->assertEquals('GET', $this->Request->Method);
            $this->assertEquals('1.1', $this->Request->Version);
            $this->assertEquals(['Id' => 54], $this->Request->Params);

            // Tests that headers are parsed correctly
            $this->assertEquals([
                'Accept-Languages' => 'cs,en;q=0.8,sk',
                'Accept-Charset'   => 'UTF-8',
                'Accept-Encoding'  => 'compress,qzip;q=0.5',
            ], $this->Request->Headers);

            $this->assertEquals([], $this->Request->Cookies);
            $this->assertEquals([], $this->Request->Files);
            $this->assertEquals('', $this->Request->Content);

            // Tests Accept headers are parsed correctly
            $this->assertEquals(
                [
                    ['language' => 'cs'],
                    ['language' => 'en', 'q' => '0.8'],
                    ['language' => 'sk'],
                ],
                $this->Request->Languages
            );

            $this->assertEquals(
                [
                    ['charset' => 'UTF-8'],
                ],
                $this->Request->Charsets
            );

            $this->assertEquals([
                ['encoding' => 'compress'],
                ['encoding' => 'qzip', 'q' => '0.5'],
            ],
            $this->Request->Encodings
            );
        }

        /**
         * Tests ParseAccept helper.
         */
        public function testParseAccept()
        {
            $this->assertEquals([
                    ['name' => '1'],
                    ['name' => '2'],
                    ['name' => '3'],
                ],
                $this->Request->ParseAccept('1, 2, 3', 'name')
            );

            $this->assertEquals([
                    ['name' => '1'],
                    ['name' => '2', 'q' => '0.8'],
                    ['name' => '3'],
                ],
                $this->Request->ParseAccept('1, 2;q=0.8, 3', 'name')
            );
        }
    }
