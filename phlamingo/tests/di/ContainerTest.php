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

namespace Phlamingo\Tests\Di;

use Phlamingo\Di\BaseFactory;
    use Phlamingo\Di\Container;

    class Dependency
    {
        public $var = 15;
    }

    class TestingService
    {
        public $dependency;

        public function __construct($dependency)
        {
            $this->dependency = $dependency;
        }

        public $var;
    }

    class TestingServiceFactory extends BaseFactory
    {
        public function make()
        {
            $service = new TestingService($this->container->Get('service1'));

            return $service;
        }
    }

    class ContainerTest extends \PHPUnit_Framework_TestCase
    {
        protected $Container;

        public function setUp()
        {
            $this->Container = new Container();
            $this->Container->addService('service1', function () {
                return new Dependency();
            });
            $this->Container->addService('service2', new TestingServiceFactory());

            $this->Container->addAlias('service2', 'alias');
        }

        public function testServices()
        {
            $this->assertEquals(new Dependency(), $this->Container->Get('service1'));
            $this->assertEquals(new TestingService(new Dependency()), $this->Container->Get('service2'));

            $this->assertEquals(new TestingService(new Dependency()), $this->Container->Get('alias'));
        }
    }
