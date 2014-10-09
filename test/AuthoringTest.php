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
use \taoTests_models_classes_TestsService;
use \core_kernel_classes_Class;
use \core_kernel_classes_Resource;
use \common_Utils;


include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 
 */
class AuthoringTestCase extends TaoPhpUnitTestRunner {

	/**
	 * @var taoTests_models_classes_TestsService
	 */
	protected $testsService = null;

	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoPhpUnitTestRunner::initTest();
		$this->testsService = taoTests_models_classes_TestsService::singleton();
	}

	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoTests_models_classes_TestsService::__construct
	 */
	public function testService(){
		$this->assertIsA($this->testsService, 'tao_models_classes_Service');
		$this->assertIsA($this->testsService, 'taoTests_models_classes_TestsService');
	}

    /**
     * test create instance
     * @return \core_kernel_classes_Resource
     */
	public function testInstanceCreate() {
	    $testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$testInstance = $this->testsService->createInstance($testClass, 'unittest test');
		$this->assertIsA($testInstance, 'core_kernel_classes_Resource');
		$this->assertTrue($testInstance->exists());

		$type = current($testInstance->getTypes());
		$this->assertEquals(TAO_TEST_CLASS, $type->getUri());

        return $testInstance;
	}

    /**
     * test model instance
     * testmodel associated by random, not tested here
     * @depends testInstanceCreate
     * @param $testInstance
     * @return void
     */
    public function testModelInstance($testInstance) {
		$modelInstance = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOTest.rdf#SimpleTestModel');
		$this->testsService->setTestModel($testInstance, $modelInstance);

		$testModel = $this->testsService->getTestModel($testInstance);
		$this->assertTrue($modelInstance->equals($testModel));
    }

    /**
     * test duplicate test instance
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
     * test instance setPropertyValue
     * @depends testInstanceCreate
     * @param \core_kernel_classes_Resource $testInstance
     * @return \core_kernel_classes_Property
     */
	public function testInstancePropertyCreate($testInstance){
        $prop = $this->testsService->getRootclass()->createProperty(
            'ResourceTestCaseProperty ' . common_Utils::getNewUri()
        );
        $testInstance->setPropertyValue($prop, 'wfAuthoringTestCase');
        $props = $testInstance->getPropertiesValues(array($prop));
        $this->assertTrue(in_array('wfAuthoringTestCase', $props[$prop->getUri()]));

        return $prop;
    }

    /**
     * test instance property delete
     * @depends testInstanceCreate
     * @depends testInstancePropertyCreate
     * @param \core_kernel_classes_Resource $testInstance
     * @param \core_kernel_classes_Property $prop
     * @return void
     */
	public function testInstancePropertyDelete($testInstance, $prop) {
        $prop->delete();
        $this->assertFalse($prop->exists());

        $testInstance->removePropertyValues($prop);
        $props = $testInstance->getPropertiesValues(array($prop));
        $this->assertFalse(in_array('wfAuthoringTestCase', $props[$prop->getUri()]));
    }

    /**
     * test clone test instance
     * @depends testInstanceCreate
     * @param \core_kernel_classes_Resource $testInstance
     * @return void
     */
    public function testInstanceClone($testInstance) {
        $clone = $this->testsService->cloneInstance($testInstance);
		$this->assertIsA($clone, 'core_kernel_classes_Resource');
        $this->assertTrue($clone->exists());
        $this->assertNotEquals($testInstance->getUri(), $clone->getUri());

        $clone->delete();
    }

    /**
     * test getTestItems
     * @depends testInstanceCreate
     * @param \core_kernel_classes_Resource $testInstance
     * @return void
     */
    public function testGetTestItems($testInstance) {
        $items = $this->testsService->getTestItems($testInstance);
        $this->assertEquals(count($items), 0);
    }

    /**
     * test delete test instance
     * @depends testInstanceCreate
     * @param \core_kernel_classes_Resource $testInstance
     * @return void
     */
    public function testInstanceDelete($testInstance) {
		$this->assertTrue($testInstance->delete());
		$this->assertFalse($testInstance->exists());
    }

}