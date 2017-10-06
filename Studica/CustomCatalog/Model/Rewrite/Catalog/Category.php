<?php

namespace Studica\CustomCatalog\Model\Rewrite\Catalog;

class Category extends \Magento\Catalog\Model\Category {

    public function afterSave() {
        $result = parent::afterSave();
        $this->_getResource()->addCommitCallback([$this, 'reindex']);

        $store_id = $result->getStoreId();

        $result->setCustomStore($store_id);

        $data = array("category_id" => $result->getId(), "posttitle" => $result->getName(), "storeid" => $result->getStoreId(), "parent_id" => $result->getParentId(), "level" => $result->getLevel(), "segmentid" => $result->getCustomSegmentation());

        $url = wordpress_category_creation_url;

        $myvars = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
      /*  echo $response;
        exit;
        return $result;*/
    }

}

?>