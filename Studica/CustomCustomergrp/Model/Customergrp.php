<?php
/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomCustomergrp\Model;

use Studica\CustomCustomergrp\Api\CustomergrpInterface;

/**
 * Defines the implementaiton class of the Customergrp service contract.
 */
class Customergrp extends \Magento\Framework\App\Http implements CustomergrpInterface
{
    /**
     * Return the collection of the Customergrp.
     *
     * @api
     * @return array of The collection.
     */

		public function getcustomergrpcollection(){
			$custgrpcollection = $this->_objectManager->create('\Magento\Customer\Model\Group')->getCollection();
			$result = array();
			if(count($custgrpcollection->getData()) > 0 ){
				return $custgrpcollection->getData();
			}else{
			return $result;
			}
				
			}

	 
}