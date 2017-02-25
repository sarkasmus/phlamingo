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
        private $response;

        /**
         * Instance the tested class.
         */
        public function setUp()
        {
            $this->response = new Response('Hello world', '1.1', 200, 'UTF-8', ['Content-Type: text/html']);
            $this->response->reflection = new \ReflectionClass($this->response);
        }

        /**
         * Tests properties and getters/setters.
         */
        public function testResponseProperties()
        {
            $this->assertEquals('Hello world', $this->response->content);
            $this->assertEquals('1.1', $this->response->version);
            $this->assertEquals(200, $this->response->statusCode);
            $this->assertEquals('OK', $this->response->status);

            $this->response->statusCode = 500;
            $this->assertEquals(500, $this->response->statusCode);
            $this->assertEquals('Internal Server Error', $this->response->status);

            $this->assertEquals('UTF-8', $this->response->charset);
            $this->assertEquals(['Content-Type: text/html'], $this->response->headers);

            $this->response->addHeader('Date: Tue, 15 Nov 1994 08:12:31 GMT');
            $this->assertEquals(['Content-Type: text/html', 'Date: Tue, 15 Nov 1994 08:12:31 GMT'], $this->response->headers);
        }

        /**
         * Tests reactions when trying to input invalid values.
         */
        public function testExceptions()
        {
            try {
                // setting invalid status code
                $this->response->statusCode = 2156;
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
