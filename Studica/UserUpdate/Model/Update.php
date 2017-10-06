<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\UserUpdate\Model;

use Studica\UserUpdate\Api\UpdateInterface;

/**
 * Defines the implementaiton class of the user service contract.
 */
class Update extends \Magento\Framework\App\Http implements UpdateInterface {

    /**
     * Return the user information.
     *
     * @api
     * @param string $username   
     * @param string $firstname  
     * @param string $lastname  
     * @param string $email 
     * @param string $password
	 * @param string $roleid
     * @return array of The user information.
     */
    public function getuserupdate($username, $firstname, $lastname, $email, $password, $roleid) {
        $usermodel = $this->_objectManager->create('\Magento\User\Model\User');



        $user = $usermodel->loadByUsername($username); 
//echo '<pre>';print_r($user->getData());exit;
$user_creation = $usermodel->setUsername($username)
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setEmail($email)
                ->setPassword($password)
                ->save();

$role = $this->_objectManager->create('\Magento\Authorization\Model\Role');
          $role->setParent_id($roleid);
          $role->setTree_level(2);
          $role->setRole_type('G');
          $role->setUser_id($user_creation->getUserId());
		  $role->setUser_type(2);
		  $role->setRole_name($user_creation->getFirstname());
          $role->save();

   
         $user_id =   $user_creation->getData('user_id');
		 //$pwd =		  $user->getData('password');
				
        if (!empty($user_id)){
            $prodarrfinal[] = array('status' => 1, 'user_id' => $user_id, 'password' => $password);
            
        } else {
            $prodarrfinal[] = array('status' => 0, "message" => "There is some problem while creating the user ");
        }
		return $prodarrfinal;
    }
	  /**
     * Return the user information.
     *
     * @api
     * @param string $username
	 * @return void for the user information.
     */
	 
	 public function getuserdelete($username){
	 $usermodel = $this->_objectManager->create('\Magento\User\Model\User');
	  $user = $usermodel->loadByUsername($username); 
                if($user->delete()){
				return true;
				}else{
					return false;
				}
	 }

}
