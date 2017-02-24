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

use Phlamingo\Core\MVC\Exceptions\ControllerException;
    use Phlamingo\Core\Object;
    use Phlamingo\HTTP\JsonResponse;
    use Phlamingo\HTTP\Request;
    use Phlamingo\HTTP\Response;
    use phlamingo\http\XmlResponse;

    /**
     * BaseController is parent of all controllers in
     * application and is providing its base logic.
     */
    abstract class BaseController extends Object
    {
        /**
         * @Service Request
         *
         * @var Request
         */
        public $request;

        /**
         * @Service Session
         */
        public $session;

        /**
         * Calls before action.
         */
        protected function beforeAction()
        {
        }

        /**
         * Calls after action.
         */
        protected function afterAction()
        {
        }

        /**
         * Runs a controller action.
         *
         * @param string $action Action name
         * @param array  $params Params of the action
         *
         * @throws ControllerException When action returns incorrect response
         *
         * @return Response Response
         */
        final public function run(string $action, ...$params)
        {
            $this->beforeAction();
            $response = $this->$action(...$params);
            $this->afterAction();

            if ($response instanceof Response) {
                return $response;
            } elseif (is_string($response)) {
                return new Response($response);
            } elseif (is_array($response)) {
                return new JsonResponse($response);
            } elseif ($response instanceof \DOMDocument) {
                return new XmlResponse($response);
            } else {
                throw new ControllerException("Action {$action} returns incorrect response");
            }
        }

        /**
         * Redirects user to another url.
         *
         * @param string|array $pathOrEvent String path to redirect or event of controller as array
         * @param array        $params      Params of the redirect path
         *
         * @throws \InvalidArgumentException When pathOrEvent is not of type string or array
         *
         * @return Response Response
         */
        final protected function redirect($pathOrEvent, ...$params)
        {
            if (is_string($pathOrEvent)) {
                return new Response('', '1.1', 301, 'UTF-8', 'Location: '.$pathOrEvent);
            } elseif (is_array($pathOrEvent)) {
                $router = new Router();
                $router->generateURL($pathOrEvent, ...$params);

                return new Response('', '1.1', 301, 'UTF-8', 'Location: '.$pathOrEvent);
            } else {
                throw new \InvalidArgumentException('First parameter of Controller::Redirect method must be string or array. '.gettype($pathOrEvent).' Given');
            }
        }
    }
