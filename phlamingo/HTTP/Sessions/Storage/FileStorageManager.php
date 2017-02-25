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
    class FileStorageManager extends BaseStorageManager
    {
        public function save(Session $session)
        {
            $sections = $session->sections;
            $sections["default_session"] = $session;

            $result = [
                "_session" => [
                    "id" => $session->sessionID,
                    "time" => time(),
                    "expiration" => $session->expiration
                ]
            ];

            foreach ($sections as $key => $section)
            {
                $name = $section->name;
                $locked = $section->isLocked();
                $variables = $section->variables;
                $lockedVariables = $section->lockedVariables;
                $expiration = $section->expiration;
                $expirations = $section->expirations;
                $saveModes = $section->saveModes;

                if (isset($saveModes))
                {
                    foreach ($saveModes as $smkey => $saveMode)
                    {
                        $saveModes[$smkey] = get_class($saveMode);
                    }
                }

                $result[$key] = [
                    "name" => $name,
                    "locked" => $locked,
                    "variables" => $variables,
                    "lockedVariables" => $lockedVariables,
                    "expiration" => $expiration,
                    "expirations" => $expirations
                ];
            }

            $json = json_encode($result);

            $fileName = TEMP . "/sess" . $session->sessionID;
            file_put_contents($fileName, $json);
        }

        public function pull($sessionID)
        {
            if (file_exists(TEMP . "/sess" . $sessionID))
            {
                $content = file_get_contents(TEMP . "/sess" . $sessionID);
                $content = json_decode($content, true);
                return $content;
            }

            return false;
        }

        public function destroy(Session $session)
        {
            if (file_exists(TEMP . "/sess" . $session->sessionID))
            {
                unlink(TEMP . "/sess" . $session->sessionID);
            }
        }

        public function regenerateID(Session $session, string $newSessionId)
        {
            if (file_exists(TEMP . "/sess" . $session->sessionID))
            {
                rename(TEMP . "/sess" . $session->sessionID, TEMP . "/sess" . $newSessionId);
            }
        }

        public function getIterator() : int
        {
            $iterator = (int)file_get_contents(TEMP . "/sessIterator") + 1;
            file_put_contents(TEMP . "/sessIterator", $iterator);
            return $iterator;
        }
    }