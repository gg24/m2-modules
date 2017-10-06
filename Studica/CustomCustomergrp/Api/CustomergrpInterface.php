<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomCustomergrp\Api;
 
interface CustomergrpInterface
{
    /**
     * Return the collection of the Customergrp.
     *
     * @api
     * @return array of The collection.
     */
    public function getcustomergrpcollection();
}