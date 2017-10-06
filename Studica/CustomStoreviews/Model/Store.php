<?php
/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomStoreviews\Model;

use Studica\CustomStoreviews\Api\StoreInterface;

/**
 * Defines the implementaiton class of the store service contract.
 */
class Store extends \Magento\Framework\App\Http implements StoreInterface
{
    /**
     * Return the collection of the store.
     *
     * @api
     * @param string $groupid store operand.
     * @return array of The collection.
     */

		public function getstorecollection($groupid){
			$storecollection = $this->_objectManager->create('\Magento\Store\Model\Store')->getCollection()->addFieldToFilter('group_id', $groupid);
$result = array();
			if(count($storecollection->getData()) > 0 ){
				return $storecollection->getData();
			}else{
			return $result;
			}
				
			}

	 
}