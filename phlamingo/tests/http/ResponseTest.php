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

use Phlamingo\HTTP\Exceptions\HttpException;
    use Phlamingo\HTTP\Response;

    /**
     * Unit test case of class Phlamingo\HTTP\Response.
     */
    class ResponseTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Tested class.
         */
        private $Response;

        /**
         * Instance the tested class.
         */
        public function setUp()
        {
            $this->Response = new Response('Hello world', '1.1', 200, 'UTF-8', ['Content-Type: text/html']);
            $this->Response->Reflection = new \ReflectionClass($this->Response);
        }

        /**
         * Tests properties and getters/setters.
         */
        public function testResponseProperties()
        {
            $this->assertEquals('Hello world', $this->Response->Content);
            $this->assertEquals('1.1', $this->Response->Version);
            $this->assertEquals(200, $this->Response->StatusCode);
            $this->assertEquals('OK', $this->Response->Status);

            $this->Response->StatusCode = 500;
            $this->assertEquals(500, $this->Response->StatusCode);
            $this->assertEquals('Internal Server Error', $this->Response->Status);

            $this->assertEquals('UTF-8', $this->Response->Charset);
            $this->assertEquals(['Content-Type: text/html'], $this->Response->Headers);

            $this->Response->AddHeader('Date: Tue, 15 Nov 1994 08:12:31 GMT');
            $this->assertEquals(['Content-Type: text/html', 'Date: Tue, 15 Nov 1994 08:12:31 GMT'], $this->Response->Headers);
        }

        /**
         * Tests reactions when trying to input invalid values.
         */
        public function testExceptions()
        {
            try {
                // setting invalid status code
                $this->Response->StatusCode = 2156;
                $this->fail("exception wasn't thrown");
            } catch (HttpException $e) {
            }

            try {
                // setting invalid status code
                $response = new Response('some content', '1.1', 2156);
                $this->fail("exception wasn't thrown");
            } catch (HttpException $e) {
            }

            try {
                // setting invalid version
                $response = new Response('some content', 'invalid version');
                $this->fail("exception wasn't thrown");
            } catch (HttpException $e) {
            }
        }
    }
