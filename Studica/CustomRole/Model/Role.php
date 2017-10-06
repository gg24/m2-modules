<?php
/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomRole\Model;

use Studica\CustomRole\Api\RoleInterface;

/**
 * Defines the implementaiton class of the role service contract.
 */
class Role extends \Magento\Framework\App\Http implements RoleInterface
{
    /**
     * Return the collection of the role.
     *
     * @api
     * @return array of The collection.
     */

		public function getrolecollection(){
			$rolecollection = $this->_objectManager->create('\Magento\Authorization\Model\ResourceModel\Role\Collection');
			$rolecollection = $rolecollection->addFieldToFilter('role_type',array('like' => 'G'));
			$result = array();
			if(count($rolecollection->getData()) > 0 ){
				return $rolecollection->getData();
			}else{
			return $result;
			}	
			}	 
}