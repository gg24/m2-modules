<?php
   
    namespace Studica\CustomCatalog\Model\Rewrite\Catalog;
 
    class CategoryRepository extends \Magento\Catalog\Model\CategoryRepository
    {

		 public function delete(\Magento\Catalog\Api\Data\CategoryInterface $category)
    {   
        try {
            $categoryId = $category->getId();
			$data = array("category_id"=>$category->getId());
		
		$myvars = json_encode($data);
		 $url = wordpress_category_creation_url;
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec( $ch );
		//echo $response;exit;
            $this->categoryResource->delete($category);
        } catch (\Exception $e) {
            throw new StateException(
                __(
                    'Cannot delete category with id %1',
                    $category->getId()
                ),
                $e
            );
        }
        unset($this->instances[$categoryId]);
        return true;
    }
 
    }
	
	?>