<?php

namespace Intex\Wallet\Controller\Standard;

class Response extends \Intex\Wallet\Controller\WalletAbstract {

    public function execute() {
    $returnUrl = $this->getCheckoutHelper()->getUrl('checkout');

        try {
        $paymentMethod = $this->getPaymentMethod();
        $params = $this->getRequest()->getParams();
    	$order = $this->getOrder();
    // actual processing
        $GrandTotal= (double)$order->getGrandTotal();
        $LastRealOrderId = $order->getIncrementId();
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$secret_key = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/wallet/secret_key');
 	$merchand_id = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/wallet/mid');
	$walletmodel = $objectManager->get('Intex\Wallet\Model\Wallet');
       
       
	$statuscode = $params['statuscode'];
        $orderid = $params['orderid'];
        $mid = $params['mid'];
        $amount = $params['amount'];
        $message = $params['statusmessage'];
        $checksumReceived = $params['checksum'];
        
        $all = "'" . $statuscode . "''" . $orderid . "''" . $amount . "''" . $message . "''" . $mid . "'";
        
        if($checksumReceived != null){
        	$isChecksumValid = $walletmodel->verifyChecksum($checksumReceived, $secret_key, $all);
        }
    
            
        if($isChecksumValid){
           // success
            $validateresponse = $this->validateResponse($params);
		    if ($validateresponse) {
		    		
		if($walletmodel->getCheckStatusApi($params,$secret_key, $merchand_id, $GrandTotal, $LastRealOrderId)){
	    		$returnUrl = $this->getCheckoutHelper()->getUrl('checkout/onepage/success');
		        $order = $this->getOrder();
		        $payment = $order->getPayment();
		        $paymentMethod->postProcessing($order, $payment, $params);
	    	}
	    	else {
	    		$walletmodel->showfailure("Verification failed");
	    	}
	    				
	    			}
	    			else {
	    				$walletmodel->showfailure("Order Mismatch");
	    			}
		    } 
    	else {
    		$this->messageManager->addErrorMessage(__('Payment failed. Please try again or choose a different payment method'));
                $returnUrl = $this->getCheckoutHelper()->getUrl('checkout/onepage/failure');
            }
        }
         catch (\Magento\Framework\Exception\LocalizedException $e) {
           	$this->messageManager->addExceptionMessage($e, $e->getMessage());
        } catch (\Exception $e) {
       	$this->messageManager->addExceptionMessage($e, __('We can\'t place the order.'));
        }

        $this->getResponse()->setRedirect($returnUrl);
    }
    

	
	/**
     * Verify the response coming into the server
     * @return boolean
     */
    protected function validateResponse($returnParams) 
    {
        //$postdata = Mage::app()->getRequest()->getPost();
        //$session = Mage::getSingleton('checkout/session');
        $flag = False;
        error_log('Response Code is ' . $returnParams['statuscode']);
		
		if ((int)$returnParams['statuscode'] == 0) {
			$flag = True;
		}
		else{			
			$flag = False;			
		}
        return $flag;
    }
	
	   
}
