<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomRegister\Model;

use Studica\CustomRegister\Api\RegisterInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Defines the implementaiton class of the register service contract.
 */
class Register extends \Magento\Framework\App\Http implements RegisterInterface {

    /**
     * Return the register information.
     *
     * @api
     * @param string $username   
     * @param string $firstname  
     * @param string $lastname  
     * @param string $email 
     * @param string $password
     * @param string $roleid
     * @param string $roletype
     * @return array of The register information.
     */
    public function getcustomregister($username, $firstname, $lastname, $email, $password, $roleid, $roletype) {

        $usermodel = $this->_objectManager->create('\Magento\User\Model\User');


        $rolearr = explode(',', $roleid);
        $user_creation = $usermodel->setUsername($username)
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setEmail($email)
                ->setPassword($password)
                ->save();
        $user = $user_creation->loadByUsername($username);
        $userId = $user->getUserId();
        if (!empty($userId)) {

            $custresource = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get('Magento\Framework\App\ResourceConnection');
            $customcon = $custresource->getConnection();

            foreach ($rolearr as $roleid) {

                $sql = "insert into authorization_role(parent_id,tree_level,sort_order,role_type,user_id,user_type,role_name) values ('" . $roleid . "','2', '0', '" . $roletype . "', '" . $userId . "', '2', 'Operator')";
                $customcon->query($sql);
            }
        }
        $user_id = $user->getData('user_id');
        if (!empty($user_id)) {
            $prodarrfinal[] = array('status' => 1, 'user_id' => $user_id, 'password' => $password);
        } else {
            $prodarrfinal[] = array('status' => 0, "message" => "There is some problem while creating the user ");
        }
        return $prodarrfinal;
    }

}
