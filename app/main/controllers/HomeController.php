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

namespace App\Main\Controllers;

use Phlamingo\Core\MVC\BaseController;
    use Phlamingo\HTTP\Response;

    /**
     * {Description}.
     */
    class HomeController extends BaseController
    {
        /**
         * @Route /
         */
        public function DefaultAction()
        {
            return new Response('Hello world');
        }
    }
