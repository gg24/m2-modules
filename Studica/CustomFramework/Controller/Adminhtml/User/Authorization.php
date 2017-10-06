<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Studica\CustomFramework\Controller\Adminhtml\User;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\App\ResourceConnection;

class Authorization extends \Magento\Framework\Authorization {

     public function isAllowed($resource, $privilege = null)
    {   
		$SessionManager = \Magento\Framework\App\ObjectManager::getInstance()
			->get('Magento\Backend\Model\Auth\Session');
		$userid = $SessionManager->getUser()->getUserId();
		$AbstractDb= \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
		$role= \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Authorization\Model\Role');
		$connection =$AbstractDb->getConnection();
	    $binds = ['role_id' => $role->getId()];
        $select = $connection->select()
            ->from('authorization_role', ['parent_id'])
            ->where('user_id = '.$userid);
        $res = $connection->fetchCol($select, $binds);
		foreach ($res as $role ) {
			$return =  $this->_aclPolicy->isAllowed($role, $resource, $privilege);
			if ($return== true)
				return true;
		}
		return false;
    }

}
