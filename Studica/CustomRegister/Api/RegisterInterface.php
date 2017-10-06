<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomRegister\Api;


/**
 * Defines the service contract for some simple maths functions. The purpose is
 * to demonstrate the definition of a simple web service, not that these
 * functions are really useful in practice. The function prototypes were therefore
 * selected to demonstrate different parameter and return values, not as a good
 * calculator design.
 */

interface RegisterInterface
{
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
	 
	 public function getcustomregister($username, $firstname, $lastname, $email, $password, $roleid, $roletype); 
}