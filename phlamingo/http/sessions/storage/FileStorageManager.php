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
        public function Save(Session $session)
        {
            $sections = $session->Sections;
            $sections["default_session"] = $session;

            $result = [
                "_session" => [
                    "id" => $session->SessionID,
                    "time" => time(),
                    "expiration" => $session->Expiration
                ]
            ];

            foreach ($sections as $key => $section)
            {
                $name = $section->Name;
                $locked = $section->IsLocked();
                $variables = $section->Variables;
                $lockedVariables = $section->LockedVariables;
                $expiration = $section->Expiration;
                $expirations = $section->Expirations;
                $saveModes = $section->SaveModes;

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

            $fileName = TEMP . "/sess" . $session->SessionID;
            file_put_contents($fileName, $json);
        }

        public function Pull($sessionID)
        {
            if (file_exists(TEMP . "/sess" . $sessionID))
            {
                $content = file_get_contents(TEMP . "/sess" . $sessionID);
                $content = json_decode($content, true);
                return $content;
            }

            return false;
        }

        public function Destroy(Session $session)
        {
            if (file_exists(TEMP . "/sess" . $session->SessionID))
            {
                unlink(TEMP . "/sess" . $session->SessionID);
            }
        }

        public function RegenerateID(Session $session, string $newSessionId)
        {
            if (file_exists(TEMP . "/sess" . $session->SessionID))
            {
                rename(TEMP . "/sess" . $session->SessionID, TEMP . "/sess" . $newSessionId);
            }
        }
    }