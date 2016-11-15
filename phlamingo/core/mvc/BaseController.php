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
    use Phlamingo\Di\Container;
    use Phlamingo\HTTP\Request;


    /**
     * BaseController is parent of all controllers in
     * application and is providing its base logic
     */
    abstract class BaseController extends Object
    {
        /**
         * @Service request
         */
        public $Request;

        public function BeforeAction()
        {

        }

        public function AfterAction()
        {

        }

        public final function Run(string $action, ...$params)
        {

        }
    }