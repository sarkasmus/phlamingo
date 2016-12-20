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

        public function BeforeAction()
        {

        }

        public function AfterAction()
        {

        }

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
                // Implement controller exception !!!
                throw new ControllerException();
            }
        }

        protected final function SetupPersists()
        {

        }

        protected final function Render(string $template)
        {

        }

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
                throw new ControllerException("Path" . var_export($pathOrEvent) . "can't be redirected");
            }
        }
    }