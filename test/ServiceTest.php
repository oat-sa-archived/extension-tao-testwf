<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

namespace oat\taoWfTest\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use \taoWfTest_models_classes_WfTestService;
use \core_kernel_classes_Property;


/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 
 */
class ServiceTest extends TaoPhpUnitTestRunner {

	/**
	 * @var taoTests_models_classes_TestsService
	 */
	protected $wftService = null;

	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoPhpUnitTestRunner::initTest();
		$this->wftService = taoWfTest_models_classes_WfTestService::singleton();
	}

	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoTests_models_classes_TestsService::__construct
	 */
	public function testService(){
		$this->assertIsA($this->wftService, 'tao_models_classes_Service');
		$this->assertIsA($this->wftService, 'taoTests_models_classes_TestsService');
		$this->assertIsA($this->wftService, 'taoWfTest_models_classes_WfTestService');
	}

    /**
     * test create instance
     * @return \core_kernel_classes_Resource
     */
	public function testInstanceCreate() {
	    $testClass = $this->wftService->getRootclass();
		$testInstance = $this->wftService->createInstance($testClass, 'unittest test');
		$this->assertIsA($testInstance, 'core_kernel_classes_Resource');
		$this->assertTrue($testInstance->exists());

		$type = current($testInstance->getTypes());
		$this->assertEquals(TAO_TEST_CLASS, $type->getUri());

        return $testInstance;
	}

	/**
	 *
	 * @author Lionel Lecaque, lionel@taotesting.com
	 * @param string $uri
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	private function getResourceMock($uri)
	{
	    $resourceMock = $this->getMockBuilder('core_kernel_classes_Resource')
	    ->setMockClassName('FakeResource')
	    ->setConstructorArgs(array(
	        $uri
	    ))
	    ->getMock();
	
	    return $resourceMock;
	}
	
    /**
     * test SetTestItems and GetTestItems
     * @depends testInstanceCreate
     * @param \core_kernel_classes_Resource $testInstance
     * @return void
     */
    public function testSetTestItems($testInstance) {
		$item = $this->getResourceMock('http://localhost/test.rdf#fakeItem1Uri');
		$item->expects($this->once())
		->method('getLabel')
		->will($this->returnValue('fakeItemLabel'));
		
		$item->expects($this->once())
		->method('getUri')
		->will($this->returnValue('http://test.rdf#fakeItem1Uri'));
		
		$item2 = $this->getResourceMock('http://test.rdf#fakeItem2Uri');
		$item2->expects($this->once())
		->method('getLabel')
		->will($this->returnValue('fakeItemLabel2'));
		
		$item2->expects($this->once())
		->method('getUri')
		->will($this->returnValue('http://test.rdf#fakeItem2Uri'));
		

        $this->assertTrue($this->wftService->setTestItems($testInstance, array($item,$item2)));
        $tests = $this->wftService->getTestItems($testInstance);
        $this->assertEquals(2, count($tests));
        
        $this->assertInstanceOf('core_kernel_classes_Resource',current($tests));
        $this->assertEquals('http://test.rdf#fakeItem1Uri',current($tests)->getUri());
        $this->assertInstanceOf('core_kernel_classes_Resource',next($tests));
        $this->assertEquals('http://test.rdf#fakeItem2Uri',current($tests)->getUri());
    }

    /**
     * test createTestProcess
     * @depends testInstanceCreate
     * @param \core_kernel_classes_Resource $testInstance
     * @return void
     */
    public function testCreateTestProcess($testInstance) {
        $process = $this->wftService->createTestProcess($testInstance);
        $this->assertIsA($process, 'core_kernel_classes_Resource');
        $this->assertEquals("Process " . $testInstance->getLabel(), $process->getLabel());

        return $process;
    }

    /**
     * test SetTestItems exception, since we created a new process
     * @depends testInstanceCreate
     * @param \core_kernel_classes_Resource $testInstance
     * @expectedException \common_Exception
     * @return void
     */
    public function testSetTestItemsFailure($testInstance) {
		$item = $this->wftService->createInstance($this->wftService->getRootclass(), 'WfTestUnitTestItem');

		$this->assertIsA($item, 'core_kernel_classes_Resource');
        $this->assertEquals('WfTestUnitTestItem', $item->getLabel());
        $this->assertTrue($item->exists());

        $this->assertTrue($this->wftService->setTestItems($testInstance, array($item)));
		$this->assertTrue($item->delete());
		$this->assertFalse($item->exists());
    }

    /**
     * test delete test instance
     * @depends testInstanceCreate
     * @param \core_kernel_classes_Resource $testInstance
     * @return void
     */
	public function testInstanceDuplicate($testInstance){
        $duplicate = $testInstance->duplicate();
        $this->assertTrue($duplicate->exists());
        $this->assertNotEquals($testInstance->getUri(), $duplicate->getUri());
        $this->assertEquals('unittest test', $duplicate->getLabel());

        $duplicate->delete();
    }

    
    /**
     * test deleteTest
     * @depends testInstanceCreate
     * @param \core_kernel_classes_Resource $testInstance
     * @return void
     */
    public function testDeleteTest($testInstance) {
		$this->assertTrue($testInstance->exists());
        $this->wftService->deleteTest($testInstance);
		$this->assertFalse($testInstance->exists());
    }
    


}