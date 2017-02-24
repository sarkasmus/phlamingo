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

namespace Phlamingo\Tests\HTTP\Sessions;

use Phlamingo\HTTP\Sessions\Exceptions\SessionException;
    use Phlamingo\HTTP\Sessions\Session;
    use Phlamingo\HTTP\Sessions\SessionSection;

    class SessionTest extends \PHPUnit_Framework_TestCase
    {
        protected $Session;

        public function setUp()
        {
            $storageManagerMock = $this->createMock('Phlamingo\\HTTP\\Sessions\\Storage\\FileStorageManager');
            $storageManagerMock->expects($this->once())->method('GetIterator')->willReturn(1);
            $this->Session = new Session(
                null,
                $storageManagerMock
            );
            $this->Session->Reflection = new \ReflectionClass($this->Session);
        }

        public function testProperties()
        {
            $this->assertEquals(hash('sha256', $_SERVER['REMOTE_ADDR'].'1'), $this->Session->SessionID);
            $this->assertEquals('Session', $this->Session->Name);
            $this->assertInstanceOf('Phlamingo\\HTTP\\Sessions\\Storage\\FileStorageManager', $this->Session->StorageManager);
        }

        public function testVariables()
        {
            $this->Session->var = 'value';
            $this->assertEquals('value', $this->Session->var);

            $this->Session->AddSection('Section');
            $this->assertEquals(new SessionSection('Section'), $this->Session->Section);

            $this->Session->Section->var = 'another value';
            $this->assertEquals('another value', $this->Session->Section->var);
        }

        public function testRename()
        {
            $this->Session->variableName = 'value';
            $this->Session->Rename('variableName', 'var');

            try {
                $foo = $this->Session->variableName;
                $this->fail('Expected exception');
            } catch (SessionException $e) {
            }

            $this->assertEquals('value', $this->Session->var);

            $this->Session->Rename('SessionSection');
            $this->assertEquals('SessionSection', $this->Session->Name);
        }

        public function testMove()
        {
            $this->Session->AddSection('Section1');
            $this->Session->AddSection('Section2');

            $this->Session->Section1->var1 = '1';
            $this->Session->Section1->var2 = '2';
            $this->Session->Section1->var = 'value';
            $this->Session->Section1->Move($this->Session->Section2, 'var');

            try {
                $foo = $this->Session->Section1->var;
                $this->fail('Exception expected');
            } catch (SessionException $e) {
            }

            $this->assertEquals('value', $this->Session->Section2->var);

            $this->Session->Section1->Move($this->Session->Section2);
            $this->assertEquals(['var1' => '1', 'var2' => '2', 'var' => 'value'], $this->Session->Section2->getIterator());
        }

        public function testClear()
        {
            $this->Session->AddSection('Section1');
            $this->Session->Section1->var1 = '1';
            $this->Session->Section1->var2 = '2';
            $this->Session->Section1->var3 = '3';

            $this->Session->Section1->Clear('var1');

            try {
                $foo = $this->Session->Section1->var1;
                $this->fail('Exception excepted');
            } catch (SessionException $e) {
            }

            $this->Session->Section1->var1 = '1';
            $this->Session->Section1->Clear();

            $this->assertEquals(['var1' => null, 'var2' => null, 'var3' => null], $this->Session->Section1->getIterator());
        }

        public function testLockAndUnlock()
        {
            $this->Session->AddSection('Section');
            $this->Session->Section->var1 = '1';
            $this->Session->Section->var2 = '2';

            $this->Session->Section->Lock('var1');
            try {
                $this->Session->Section->var1 = 'value';
                $this->fail('Expected exception');
            } catch (SessionException $e) {
            }

            $this->assertEquals(true, $this->Session->Section->IsLocked('var1'));

            $this->Session->Section->Unlock('var1');
            $this->assertEquals(false, $this->Session->Section->IsLocked('var1'));

            $this->Session->Section->Lock();
            $this->assertEquals(true, $this->Session->Section->IsLocked());
            try {
                $this->Session->Section->var2 = 'value';
                $this->fail('Expected exception');
            } catch (SessionException $e) {
            }
        }

        public function testSetterAndGetter()
        {
            $this->Session->AddSection('Section');
            $this->Session->Section->var = '';
            $this->Session->Section->Setter('var', function ($variable, $value) {
                return hash('sha256', $value);
            });
            $this->Session->Section->var = 'value';

            $this->assertEquals(hash('sha256', 'value'), $this->Session->Section->var);

            $this->Session->Section->Getter('var', function ($value) {
                return $value.'with_getter';
            });
            $this->assertEquals(hash('sha256', 'value').'with_getter', $this->Session->Section->var);
        }

        public function testExpiration()
        {
            $this->Session->var = 'value';

            $this->Session->Expiration('var', 100);
            $this->assertEquals(100, $this->Session->VarExpiration('var'));

            $this->Session->Expiration('var', '1 day');
            $this->assertEquals(86400, $this->Session->VarExpiration('var'));

            $this->Session->Expiration('var', '5 hours');
            $this->assertEquals(5 * 3600, $this->Session->VarExpiration('var'));

            $this->Session->Expiration('var', '1 month');
            $this->assertEquals(31 * 24 * 3600, $this->Session->VarExpiration('var'));
        }

        // IMPLEMENT SAVE MODES
    }
