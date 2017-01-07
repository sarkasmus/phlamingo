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

    namespace phlamingo\http;


    /**
     * {Description}
     */
    class XmlResponse extends Response
    {
        public function __construct(\DOMDocument $content, $version = "1.1", $statusCode = 200, $charset = "UTF-8", array $headers = [])
        {
            $content = $content->saveXML();
            $headers[] = "Content-Type: application/xml";
            parent::__construct($content, $version, $statusCode, $charset, $headers);
        }
    }