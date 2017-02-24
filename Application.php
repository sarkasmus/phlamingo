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
    use Phlamingo\Config\Configurator;
    use Phlamingo\Core\MVC\BaseController;
    use Phlamingo\Di\Container;
    use Phlamingo\HTTP\Request;

    /**
     * Application manages all actions to setup environment and
     * proccess the user request.
     */
    class Application extends \Phlamingo\Core\ApplicationAbstract
    {
        /**
         * Here write code to run your app.
         *
         * @param BaseController $controller       Requested controller
         * @param string         $controllerAction Name of requested action
         * @param array|mixed    $params           Params of controller action
         */
        public function Main(BaseController $controller, string $controllerAction, ...$params)
        {
            // Here write your code:

            // Runs controller
            $response = $controller->run($controllerAction, ...$params);
            $response->Send();
        }

        /**
         * Here write your config code before app will start.
         *
         * @param Configurator $config Config class
         *
         * @return Configurator Set config class
         */
        public function Config(Configurator $config) : Configurator
        {
            // Here setup your app:
            $configCacher = new \Phlamingo\Cache\ApplicationCachers\ConfigCacher();


            if ($configCacher->cached())
            {
                foreach ($configCacher->get() as $key => $values)
                {
                    if ($values !== null and $key !== null) {
                        $config->Push($values, $key);

                    }
                }

            }
            else
            {
                $configCacher->cache();
                foreach ($configCacher->get() as $key => $values)
                {
                    if ($values !== null and $key !== null) {
                        $config->Push($values, $key);

                    }
                }
            }

            return $config;
        }

        public function SetupDI(Container $container)
        {
            $diCacher = new \Phlamingo\Cache\ApplicationCachers\DICacher();


            if ($diCacher->cached())
            {
                foreach ($diCacher->get() as $service => $factory)
                {
                    if ($service !== null and $factory !== null)
                        $container->AddService($service, new $factory);
                }
            }
            else
            {
                $diCacher->cache();
                foreach ($diCacher->get() as $service => $factory)
                {
                    if ($service !== null and $factory !== null) {
                        $container->AddService($service, new $factory);

                    }
                }
            }
        }
    }
