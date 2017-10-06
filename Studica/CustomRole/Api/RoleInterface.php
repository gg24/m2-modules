<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomRole\Api;
 
interface RoleInterface
{
    /**
     * Return the collection of the role.
     *
     * @api
     * @return array of The collection.
     */
    public function getrolecollection();
}