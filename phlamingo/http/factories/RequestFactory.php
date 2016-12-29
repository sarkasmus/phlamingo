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

namespace Phlamingo\HTTP\Factories;

use Phlamingo\Di\BaseFactory;
use Phlamingo\HTTP\Request;


/**
 * {Description}
 * @Factory Request
 */
class RequestFactory extends BaseFactory
{
    public  function Make() : Request
    {
        return new Request(
            $_SERVER['REQUEST_URI'],
            $_SERVER['REQUEST_METHOD'],
            explode("/", $_SERVER['SERVER_PROTOCOL'])[1],
            $_GET,
            getallheaders(),
            $_COOKIE,
            $_FILES,
            file_get_contents("php://input")
        );
    }
}