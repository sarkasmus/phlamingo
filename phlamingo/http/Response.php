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
     * A HTTP Response object - fully programmable extension
     * for control HTTP protocol
     */
    class Response extends Object
    {
        /**
         * List of all http status codes and their status texts
         * @var array $statusList
         */
        public $statusList = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',            // RFC2518
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',          // RFC4918
            208 => 'Already Reported',      // RFC5842
            226 => 'IM Used',               // RFC3229
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',    // RFC7238
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',                                               // RFC2324
            421 => 'Misdirected Request',                                         // RFC7540
            422 => 'Unprocessable Entity',                                        // RFC4918
            423 => 'Locked',                                                      // RFC4918
            424 => 'Failed Dependency',                                           // RFC4918
            425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
            426 => 'Upgrade Required',                                            // RFC2817
            428 => 'Precondition Required',                                       // RFC6585
            429 => 'Too Many Requests',                                           // RFC6585
            431 => 'Request Header Fields Too Large',                             // RFC6585
            451 => 'Unavailable For Legal Reasons',                               // RFC7725
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates (Experimental)',                      // RFC2295
            507 => 'Insufficient Storage',                                        // RFC4918
            508 => 'Loop Detected',                                               // RFC5842
            510 => 'Not Extended',                                                // RFC2774
            511 => 'Network Authentication Required',                             // RFC6585
        ];

        /**
         * Version of HTTP protocol
         * @var string $Version
         */
        public $Version;

        /**
         * Code status of response
         * @var integer $StatusCode
         */
        protected $StatusCode;

        /**
         * Text variant of status of response
         * @var string $Status
         */
        protected $Status;

        /**
         * Charset which is used in response
         * @var string $Charset
         */
        public $Charset;

        /**
         * All other headers in response
         * @var array $Headers
         */
        public $Headers;

        /**
         * Content of response
         * @var string $Content
         */
        public $Content;

        /**
         * Constructor
         *
         * @param string    $content       Content of response
         * @param string    $version       Version of HTTP protocol            [optional  =  "1.1"        ]
         * @param integer   $statusCode    Code status of response             [optional  =  200          ]
         * @param string    $charset       Charset which is used in response   [optional  =  "UTF-8"      ]
         * @param array     $headers       All other headers in response       [optional  =  empty array  ]
         */
        public function __construct(string $content, string $version = "1.1", int $statusCode = 200, string $charset = "UTF-8", array $headers = [])
        {
            parent::__construct();

            $this->Content = $content;
            $this->Version = $version;
            $this->StatusCode = $statusCode;
            $this->Charset = $charset;
            $this->Headers = $headers;

            // Convert number code to text
            $this->Status = strtr($statusCode, $this->statusList);
        }

        /**
         * Adds header to header array
         *
         * @param string $header Header to add
         */
        public function AddHeader(string $header)
        {
            $this->Headers[] = $header;
        }

        /**
         * Sets new status code
         *
         * @param integer $code Status code
         */
        public function SetStatusCode(int $code)
        {
            $this->StatusCode = $code;
            $this->Status = strtr($code, $this->statusList);
        }

        /**
         * Sends all headers
         */
        public function SendHeaders()
        {
            if (!headers_sent())
            {
                $this->Headers[] = "HTTP $this->Version $this->StatusCode $this->Status";
                $this->Headers[] = "Charset: " . $this->Charset;
                foreach ($this->Headers as $header)
                {
                    header($header, true, $this->StatusCode);
                }
            }
        }

        /**
         * Sends content
         */
        public function SendContent()
        {
            echo $this->Content;
        }

        /**
         * Sends full response
         */
        public function Send()
        {
            $this->SendHeaders();
            $this->SendContent();
        }
    }