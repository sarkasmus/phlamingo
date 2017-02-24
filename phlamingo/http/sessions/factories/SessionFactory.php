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

namespace Phlamingo\HTTP\Sessions\Factories;

use Phlamingo\Di\BaseFactory;
    use Phlamingo\HTTP\Sessions\Exceptions\SessionException;
    use Phlamingo\HTTP\Sessions\Session;

    /**
     * {Description}.
     *
     * @Factory Session
     */
    class SessionFactory extends BaseFactory
    {
        public function make()
        {
            $request = $this->container->Get('Request');

            if (isset($this->singleton)) {
                return $this->singleton;
            } elseif (isset($request->Cookies['SessID'])) {
                try {
                    $this->singleton = new Session($request->Cookies['SessID']);

                    return $this->singleton;
                } catch (SessionException $e) {
                    $this->singleton = new Session();

                    return $this->singleton;
                }
            } else {
                $this->singleton = new Session();

                return $this->singleton;
            }
        }
    }
