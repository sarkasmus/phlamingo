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

    use Phlamingo\Core\MVC\BaseController;
    use Phlamingo\HTTP\Request;
    use Phlamingo\Core\MVC\Router;
    use Phlamingo\Config\Configurator;
    use Phlamingo\Di\Container;

    /**
     * Application manages all actions to setup environment and
     * proccess the user request
     */
    class Application extends \Phlamingo\Core\ApplicationAbstract
    {
        /**
         * Here write code to run your app
         *
         * @param BaseController $controller Requested controller
         * @param string $controllerAction Name of requested action
         * @param array|mixed $params Params of controller action
         */
        public function Main(BaseController $controller, string $controllerAction, ...$params)
        {
            // Here write your code:


            // Runs controller
            $response = $controller->run($controllerAction, ...$params);
            $response->Send();
        }

        /**
         * Here write your config code before app will start
         *
         * @param Configurator $config Config class
         * @return Configurator Set config class
         */
        public function Config(Configurator $config) : Configurator
        {
            // Here setup your app:

            return $config;
        }

        public function SetupDI(Container $container)
        {
            $container->AddService("Request", function(){
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
            });

            $container->AddService("Session", function(Container $container){
                $request = $container->Get("Request");
                if (isset($container->Singletons["Session"]))
                {
                    return $container->Singletons["Session"];
                }
                elseif (isset($request->Cookies["SessID"]))
                {
                    $container->Singletons["Session"] = new Phlamingo\HTTP\Sessions\Session($request->Cookies["SessID"]);
                    return $container->Singletons["Session"];
                }
                else
                {
                    $container->Singletons["Session"] = new Phlamingo\HTTP\Sessions\Session();
                    setcookie(
                        "SessID",
                        $container->Singletons["Session"]->SessionID,
                        time() + 8600 * 14,
                        "/",
                        DOMAIN,
                        false,
                        true
                    );
                    return $container->Singletons["Session"];
                }
            });
        }

        /**
         * Here specify router settings e.g add routes
         *
         * @param Router $router Router
         * @return Router Set router
         */
        public function SetupRouter(Router $router) : Router
        {
            // Here setup your router:
            $router->SetHomepage(["controller" => new App\Main\Controllers\HomeController(), "action" => "DefaultAction"]);
            $router->AddRoute("/session/{string}", ["controller" => new \App\Main\Controllers\HomeController(), "action" => "SetSessionAction"]);
            /*$router->AddRoute("controller/user/{1-100}", ["controller" => "Controller", "action" => "UserAction"]);
            $router->AddRoute("controller/user/{int:admin}", ["controller" => "Controller", "action" => "UserAction"]);
            $router->AddRoute("article/{en|fr|de}", ["controller" => "Article", "action" => "Read"]);*/

            return $router;
        }
    }