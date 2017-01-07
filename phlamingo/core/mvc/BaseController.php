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

    namespace Phlamingo\Core\MVC;

    use DocBlockReader\Reader;
    use Phlamingo\Core\Object;
    use Phlamingo\HTTP\Request;
    use Phlamingo\Core\MVC\Exceptions\ControllerException;
    use Phlamingo\HTTP\Response;


    /**
     * BaseController is parent of all controllers in
     * application and is providing its base logic
     */
    abstract class BaseController extends Object
    {
        /**
         * @Service Request
         * @var Request
         */
        public $Request;

        /**
         * @Service Session
         */
        public $Session;

        /**
         * Calls before action
         */
        protected function BeforeAction()
        {

        }

        /**
         * Calls after action
         */
        protected function AfterAction()
        {

        }

        /**
         * Runs a controller action
         *
         * @param string $action Action name
         * @param array $params Params of the action
         * @return Response Response
         * @throws ControllerException When action returns incorrect response
         */
        public final function Run(string $action, ...$params)
        {
            $this->BeforeAction();
            $response = $this->$action(...$params);
            $this->AfterAction();

            if ($response instanceof Response)
            {
                return $response;
            }
            elseif (is_string($response))
            {
                return new Response($response);
            }
            elseif (is_array($response))
            {
                return new JsonResponse($response);
            }
            elseif ($response instanceof \DOMDocument)
            {
                return new XmlResponse($response);
            }
            else
            {
                throw new ControllerException("Action {$action} returns incorrect response");
            }
        }

        /**
         * Redirects user to another url
         *
         * @param string|array $pathOrEvent String path to redirect or event of controller as array
         * @param array $params Params of the redirect path
         * @return Response Response
         * @throws \InvalidArgumentException When pathOrEvent is not of type string or array
         */
        protected final function Redirect($pathOrEvent, ...$params)
        {
            if (is_string($pathOrEvent))
            {
                return new Response("", "1.1", 301, "UTF-8", "Location: " . $pathOrEvent);
            }
            elseif (is_array($pathOrEvent))
            {
                $router = new Router();
                $router->GenerateURL($pathOrEvent, ...$params);
                return new Response("", "1.1", 301, "UTF-8", "Location: " . $pathOrEvent);
            }
            else
            {
                throw new \InvalidArgumentException("First parameter of Controller::Redirect method must be string or array. " . gettype($pathOrEvent) . " Given");
            }
        }
    }