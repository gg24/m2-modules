<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomStoreviews\Api;
 
interface StoreInterface
{
    /**
     * Return the collection of the store.
     *
     * @api
     * @param string $groupid store operand.
     * @return array of The collection.
     */
    public function getstorecollection($groupid);
}