<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomProduct\Model;

use Studica\CustomProduct\Api\ProductInterface;

/**
 * Defines the implementaiton class of the Product service contract.
 */
class Product extends \Magento\Framework\App\Http implements ProductInterface
{
    /**
     * Return the collection of product.
     *
     * @api
	 * @param string $sku  operand.
     * @return array of The product.
     */
 public function getcustomproduct($sku) {
		$segmentarr = array();
	$var  = 	$prodcollection  = $this->_objectManager->create('\Magento\Catalog\Model\Product');
		$StockRegistryProvider = $this->_objectManager->create('\Magento\CatalogInventory\Model\StockRegistryProvider');
		$prodarrsub = array(); 
		if ($sku){
				$prodcollectiond = $prodcollection->loadByAttribute('sku', $sku);
				
				$prod_id  =  $prodcollectiond->getEntityId();
				
                 $prodfullcollection = $var->load($prod_id);
				
				if($prodcollectiond->getData('has_options') == 1){
				
				$prodoptions  = $this->_objectManager->create('\Magento\Catalog\Model\Product\Option');
				$options = $prodoptions->getProductOptionCollection($prodfullcollection);
				
				$optionsarray = $options->getData();
					
				$prodfullcollection->setOptionsContainer($optionsarray);
				
				$vargallery = $prodfullcollection->getData('media_gallery');
				
				}
				
				
			$websiteId = 1;
            $stockStatus = $StockRegistryProvider->getStockStatus($prodcollectiond->getEntityId(), $websiteId);
			$qty = $stockStatus->getQty();
			$prodcollectiond->setQty($qty);
			
			if(!empty($vargallery)){
			$prodcollectiond->setMedia_gallery_entries($vargallery['images']);
			}
			$segmentarr[] = array('status'=>'1','proddata'=>$prodcollectiond->getData());	
			return $segmentarr;
		}else{
							$segmentarr = array('status'=>'0');
							return $segmentarr;
					}
}		
}