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

namespace Phlamingo\Core\MVC\Factories;

use Phlamingo\Core\MVC\Router;
    use Phlamingo\Di\BaseFactory;

    /**
     * {Description}.
     *
     * @Factory Router
     */
    class RouterFactory extends BaseFactory
    {
        public function Make()
        {
            if (isset($this->Singleton)) {
                return $this->Singleton;
            } else {
                $routerCacher = new \Phlamingo\Cache\ApplicationCachers\RouterCacher();
                $router = new Router();


                if ($routerCacher->cached())
                {
                    foreach ($routerCacher->get() as $mask => $event)
                    {
                        if ($mask !== null and $event !== null) {
                            $router->AddRoute($mask, $event);
                        }
                    }

                }
                else
                {
                    $routerCacher->cache();
                    foreach ($routerCacher->get() as $mask => $event)
                    {
                        if ($mask !== null and $event !== null) {
                            $router->AddRoute($mask, $event);
                        }
                    }
                }

                $this->Singleton = $router;

                return $router;
            }
        }
    }
