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

namespace Phlamingo\Tests\Core\Mvc;

use Phlamingo\Core\MVC\Router;

    class RouterTest extends \PHPUnit_Framework_TestCase
    {
        protected $Router;

        public function setUp()
        {
            $this->Router = new Router();
            $this->Router->AddRoute('/route1', ['controller' => 'controller', 'action' => 'action']);
            $this->Router->AddRoute('/route2/route2', ['controller' => 'controller', 'action' => 'action']);
            $this->Router->AddRoute('/route3/{string}', ['controller' => 'controller', 'action' => 'action']);
            $this->Router->AddRoute('/route4/{int}', ['controller' => 'controller', 'action' => 'action']);
            $this->Router->AddRoute('/route5/{en|fr}', ['controller' => 'controller', 'action' => 'action']);
            $this->Router->AddRoute('/route6/{string:default}', ['controller' => 'controller', 'action' => 'action']);
            $this->Router->AddRoute('/route7/{int|default}', ['controller' => 'controller', 'action' => 'action']);
        }

        public function testRoutes()
        {
            $this->assertEquals(
                ['controller' => 'controller', 'action' => 'action', 'params' => []],
                $this->Router->Route('/route1')
            );
            $this->assertEquals(
                ['controller' => 'controller', 'action' => 'action', 'params' => []],
                $this->Router->Route('/route2/route2')
            );

            $this->assertEquals(
                ['controller' => 'controller', 'action' => 'action', 'params' => ['dsadasdasdsa']],
                $this->Router->Route('/route3/dsadasdasdsa')
            );

            try {
                $this->assertEquals(
                    ['controller' => 'controller', 'action' => 'action', 'params' => []],
                    $this->Router->Route('/route5/')
                );
                $this->fail('Exception expected');
            } catch (\Exception $e) {
            }

            $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => [548]], $this->Router->Route('/route3/548'));

            $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => [598]], $this->Router->Route('/route4/598'));
            try {
                $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => ['dsdsds']], $this->Router->Route('/route4/dsdsds'));
                $this->fail('Exception expected');
            } catch (\Exception $e) {
            }

            $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => ['en']], $this->Router->Route('/route5/en'));
            $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => ['fr']], $this->Router->Route('/route5/fr'));

            try {
                $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => ['dssd']], $this->Router->Route('/route5/dssd'));
                $this->fail('Exception expected');
            } catch (\Exception $e) {
            }

            $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => ['default']], $this->Router->Route('/route6'));
            $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => ['dasdsasda']], $this->Router->Route('/route6/dasdsasda'));
            $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => [584]], $this->Router->Route('/route7/584'));
            $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => ['default']], $this->Router->Route('/route7/default'));
        }

        public function testRoute7()
        {
            try {
                $this->assertEquals(['controller' => 'controller', 'action' => 'action', 'params' => ['dsdsasd']], $this->Router->Route('/route7/dsdsasd'));
                $this->fail('Exception expected');
            } catch (\Exception $e) {
            }
        }
    }
