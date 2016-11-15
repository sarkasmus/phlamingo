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

    namespace Phlamingo\Core;

    use Phlamingo\Config\Config;
    use Phlamingo\Core\MVC\BaseController;
    use Phlamingo\Core\MVC\Router;
    use Phlamingo\Di\Container;
    use Phlamingo\HTTP\Request;


    /**
     * Abstract interface for Application class
     */
    abstract class ApplicationAbstract
    {
        /**
         * Implements application running logic
         *
         * @param BaseController $controller Requested controller
         * @param string $controllerAction Name of requested action
         * @param array|mixed $params Params of controller action
         */
        public abstract function Main(BaseController $controller, string $controllerAction, ...$params);

        /**
         * Routes URL and calls Main()
         *
         * @param Router $router Router
         */
        public final function CallMain(Router $router)
        {
            $container = new Container();
            $request = $container->Get("request");
            $event = $router->Route($request);
            $this->Main($event['controller'], $event['action'], ...$event['params']);
        }

        /**
         * Implements configuration logic
         *
         * @param Config $config Config class
         * @return Config Set config class
         */
        public abstract function Config(Config $config) : Config;

        /**
         * Sets up a router
         *
         * @param Router $router Router
         * @return Router Set router
         */
        public abstract function SetupRouter(Router $router) : Router;

        public abstract function SetupDI(Container $container);

        /**
         * Called before SetupRouter(), implements default settings of router
         *
         * @return Router Created router
         */
        public final function AbstractSetupRouter() : Router
        {
            return new Router();
        }

        /**
         * Called before Config(), implements default configuration of application
         *
         * @return Config Configuration class
         */
        public final function AbstractConfig() : Config
        {
            return new Config();
        }
    }