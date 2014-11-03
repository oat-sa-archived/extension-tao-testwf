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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
namespace oat\taoWfTest\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use \common_ext_ExtensionsManager;
use \taoTests_models_classes_WfTestService;
use \tao_models_classes_service_FileStorage;
use \taoWfTest_models_classes_WfTestService;
use \common_report_Report;
use \taoWfTest_models_classes_WfTestCompiler;

class WfTestCompilerTest extends TaoPhpUnitTestRunner
{

    protected $test;

    protected $service;

    public function setUp()
    {
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfTest'); // loads the extension
        
        TaoPhpUnitTestRunner::initTest();
        $this->service = taoWfTest_models_classes_WfTestService::singleton();
        $this->test = $this->service->createInstance($this->service->getRootclass(), 'taoWfTestUnitCompilerTest');
    }

    public function tearDown()
    {
        $this->test->delete();
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $uri            
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getResourceMock($uri)
    {      
        
        $prophet = new \Prophecy\Prophet;
        $resource = $prophet->prophesize('core_kernel_classes_Resource');
         
        $resource->getUri()->willReturn($uri);
        return $resource;

    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testCompile()
    {
        $item = $this->getResourceMock('http://localhost/test.rdf#fakeItem1Uri');
        
        $item->getLabel()->willReturn('fakeItemLabel');
        $item->getUri()->willReturn('http://localhost/test.rdf#fakeItem1Uri');
        
        $item2 = $this->getResourceMock('http://test.rdf#fakeItem2Uri');
        
        $item2->getLabel()->willReturn('fakeItemLabel2');
        $item2->getUri()->willReturn('http://localhost/test.rdf#fakeItem2Uri');
        
        
        $this->assertTrue($this->service->setTestItems($this->test, array(
            $item->reveal(),
            $item2->reveal()
        )));
        $storage = tao_models_classes_service_FileStorage::singleton();
        
        $compiler = new taoWfTest_models_classes_WfTestCompiler($this->test, $storage);
        
        $report = $compiler->compile();
        
        $item->getUri()->shouldHaveBeenCalled();
        $item2->getUri()->shouldHaveBeenCalled();
        
        $this->assertInstanceOf('common_report_Report', $report);
        $this->assertEquals(common_report_Report::TYPE_ERROR, $report->getType());

    }
}

?>