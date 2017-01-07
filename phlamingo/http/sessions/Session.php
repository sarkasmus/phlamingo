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

    namespace Phlamingo\HTTP\Sessions;

    use Phlamingo\HTTP\Sessions\Exceptions\SessionException;
    use Phlamingo\HTTP\Sessions\Storage\BaseStorageManager;
    use Phlamingo\HTTP\Sessions\Storage\FileStorageManager;


    /**
     * {Description}
     *
     * @Service Session
     */
    class Session extends SessionSection
    {
        /**
         * Session ID of session relation
         * @var string $SessionID
         */
        protected $SessionID;

        /**
         * StorageManager object to control saving, pulling and destroying session data
         * @var BaseStorageManager $StorageManager
         */
        protected $StorageManager;

        /**
         * List of sections in sessions
         * @var array $Sections
         */
        protected $Sections = [];

        /**
         * Constructor
         *
         * @param string|null $sessionID Session ID of relation - if null is set new session is generated with unique session id
         * @param BaseStorageManager|null $storageManager StorageManager
         */
        public function __construct($sessionID = null, BaseStorageManager $storageManager = null)
        {
            parent::__construct("Session");
            if ($storageManager === null)
            {
                $storageManager = new FileStorageManager();
            }

            $this->SessionID = $sessionID;
            $this->StorageManager = $storageManager;

            if ($sessionID === null)
            {
                $this->RegenerateSessionID();
            }
            else
            {
                $this->Pull();
            }
        }

        /**
         * Regenerates session ID of current session
         */
        public function RegenerateSessionID()
        {
            $iterator = $this->StorageManager->GetIterator();
            $this->StorageManager->Destroy($this);
            $this->SessionID = hash("sha256", $_SERVER['REMOTE_ADDR'] . $iterator);
            if (!headers_sent())
            {
                setcookie(
                    "SessID",
                    $this->SessionID,
                    time() + 8600 * 14,
                    "/",
                    DOMAIN,
                    false,
                    true
                );
            }
        }

        /**
         * Pulls data from storage manager for current session relation (with session id)
         */
        public function Pull()
        {
            if (($pulledContent = $this->StorageManager->Pull($this->SessionID)) !== false)
            {

                $default = $pulledContent["default_session"];

                $this->Expiration($default["expiration"]);

                if ($default["locked"])
                {
                    $this->Lock();
                }

                foreach ($default["variables"] as $key => $variable)
                {
                    $this->$key = $variable;
                }

                foreach ($default["lockedVariables"] as $key => $variable)
                {
                    $this->Lock($key);
                }

                foreach ($default["expirations"] as $key => $variable)
                {
                    $this->Expiration("key", $variable);
                }

                unset($pulledContent["_session"], $pulledContent["default_session"]);
                foreach ($pulledContent as $section)
                {
                    $name = $section["name"];
                    $this->AddSection($name);

                    $this->Sections[$name]->Expiration($section["expiration"]);

                    if ($section["locked"])
                    {
                        $this->Sections[$name]->Lock();
                    }

                    foreach ($section["variables"] as $key => $variable)
                    {
                        $this->Sections[$name]->$key = $variable;
                    }

                    foreach ($section["lockedVariables"] as $key => $variable)
                    {
                        $this->Sections[$name]->Lock($key);
                    }

                    foreach ($section["expirations"] as $key => $variable)
                    {
                        $this->Sections[$name]->Expiration("key", $variable);
                    }
                }
            }
            else
            {
                throw new SessionException("Session ID {$this->SessionID} doesn't exists");
            }
        }

        /**
         * Destroys session
         */
        public function Destroy()
        {
            $this->StorageManager->Destroy($this);
        }

        /**
         * Adds section to section list
         *
         * @param string $name New section name
         * @throws SessionException When section name already exists
         * @throws \InvalidArgumentException When string is empty
         */
        public function AddSection(string $name)
        {
            if (!empty($name))
            {
                if (!isset($this->Sections[$name]))
                {
                    $this->Sections[$name] = new SessionSection($name);
                }
                else
                {
                    throw new SessionException("Session section with name {$name} already exists and can!'t be added");
                }
            }
            else
            {
                throw new \InvalidArgumentException("Session::AddSection expects section name to not be empty");
            }
        }

        /**
         * Returns section or calls parent implementation of  magic function get
         *
         * @param string $name Name of the inaccessible property
         * @return SessionSection|mixed Section or returned value of parent function
         * @throws SessionException When section is not defined and parent call failed
         */
        public function __get(string $name)
        {
            if (isset($this->Sections[$name]))
            {
                return $this->Sections[$name];
            }
            else
            {
                try
                {
                    return parent::__get($name);
                }
                catch (SessionException $e)
                {
                    throw new SessionException("In session is not defined section or variable with name {$name}");
                }
            }
        }

        /**
         * Saves session relation by storage manager
         */
        public function Save()
        {
            $this->StorageManager->Save($this);
        }

        /**
         * Getter for property SessionID
         *
         * @return string SessionID
         */
        public function getSessionID() : ?string
        {
            return $this->SessionID;
        }

        /**
         * Setter for property StorageManager
         *
         * @param BaseStorageManager $storageManager Storage manager object
         */
        public function setStorageManager(BaseStorageManager $storageManager)
        {
            $this->StorageManager = $storageManager;
        }

        /**
         * Getter for property StorageManager
         *
         * @return BaseStorageManager StorageManager
         */
        public function getStorageManager() : BaseStorageManager
        {
            return $this->StorageManager;
        }

        /**
         * Getter for property Sections
         *
         * @return array Sections
         */
        public function getSections() : array
        {
            return $this->Sections;
        }

        /**
         * Destructor - automatic saves session
         */
        public function __destruct()
        {
            $this->Save();
        }
    }
