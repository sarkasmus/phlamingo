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

    namespace Phlamingo\HTTP;

    use Phlamingo\Core\Object;


    /**
     * HTTP Request - fully programable extension
     * for control HTTP protocol
     */
    class Request extends Object
    {
        /**
         * Request Uniform Resource Identifier
         * @var string $URI
         */
        public $URI;

        /**
         * Method used by request
         * @var string $Method
         */
        public $Method;

        /**
         * Version of HTTP protocol
         * @var string $Version
         */
        public $Version;

        /**
         * Headers of requests
         * @var array $Headers
         */
        public $Headers;

        /**
         * Parameters sent in request
         * @var array $Params
         */
        public $Params;

        /**
         * Cookies sent in request
         * @var array $Cookies
         */
        public $Cookies;

        /**
         * Files sent in request
         * @var array $Files
         */
        public $Files;

        /**
         * Content of request
         * @var string $Content
         */
        public $Content;

        /**
         * Accepted languages
         * @var array $Languages
         */
        public $Languages;

        /**
         * Accepted charsets
         * @var array $Charsets
         */
        public $Charsets;

        /**
         * Accepted Encodings
         * @var array $Encodings
         */
        public $Encodings;

        /**
         * Accepted Content types
         * @var array $ContentTypes
         */
        public $ContentTypes;

        /**
         * Information about client
         * @var string $Client
         */
        public $Client;

        /**
         * Constructor
         *
         * @param string    $uri        Request Uniform Resource Identifier
         * @param string    $method     Method used by request
         * @param string    $version    Version of HTTP protocol
         * @param array     $params     Parameters sent in request (GET, POST)
         * @param array     $headers    Headers of request
         * @param array     $cookies    Cookies sent in requests
         * @param array     $files      Files sent in request
         * @param string    $content    Content of request
         */
        public function __construct(string $uri, string $method, string $version, array $params, array $headers, array $cookies, array $files, string $content)
        {
            parent::__construct();

            $this->URI = $uri;
            $this->Method = $method;
            $this->Version = $version;
            $this->Params = $params;
            $this->Headers = self::ParseHeaders($headers);
            $this->Cookies = $cookies;
            $this->Files = $files;
            $this->Content = $content;

            $this->SetupHeaders();
        }

        /**
         * Setups data from headers
         */
        public function SetupHeaders()
        {
            $this->Languages = self::ParseAccept(isset($this->Headers["Accept-Languages"]) ? $this->Headers["Accept-Languages"] : "undefined", "language");
            $this->Charsets = self::ParseAccept(isset($this->Headers["Accept-Charset"]) ? $this->Headers["Accept-Charset"] : "undefined", "charset");
            $this->Encodings = self::ParseAccept(isset($this->Headers["Accept-Encoding"]) ? $this->Headers["Accept-Encoding"] : "undefined", "encoding");
            $this->ContentTypes = self::ParseAccept(isset($this->Headers["Accept"]) ? $this->Headers["Accept"] : "undefined", "mime-type");
            $this->Client = isset($this->Headers["User-Agent"]) ? $this->Headers["User-Agent"] : "undefined";
        }

        /**
         * Parses HTTP headers to key => value format
         *
         * @@param array $headers Headers
         * @return array Array of arrays [key => value]
         */
        public static function ParseHeaders(array $headers) : array
        {
            $result = [];
            foreach ($headers as $header)
            {
                if (is_string($header))
                {
                    $header = preg_replace('/\s+/', '', $header);
                    $header = explode(":", $header);
                    $result[$header[0]] = $header[1];
                }
                else
                {
                    $result = $header;
                }
            }

            return $result;
        }

        /**
         * Parses accept entries to associative array
         *
         * @param string $accept Accept data
         * @param string $name Accept name
         * @return array Arrays of header datas
         */
        public static function ParseAccept(string $accept, string $name) : array
        {
            $accept = explode(",", $accept);
            $parsedAccept = [];
            foreach ($accept as $item)
            {
                $item = explode(";", $item);
                $array = [$name => $item[0]];
                if (count($item) == 2)
                {
                    $item[1] = explode("=", $item[1]);
                    $array[$item[1][0]] = $item[1][1];
                }
                $parsedAccept[] = $array;
            }
            return $parsedAccept;
        }
    }