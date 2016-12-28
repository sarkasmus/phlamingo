<?php

    /**
     * @author Michal Doubek
     * @copyright (c) 2016 Michal Doubek
     */

    namespace App\Main\Controllers;
    use Phlamingo\Cache\ApplicationCachers\DICacher;
    use Phlamingo\Cache\Cache;
    use Phlamingo\Core\MVC\BaseController;
    use Phlamingo\HTTP\Response;
    use Phlamingo\HTTP\Sessions\SessionSection;


    /**
     * {Description}
     */
    class HomeController extends BaseController
    {
        /**
         * @Persist
         * @View\Var
         * @Form
         * @Form\Values
         * @Form\Input(form.name)
         *
         * @View\Getter
         */

        /**
         * Persist
         */
        protected $persist;

        public function DefaultAction()
        {
            $cache = new Cache("TestingCache");
            return $cache->Content;
        }

        public function SetSessionAction(string $value)
        {
            $this->Session->var = $value;
            $this->Session->Save();
            return $value;
        }

        public function RegenerateID()
        {
            $cache = new Cache("TestingCache", "Content");
            $cache->Save();
            return "ok";
        }

        public function Rename(string $variableOrNewSectionName, string $newName)
        {
            $this->Session->Rename($variableOrNewSectionName, $newName);
            $this->Session->Save();
            return "ok";
        }

        public function Move(string $section, string $variable)
        {
            $this->Session->AddSection($section);
            $this->Session->Move($this->Session->$section, $variable);
            $this->Save();
        }

        public function Clear(string $variable)
        {
            $this->Session->Clear($variable);
            $this->Session->Save();
        }

        public function Lock(string $variable)
        {
            $this->Session->Lock($variable);
            $this->Session->Save();
        }

        public function Unlock(string $variable)
        {
            $this->Session->Unlock($variable);
            $this->Session->Save();
        }

        public function Expiration($variableOrSectionExpiration, $expiration)
        {
            $this->Session->Expiration($variableOrSectionExpiration, $expiration);
            $this->Session->Save();
            return "OK";
        }

        public function VarExpiration(string $name)
        {
            return $this->Session->VarExpiration($name);
        }
    }