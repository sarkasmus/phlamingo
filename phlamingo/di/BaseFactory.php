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

    namespace Phlamingo\Di;
    use Phlamingo\Di\Exceptions\DIContainerException;


    /**
     * {Description}
     */
    abstract class BaseFactory
    {
        protected $Singleton = null;
        public $Container;

        public abstract function Make();

        public function __invoke()
        {
            if (is_object($this->Make()))
            {
                return $this->Make();
            }
            else
            {
                throw new DIContainerException("Factory " . self::class . " doesn't return object");
            }
        }
    }