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

use Phlamingo\Config\Configurator;
    use Phlamingo\Core\MVC\BaseController;
    use Phlamingo\Core\MVC\Router;
    use Phlamingo\Di\Container;
    use Phlamingo\Di\ContainerSingleton;

    /**
     * Abstract interface for Application class.
     */
    abstract class ApplicationAbstract
    {
        /**
         * Implements application running logic.
         *
         * @param BaseController $controller       Requested controller
         * @param string         $controllerAction Name of requested action
         * @param array|mixed    $params           Params of controller action
         */
        abstract public function Main(BaseController $controller, string $controllerAction, ...$params);

        /**
         * Routes URL and calls Main().
         *
         * @param Router $router Router
         */
        final public function CallMain(Router $router)
        {
            $container = ContainerSingleton::getContainer();
            $request = $container->get('Request');
            $event = $router->route($request);
            $this->Main(new $event['controller'](), $event['action'], ...$event['params']);
        }

        /**
         * Implements configuration logic.
         *
         * @param Configurator $config Config class
         *
         * @return Configurator Set config class
         */
        abstract public function Config(Configurator $config) : Configurator;

        abstract public function SetupDI(Container $container);

        /**
         * Called before Config(), implements default configuration of application.
         *
         * @return Configurator Configuration class
         */
        final public function AbstractConfig() : Configurator
        {
            return new Configurator();
        }
    }
