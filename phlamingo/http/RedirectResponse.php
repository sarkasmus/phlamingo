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

/**
     * {Description}.
     */
    class RedirectResponse extends Response
    {
        public function __construct($pathOrEvent, ...$params)
        {
            if (is_string($pathOrEvent)) {
                parent::__construct('', '1.1', 301, 'UTF-8', 'Location: '.$pathOrEvent);
            } elseif (is_array($pathOrEvent)) {
                $router = new Router();
                $router->GenerateURL($pathOrEvent, ...$params);
                parent::__construct('', '1.1', 301, 'UTF-8', 'Location: '.$pathOrEvent);
            } else {
                throw new HttpException('Path'.var_export($pathOrEvent)."can't be redirected");
            }
        }
    }
