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
        public function Make()
        {
            $request = $this->Container->Get('Request');
            if (isset($this->Container->Singletons['Session'])) {
                return $this->Container->Singletons['Session'];
            } elseif (isset($request->Cookies['SessID'])) {
                try {
                    $this->Container->Singletons['Session'] = new Session($request->Cookies['SessID']);

                    return $this->Container->Singletons['Session'];
                } catch (SessionException $e) {
                    $this->Container->Singletons['Session'] = new Session();

                    return $this->Container->Singletons['Session'];
                }
            } else {
                $this->Container->Singletons['Session'] = new Session();

                return $this->Container->Singletons['Session'];
            }
        }
    }
