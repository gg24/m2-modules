<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\UserUpdate\Api;


/**
 * Defines the service contract for some simple maths functions. The purpose is
 * to demonstrate the definition of a simple web service, not that these
 * functions are really useful in practice. The function prototypes were therefore
 * selected to demonstrate different parameter and return values, not as a good
 * calculator design.
 */

interface UpdateInterface
{
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
	 
	 public function getuserupdate($username, $firstname, $lastname, $email, $password, $roleid); 
	 /**
     * Return the user information.
     *
     * @api
     * @param string $username
	 * @return array of The user information.
     */
	 
	 public function getuserdelete($username);
}