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
use \common_ext_ExtensionsManager;
use \taoWfTest_models_classes_WfTestService;
use \taoWfTest_models_classes_WfTestModel;
use \core_kernel_classes_Class;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Property;

class WfTestModelTest extends TaoPhpUnitTestRunner
{

    /**
     *
     * @var taoWfTest_models_classes_WfTestService
     */
    protected $wftService = null;
    
    /**
     * 
     * @var taoWfTest_models_classes_WfTestModel
     */
    protected $wfModel;

    /**
     * tests initialization
     */
    public function setUp()
    {
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfTest'); // loads the extension
        
        TaoPhpUnitTestRunner::initTest();
        $this->wftService = taoWfTest_models_classes_WfTestService::singleton();
        $this->wfModel = new taoWfTest_models_classes_WfTestModel();
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testDeleteContent()
    {
        $testClass = $this->wftService->getRootclass();
        $testInstance = $this->wftService->createInstance($testClass, 'unittest test');
        $content = $testInstance->getOnePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        $this->wfModel->deleteContent($testInstance);
        $content2 = $testInstance->getOnePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        $this->assertNotNull($content);
        $this->assertInstanceOf('core_kernel_classes_Resource', $content);
        $this->assertNull($content2);
        
        $testInstance->delete();
    }

    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testCloneContent()
    {
        $testClass = $this->wftService->getRootclass();
        $testInstance = $this->wftService->createInstance($testClass, 'unittest test');
        
        $testInstance2 = $this->wftService->createInstance($testClass, 'unittest test 2');
        
        $content = $testInstance->getOnePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        $this->wfModel->deleteContent($testInstance2);
        
        $this->wfModel->cloneContent($testInstance, $testInstance2);
        
        $content2 = $testInstance->getOnePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        
        $this->assertInstanceOf('core_kernel_classes_Resource', $content2);
        $this->assertEquals($content->getUri(), $content2->getUri());
        
        $testInstance->delete();
        $testInstance2->delete();
    }
}

?>