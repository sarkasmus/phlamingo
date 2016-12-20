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

    namespace Phlamingo\HTTP\Sessions\Storage;

    use Phlamingo\HTTP\Sessions\Session;


    /**
     * {Description}
     */
    abstract class BaseStorageManager
    {
        public abstract function Pull($sessionID);
        public abstract function Save(Session $session);
        public abstract function Destroy(Session $session);
        public abstract function RegenerateID(Session $session, string $newSessionId);
    }