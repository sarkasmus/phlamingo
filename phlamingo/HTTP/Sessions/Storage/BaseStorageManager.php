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
        public abstract function pull($sessionID);
        public abstract function save(Session $session);
        public abstract function destroy(Session $session);
        public abstract function regenerateID(Session $session, string $newSessionId);
        public abstract function getIterator() : int;
    }