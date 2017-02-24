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

namespace Phlamingo\Nebula\ExpLang\Cache\Factories;

use Phlamingo\Di\BaseFactory;
    use Phlamingo\Nebula\ExpLang\Cache\MacroCacher;

    /**
     * {Description}.
     *
     * @Factory MacroCacher
     */
    class MacroCacherFactory extends BaseFactory
    {
        public function Make()
        {
            return new MacroCacher();
        }
    }
