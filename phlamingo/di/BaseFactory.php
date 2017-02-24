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

use DocBlockReader\Reader;
    use Phlamingo\Di\Exceptions\DIContainerException;

    /**
     * {Description}.
     */
    abstract class BaseFactory
    {
        protected $singleton = null;
        public $container;

        abstract public function make();

        public function __invoke()
        {
            $comment = new Reader(get_class($this));
            $comment = $comment->getParameter('Singleton');

            if ($comment == true) {
                if (isset($this->singleton)) {
                    return $this->singleton;
                } else {
                    $this->singleton = $this->make();

                    return $this->singleton;
                }
            }

            if (is_object($this->make())) {
                return $this->make();
            } else {
                throw new DIContainerException('Factory '.self::class." doesn't return object");
            }
        }
    }
