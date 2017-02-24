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

use Phlamingo\Core\Object;
    use Phlamingo\HTTP\Sessions\Exceptions\SessionException;
    use Phlamingo\HTTP\Sessions\SaveModes\BaseSaveMode;

    /**
     * {Description}.
     */
    class SessionSection extends Object implements \IteratorAggregate
    {
        /**
         * If section is locked to write.
         *
         * @var bool
         */
        protected $Locked = false;

        /**
         * List of locked variable names.
         *
         * @var array
         */
        protected $LockedVariables = [];

        /**
         * Name of the section.
         *
         * @var string
         */
        protected $Name;

        /**
         * Expiration of section in seconds.
         *
         * @var int
         */
        protected $Expiration = 0;

        /**
         * List of variables and it`s values (name => value).
         *
         * @var array
         */
        protected $Variables = [];

        /**
         * List of setters (variable => callable setter).
         *
         * @var array
         */
        protected $Setters = [];

        /**
         * List of getters (variable =. callable getter).
         *
         * @var array
         */
        protected $Getters = [];

        /**
         * List of variables expiration (variable => expiration).
         *
         * @var array
         */
        protected $Expirations = [];

        /**
         * List of save mode algorythms.
         *
         * @var array
         */
        protected $SaveModes = [];

        /**
         * Constructor.
         *
         * @param string $name Name of the section
         */
        public function __construct(string $name)
        {
            parent::__construct();
            $this->Name = $name;
        }

        /**
         * Implementation of IteratorAggregate.
         *
         * @return array Variables
         */
        public function getIterator()
        {
            return $this->Variables;
        }

        /**
         * Returns variable with name $name accessed as class property.
         *
         * Calls parent implementation (Core\Object) if variable is not defined in section
         *
         * @param string $name Name of variable
         *
         * @throws SessionException When variable is not defined or if
         *                          parent implementation of getter throws MemberAccessException
         *
         * @return string|mixed Variable value
         */
        public function __get(string $name)
        {
            if (isset($this->Variables[$name])) {
                if (isset($this->Getters[$name])) {
                    // Call getter if its defined
                    return $this->Getters[$name]($this->Variables[$name]);
                } else {
                    return $this->Variables[$name];
                }
            } else {
                // If Variable is not defined calls parent (Core\Object - properties) if Object throws exception
                // then __get throws SessionException
                try {
                    return parent::__get($name);
                } catch (\Exception $e) {
                    throw new SessionException("Session variable {$name} is not defined in section {$this->Name}");
                }
            }
        }

        /**
         * Sets variable with name $name to value $value accessed as class property.
         *
         * Calls parent implementation (Core\Object) if variable is not defined in section
         *
         * @param string       $name  Name of variable
         * @param string|mixed $value Value to set
         *
         * @throws SessionException When variable is not defined or if
         *                          parent implementation of setter throws MemberAccessException
         */
        public function __set(string $name, $value)
        {
            // If section or variable is locked then variable is not accessible for write
            if ($this->Locked === false and in_array($name, $this->LockedVariables) === false) {
                try {
                    parent::__set($name, $value);
                } catch (\Exception $e) {
                    if (isset($this->Setters[$name])) {
                        $this->Variables[$name] = $this->Setters[$name]($this->Variables[$name], $value);
                    } else {
                        $this->Variables[$name] = $value;
                    }
                }
            } else {
                throw new SessionException("Can't write to session variable {$name} because section {$this->Name} is locked");
            }
        }

        /**
         * Renames variable or whole section.
         *
         * Overridden:
         * Rename("var1", "var2");
         * Rename("Section");
         *
         * @param string      $variableOrNewSectionName Variable to rename or new section name
         * @param string|null $newName                  New variable name or null if section is renamed
         *
         * @throws \InvalidArgumentException When section name is empty string
         * @throws SessionException          When session variable is not defined
         */
        public function Rename(string $variableOrNewSectionName, string $newName = null)
        {
            // Override case: Rename(string $newSectionName) - renaming current section
            if ($newName === null) {
                if (!empty($variableOrNewSectionName)) {
                    $this->Name = $variableOrNewSectionName;
                } else {
                    throw new \InvalidArgumentException("New section name {$variableOrNewSectionName} is empty and section can't be renamed");
                }
            } else { // Override case Rename(string $variable, string $newName) - renaming variable
                if (isset($this->Variables[$variableOrNewSectionName])) {
                    $this->Variables[$newName] = $this->Variables[$variableOrNewSectionName];
                    unset($this->Variables[$variableOrNewSectionName]);
                } else {
                    throw new SessionException("Session variable {$variableOrNewSectionName} is not defined and can't be renamed");
                }
            }
        }

        /**
         * Moves variable or all variables from current section to another section.
         *
         * Overridden:
         * Move($Session->Section, "var") - moves variable var to section
         * Move($Session->Section) - moves all variables to section
         *
         * @param SessionSection $section  Reference to the section for moving the variable to
         * @param string|null    $variable Name of the variable to be moved
         *
         * @throws SessionException
         */
        public function Move(SessionSection &$section, string $variable = null)
        {
            // If variable name is set
            if ($variable !== null) {
                // If variable is defined in section
                if (isset($this->Variables[$variable])) {
                    if (!isset($section->$variable)) {
                        $section->$variable = $this->Variables[$variable];
                        unset($this->Variables[$variable]);
                    } else {
                        // When variable name is defined in getting section variable from current section can`t be moved
                        throw new SessionException("Session variable name conflict occured: variable {$variable} can't be moved to section {$section->Name} because variable with this name alerady exists");
                    }
                } else {
                    throw new SessionException("Variable {$variable} is not defined in section {$this->Name} and can't be moved to section {$section->Name}");
                }
            }
            // If variable name is not set - moving all variables to section
            else {
                // Variables are preprocessed and checked if getting section has not variable with same name
                $moving = [];
                foreach ($this->Variables as $key => $variable) {
                    if (isset($section->$key)) {
                        // When variable name is defined in getting section variable from current section can`t be moved
                        throw new SessionException("Session variable name conflict occurred: variable {$key} can't be moved to section {$section->Name} because variable with this name alerady exists");
                    }

                    $moving[$key] = $variable;
                }

                // Moving variables if no conflicts occurred
                foreach ($moving as $key => $value) {
                    $section->$key = $value;
                }
            }
        }

        /**
         * Clears variable $variable or whole section (sets variable(s) to null and these variables will not be accessible)
         * Because isset() returns false when isset(null).
         *
         * @param string|null $variable Name of the variable to clear
         *
         * @throws SessionException When variable name is not defined
         */
        public function Clear(string $variable = null)
        {
            if ($variable !== null) {
                if (isset($this->Variables[$variable])) {
                    $this->Variables[$variable] = null;
                } else {
                    throw new SessionException("Session variable {$variable} is not defined and can't be cleared");
                }
            } else {
                foreach ($this->Variables as $key => $variable) {
                    $this->Variables[$key] = null;
                }
            }
        }

        /**
         * Locks variable $variable or whole section (locks it for write).
         *
         * @param string|null $variable Variable name
         *
         * @throws SessionException When variable is not defined
         * @throws SessionException When variable is already locked
         * @throws SessionException When section is already locked
         */
        public function Lock(string $variable = null)
        {
            if ($variable !== null) {
                if (!in_array($variable, $this->LockedVariables)) {
                    if (isset($this->Variables[$variable])) {
                        $this->LockedVariables[] = $variable;
                    } else {
                        throw new SessionException("Can't lock session variable {$variable} in section {$this->Name} because it is not defined");
                    }
                } else {
                    throw new SessionException("Can't lock session variable {$variable} in section {$this->Name} because it is already locked");
                }
            } else {
                if ($this->Locked === false) {
                    $this->Locked = true;
                } else {
                    throw new SessionException("Can't lock section {$this->Name} because it is already locked");
                }
            }
        }

        /**
         * Returns if variable or section is locked.
         *
         * @param string|null $variable Variable name
         *
         * @throws SessionException When session variable is not defined
         *
         * @return bool Locked
         */
        public function IsLocked(string $variable = null) : bool
        {
            if ($variable === null) {
                return $this->Locked;
            } else {
                if (in_array($variable, $this->LockedVariables)) {
                    return true;
                } else {
                    if (isset($this->Variables[$variable])) {
                        return false;
                    } else {
                        throw new SessionException("Session variable {$variable} is not defined in section {$this->Name}");
                    }
                }
            }
        }

        /**
         * Unlocks variable or section.
         *
         * @param string|null $variable Variable to unlock
         *
         * @throws SessionException When variable or section is not locked and can`t be unlocked
         */
        public function Unlock(string $variable = null)
        {
            if ($variable !== null) {
                if (in_array($variable, $this->LockedVariables)) {
                    if (($key = array_search($variable, $this->LockedVariables)) !== false) {
                        unset($this->LockedVariables[$key]);
                    }
                } else {
                    throw new SessionException("Can't unlock session variable {$variable} in section {$this->Name} because it is not locked");
                }
            } else {
                if ($this->Locked === true) {
                    $this->Locked = false;
                } else {
                    throw new SessionException("Can't unlock section {$this->Name} because it is not locked");
                }
            }
        }

        /**
         * Sets a setter for variable or all variables in section.
         *
         * @param string|callable $variableOrSectionSetter Variable name or Setter for whole section
         * @param callable|null   $setter                  Setter or null
         *
         * @throws SessionException          When variable is not defined
         * @throws \InvalidArgumentException When $variableOrSectionSetter is not callable
         */
        public function Setter($variableOrSectionSetter, callable $setter = null)
        {
            if ($setter !== null) {
                if (isset($this->Variables[$variableOrSectionSetter])) {
                    $this->Setters[$variableOrSectionSetter] = $setter;
                } else {
                    throw new SessionException("Session variable {$variableOrSectionSetter} in section {$this->Name} is not defined");
                }
            } elseif (is_callable($variableOrSectionSetter)) {
                foreach ($this->Setters as $variable => $value) {
                    $this->Setters[$variable] = $variableOrSectionSetter;
                }
            } else {
                throw new \InvalidArgumentException('SessionSection::Setter requires first parameter type callable when second is not given');
            }
        }

        /**
         * Sets a getter for variable or all variables in section.
         *
         * @param string|callable $variableOrSectionGetter Variable name or Getter for whole section
         * @param callable|null   $getter                  Getter or null
         *
         * @throws SessionException          When variable is not defined
         * @throws \InvalidArgumentException When $variableOrSectionGetter is not callable
         */
        public function Getter($variableOrSectionGetter, callable $getter = null)
        {
            if ($getter !== null) {
                if (isset($this->Variables[$variableOrSectionGetter])) {
                    $this->Getters[$variableOrSectionGetter] = $getter;
                } else {
                    throw new SessionException("Session variable {$variableOrSectionGetter} in section {$this->Name} is not defined");
                }
            } elseif (is_callable($variableOrSectionGetter)) {
                foreach ($this->Getters as $variable => $value) {
                    $this->Getters[$variable] = $variableOrSectionGetter;
                }
            } else {
                throw new \InvalidArgumentException('SessionSection::Getter requires first parameter type callable when second is not given');
            }
        }

        /**
         * Sets a expiration for variable or section.
         *
         * @param string|int      $variableOrSectionExpiration Variable name or section expiration
         * @param int|string|null $expiration                  Expiration of variable   [optional]
         *
         * @throws SessionException          When session variable is not defined
         * @throws \InvalidArgumentException When time expression is invalid
         */
        public function Expiration($variableOrSectionExpiration, $expiration = null)
        {
            if ($expiration !== null) {
                if (isset($this->Variables[$variableOrSectionExpiration])) {
                    if ($this->IsTimeExpressionValid($expiration)) {
                        $this->Expirations[$variableOrSectionExpiration] = $this->ParseExpiration($expiration);
                    } else {
                        throw new \InvalidArgumentException("Time expression {$expiration} is invalid");
                    }
                } else {
                    throw new SessionException("Session variable {$variableOrSectionExpiration} in section {$this->Name} is not defined");
                }
            } elseif ($this->IsTimeExpressionValid($variableOrSectionExpiration) === true) {
                $this->Expiration = $this->ParseExpiration($variableOrSectionExpiration);
            } else {
                throw new \InvalidArgumentException("Time expression {$variableOrSectionExpiration} is invalid");
            }
        }

        /**
         * Returns expiration of variable.
         *
         * @param string $name Variable name
         *
         * @throws SessionException When variable $name is not defined
         *
         * @return int Expiration in seconds
         */
        public function VarExpiration(string $name) : int
        {
            if (isset($this->Variables[$name])) {
                if (isset($this->Expirations[$name])) {
                    return $this->Expirations[$name];
                } else {
                    return 0;
                }
            } else {
                throw new SessionException("Session variable {$name} in section {$this->Name} is not defined");
            }
        }

        public function AddSaveMode(BaseSaveMode $saveMode)
        {
            $this->SaveModes[] = $saveMode;
        }

        public function CallSaveModes()
        {
        }

        public function RemoveSaveMode(string $saveModeType)
        {
            foreach ($this->SaveModes as $key => $saveMode) {
                if (get_class($saveMode) == $saveModeType) {
                    unset($this->SaveModes[$key]);
                }
            }
        }

        /**
         * Returns if time expression is valid (1 day, 12 months, next monday etc.).
         *
         * @param string|int $expression Time expression
         *
         * @return bool If expression is valid
         */
        protected function IsTimeExpressionValid($expression) : bool
        {
            if (is_numeric($expression) and $expression >= 0) {
                return true;
            }

            return strtotime($expression) !== false;
        }

        /**
         * Converts text time expression to seconds.
         *
         * @param string|int $expression Time expression
         *
         * @throws \InvalidArgumentException When expression is not string or integer
         *
         * @return int Time in seconds
         */
        protected function ParseExpiration($expression) : int
        {
            if (is_numeric($expression)) {
                return (int) $expression;
            } elseif (strtotime($expression) !== false) {
                return strtotime($expression, 0);
            } else {
                throw new \InvalidArgumentException('SessionSection::ParseExpiration expects argument 1 integer or string');
            }
        }

        /**
         * Getter for property $Name.
         *
         * @return string Name of the section
         */
        public function getName()
        {
            return $this->Name;
        }

        /**
         * Getter for property $Expiration.
         *
         * @return int Expiration in seconds of the section
         */
        public function getExpiration()
        {
            return $this->Expiration;
        }

        /**
         * Getter for property $LockedVariables.
         *
         * @return array List of locked variables of the section
         */
        public function getLockedVariables()
        {
            return $this->LockedVariables;
        }

        /**
         * Getter for property $Expirations.
         *
         * @return array List of expiration in seconds
         */
        public function getExpirations()
        {
            return $this->Expirations;
        }

        public function getVariables()
        {
            return $this->Variables;
        }

        /**
         * Getter for property $SaveModes.
         *
         * @return array List of save modes
         */
        public function getSaveModes()
        {
            return $this->SaveModes;
        }

        /**
         * Getter for property $Setters.
         *
         * @return array List of setters
         */
        public function getSetters()
        {
            return $this->Setters;
        }

        /**
         * Getter for property $Getters.
         *
         * @return array List of getters
         */
        public function getGetters()
        {
            return $this->Getters;
        }
    }
