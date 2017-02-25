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

/**
     * {Description}.
     */
    final class ContainerSingleton
    {
        protected static $container;

        public static function getContainer()
        {
            if (isset(self::$container)) {
                return self::$container;
            } else {
                self::$container = new Container();

                return self::$container;
            }
        }
    }
