<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomBrand\Model;

use Studica\CustomBrand\Api\BrandInterface;

/**
 * Defines the implementaiton class of the Brand service contract.
 */
class Brand extends \Magento\Framework\App\Http implements BrandInterface
{
    /**
     * Return the collection of Brand.
     *
     * @api
     * @param string $storeid  operand.
	 * @param string $segment  operand.
	 * @param string $brand  operand.
     * @return array of The brand.
     */
 public function getcustombrand($storeid,$segment,$brand) {

	 $config = $this->_objectManager->create('\Magento\Catalog\Model\Config');
		$resource = $this->_objectManager->create('\Magento\Framework\App\ResourceConnection');
		$catcollection  = $this->_objectManager->create('\Magento\Catalog\Model\Category');
		$prodcollection  = $this->_objectManager->create('\Magento\Catalog\Model\Product');
		$procollection = $this->_objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->addAttributeToSelect($config->getProductAttributes());
		
		$resourceattr = $this->_objectManager->create('\Magento\Eav\Model\ResourceModel\Entity\Attribute');
		$bestseller_id =   $resourceattr->getIdByCode('catalog_product', 'bestseller');
		$up_sell =   $resourceattr->getIdByCode('catalog_product', 'up_sell');

		$prodarrsub = array(); 

		$segmentarr = array();
		$config = $this->_objectManager->create('\Magento\Catalog\Model\Config');
		$resource = $this->_objectManager->create('\Magento\Framework\App\ResourceConnection');
		$catcollection  = $this->_objectManager->create('\Magento\Catalog\Model\Category');
		$prodcollection  = $this->_objectManager->create('\Magento\Catalog\Model\Product');
		$procollection = $this->_objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->addAttributeToSelect($config->getProductAttributes());
		$resourceattr = $this->_objectManager->create('\Magento\Eav\Model\ResourceModel\Entity\Attribute');
		$brand_id = $resourceattr->getIdByCode('catalog_product', 'brand');
		$prodarrsub = array(); 
		$procollection->getSelect()
								 ->joinLeft(
									array('prodbrand' => $resource->getTableName('catalog_product_entity_varchar')),
									"e.entity_id = prodbrand.entity_id AND prodbrand.attribute_id = $brand_id",
									array('brand' => 'prodbrand.value')
								);
		$procollection->addFieldToFilter('brand',array('like' => '%'.$brand.'%'));
		
		if (count($procollection) > 0){
			foreach($procollection as $key=> $valh){
				$prodid = $valh->getId();
				$prodcollection = $prodcollection->loadByAttribute('sku', $valh->getSku());
				$storeids = $prodcollection->getStoreIds();
				
				if(in_array($storeid, $storeids)){
					$catcoll = $prodcollection->getCategoryCollection();
					
					foreach($catcoll as $catval){
						$catidarr[] = $catval->getId();
						
						$catpatharr = explode('/',$catval->getPath());
						
						$catdata = $catcollection->load($catpatharr[2]);
				
						$segarr = explode(',',$catdata->getCustomSegmentation());
								
						if(in_array($segment, $segarr)){
						$segmentarr[$prodid] = array('prodid'=>$valh->getId(),'status'=>'1','catid'=>$catpatharr[1],'catsegment'=>$catdata->getCustomSegmentation(),'relatedcat'=>$catidarr,'products'=>$prodcollection->getData());
						}
					}
				 }
				} return $segmentarr;
				
		}else{
							$segmentarr[] = array('status'=>'0');
							return $segmentarr;
			}
}		
}