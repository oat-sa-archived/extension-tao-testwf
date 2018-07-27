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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
use oat\tao\helpers\TreeHelper;

/**
 * Controller for actions related to the authoring of the simple test model
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoTests
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class taoWfTest_actions_Authoring extends tao_actions_SaSModule {

	/**
	 * (non-PHPdoc)
	 * @see tao_actions_SaSModule::getClassService()
	 */
	protected function getClassService() {
		return taoTests_models_classes_TestsService::singleton();
	}
	
	/**
	 * constructor: initialize the service and the default data
	 */
	public function __construct()
	{

		parent::__construct();

		//the service is initialized by default
		$this->service = taoWfTest_models_classes_WfTestService::singleton();
	}

    /**
     * Renders the auhtoring for simple tests
     */
	public function index(){

        $test = $this->getCurrentInstance();

    	$itemSequence = array();
		$itemUris = array();
		$counter = 1;
		foreach($this->service->getTestItems($test) as $item){
			$itemUris[] = $item->getUri();
			$itemSequence[$counter] = array(
				'uri' 	=> tao_helpers_Uri::encode($item->getUri()),
				'label' => $item->getLabel()
			);
			$counter++;
		}

		// data for item sequence, terrible solution
		// @todo implement an ajax request for labels or pass from tree to sequence
		$allItems = array();
		foreach($this->service->getAllItems() as $itemUri => $itemLabel){
			$allItems['item_'.tao_helpers_Uri::encode($itemUri)] = $itemLabel;
		}
		
		$this->setData('uri', $test->getUri());
    	$this->setData('allItems', json_encode($allItems));
		$this->setData('itemSequence', $itemSequence);
		
		// data for generis tree form
		$this->setData('relatedItems', json_encode(tao_helpers_Uri::encodeArray($itemUris)));

		$openNodes = TreeHelper::getNodesToOpen($itemUris, new core_kernel_classes_Class(TAO_ITEM_CLASS));
		$this->setData('itemRootNode', TAO_ITEM_CLASS);
		$this->setData('itemOpenNodes', $openNodes);
		$this->setData('saveUrl', _url('saveItems', 'Authoring', 'taoWfTest'));

        $this->setView('authoring.tpl');
    }

	/**
	 * save the related items from the checkbox tree or from the sequence box
	 * @return void
	 */
	public function saveItems()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;

        $items = array();
        
        // items from generis tree form
        foreach (tao_helpers_form_GenerisTreeForm::getSelectedInstancesFromPost() as $uri) {
            $items[] = new core_kernel_classes_Resource($uri);
        }
        
        // items from sequence
		foreach($this->getRequestParameters() as $key => $value) {
		    if(preg_match("/^instance_/", $key)){
		        $items[] = new core_kernel_classes_Resource(tao_helpers_Uri::decode($value));
		    }
		}
		if($this->service->setTestItems($this->getCurrentInstance(), $items)){
			$saved = true;
		}
		$this->returnJson(array('saved'	=> $saved));
	}

}
