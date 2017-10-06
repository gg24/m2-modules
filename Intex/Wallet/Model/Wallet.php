<?php


namespace Intex\Wallet\Model;

use Magento\Sales\Api\Data\TransactionInterface;

class Wallet extends \Magento\Payment\Model\Method\AbstractMethod {

    const PAYMENT_WALLET_CODE = 'wallet';
    protected $_code = self::PAYMENT_WALLET_CODE;

    /**
     *
     * @var \Magento\Framework\UrlInterface 
     */
    protected $_urlBuilder;
    protected $_supportedCurrencyCodes = array(
        'AFN', 'ALL', 'DZD', 'ARS', 'AUD', 'AZN', 'BSD', 'BDT', 'BBD',
        'BZD', 'BMD', 'BOB', 'BWP', 'BRL', 'GBP', 'BND', 'BGN', 'CAD',
        'CLP', 'CNY', 'COP', 'CRC', 'HRK', 'CZK', 'DKK', 'DOP', 'XCD',
        'EGP', 'EUR', 'FJD', 'GTQ', 'HKD', 'HNL', 'HUF', 'INR', 'IDR',
        'ILS', 'JMD', 'JPY', 'KZT', 'KES', 'LAK', 'MMK', 'LBP', 'LRD',
        'MOP', 'MYR', 'MVR', 'MRO', 'MUR', 'MXN', 'MAD', 'NPR', 'TWD',
        'NZD', 'NIO', 'NOK', 'PKR', 'PGK', 'PEN', 'PHP', 'PLN', 'QAR',
        'RON', 'RUB', 'WST', 'SAR', 'SCR', 'SGF', 'SBD', 'ZAR', 'KRW',
        'LKR', 'SEK', 'CHF', 'SYP', 'THB', 'TOP', 'TTD', 'TRY', 'UAH',
        'AED', 'USD', 'VUV', 'VND', 'XOF', 'YER'
    );
    
    private $checkoutSession;

    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
      public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Intex\Wallet\Helper\Wallet $helper,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order $salesOrder
              
    ) {
        $this->helper = $helper;
        $this->orderSender = $orderSender;
        $this->httpClientFactory = $httpClientFactory;
        $this->checkoutSession = $checkoutSession;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );

    }

    public function canUseForCurrency($currencyCode) {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }

    public function getRedirectUrl() {
        return $this->helper->getUrl($this->getConfigData('redirect_url'));
    }

    public function getReturnUrl() {
        return $this->helper->getUrl($this->getConfigData('return_url'));
    }

    public function getCancelUrl() {
        return $this->helper->getUrl($this->getConfigData('cancel_url'));
    }

    /**
     * Return url according to environment
     * @return string
     */
    public function getCgiUrl() {
        $env = $this->getConfigData('environment');
        if ($env === 'live') {
            return $this->getConfigData('live_url');
        }
        return $this->getConfigData('test_url');
    }
    
    /**
     * Return url according to environment
     * @return string
     */
    public function getCgiStatusUrl() {
        $env = $this->getConfigData('environment');
        if ($env === 'live') {
            return 'https://www.mobikwik.com/checkstatus';
        }
        return 'https://test.mobikwik.com/checkstatus';
    }

/**
     * Method to send the request to gateway
     */

    public function buildCheckoutRequest() {
    
        $order = $this->checkoutSession->getLastRealOrder();
        $billing_address = $order->getBillingAddress();
        $secretkey = $this->getConfigData("secret_key");
        $cell = $billing_address->getTelephone();
        $email = $order->getCustomerEmail();
        $amount = round($order->getBaseGrandTotal(), 2);
        $orderid = $this->checkoutSession->getLastRealOrderId();
        $returnurl = $this->getReturnUrl();
        $mid = $this->getConfigData("mid");
        
        $all = "'" . $cell . "''" . $email . "''" . $amount . 
                "''" . $orderid . "''" . $returnurl . "'" . $mid . "'";
        $cheksum = $this->generateRequestChecksum($secretkey,$all);
        $params = array();
        $params["email"] = $email;
        $params["amount"] = $amount;
        $params["cell"] = $cell;
        $params["orderid"] = $orderid;
        $params["mid"] = $mid;
        $params["merchantname"] = $this->getConfigData("merchantname");
        $params["redirecturl"] = $returnurl;
        $params["checksum"] = $cheksum;
        return $params;
    }
    
    /**
     * Method to generate the request checksum
     * @param String $secretKey
     * @param String $all
     */
	public function generateRequestChecksum($secretKey,$all) {
	    $algo = 'sha256';
		$checksum =  hash_hmac($algo, $all, $secretKey);
		return $checksum;
	}

    /**
     * Method to generate the error with error code
     * @param String $error
     */

    public function showfailure($error)
	{
		// failure/cancel
		error_log('Response entered in failed statement');
		$er = 'Wallet could not process your request because of the error "'.$error . '"'; 
		$this->checkoutSession->addError($er);
		$errUrl = $this->getCheckoutHelper()->getUrl('checkout/onepage/failure');
		$this->getResponse()->setRedirect($errUrl);
	}

    /**
     * Method to send to success page and place order after all verification.
     * @param String $order
     * @param String $payment
     * @param String $response
     */
    
    public function postProcessing(\Magento\Sales\Model\Order $order,
            \Magento\Framework\DataObject $payment, $response) {
        $dummy_txn_id = 'MW_'.$response['orderid'];
        $payment->setTransactionId($dummy_txn_id);
        $payment->setTransactionAdditionalInfo(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, $response);
        $payment->setAdditionalInformation('wallet_order_status', 'approved');
        $payment->addTransaction(TransactionInterface::TYPE_ORDER);
        $payment->setIsTransactionClosed(0);
        $payment->place();
        $order->setStatus('processing');
        $order->save();
    }
    
    /**
     * Method to verify the response checksum
     * @param String $receivedChecksum
     * @param String $secretKey
     * @param String $all
     */
	public function verifyChecksum($receivedChecksum,$secretKey,$all) {
	    $algo = 'sha256';
		$checksum =  hash_hmac($algo, $all, $secretKey);
        
        $isChecksumValid = False;
        
        if($checksum==$receivedChecksum){
            $isChecksumValid = True;
        }
		return $isChecksumValid;
	}

    /**
     * Method to verify the checkstatus with curl request after response
     * @param String $postdata
     * @param String $secret_key
     * @param String $merchand_id
     * @param String $GrandTotal
     * @param String $LastRealOrderId
     */
	
	 public function getCheckStatusApi($postdata, $secret_key, $merchand_id, $GrandTotal,$LastRealOrderId) 
	{
		$orderid = $postdata['orderid'];
		$amount = $postdata['amount'];
		$total_amount = $GrandTotal;
		$flag = false;
    	if( ((double)$postdata['amount'] == (double)$GrandTotal) AND ($LastRealOrderId == $postdata['orderid'])) {
    				
    		$algo_wallet = 'sha256';
    		$MerchantId =  $merchand_id ; // merchant ID 
    		$WorkingKey =  $secret_key ; // merchant key
 			
    		$checksum_string_wallet = "'{$MerchantId}''{$postdata['orderid']}'";
    		$checksum_wallet = hash_hmac($algo_wallet, $checksum_string_wallet, $WorkingKey);   /// this is final checksum //
		error_log('Initial checksum : ' . $checksum_wallet);

    		//live url "https://www.mobikwik.com/checkstatus"; 
    		//"test url https://test.mobikwik.com/checkstatus";
    		$url = $this->getCgiStatusUrl();
    		$version = '2';
    		$fields = "mid=$MerchantId&orderid=".$postdata['orderid']."&checksum=$checksum_wallet&ver=2";
                  
    				// is cURL installed yet?
    				if (!function_exists('curl_init')){
    					
    					die('Sorry cURL is not installed!');
    				}
    				// then let's create a new cURL resource handle
    				$ch = curl_init();
    				 
    				// Now set some options (most are optional)
    				 
    				// Set URL to hit
    				curl_setopt($ch, CURLOPT_URL, $url);
    				 
    				// Include header in result? (0 = yes, 1 = no)
    				curl_setopt($ch, CURLOPT_HEADER, 0);
    				 
    				curl_setopt($ch, CURLOPT_POST, 1);
    				 
    				curl_setopt($ch, CURLOPT_POSTFIELDS,  $fields);
    				 
    				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    				 
    				// Timeout in seconds
    				curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    
    
    				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    				 
    				// Download the given URL, and return output
    				error_log('getting outputXml');
    				$outputXml = curl_exec($ch);
    				error_log($outputXml);
    				// Close the cURL resource, and free system resources
    				curl_close($ch);    	
    				$outputXmlObject =  simplexml_load_string($outputXml);
                    
$checksum_string_checkapi="'{$outputXmlObject->statuscode}''{$outputXmlObject->orderid}''{$outputXmlObject->refid}''{$outputXmlObject->amount}''{$outputXmlObject->statusmessage}''{$outputXmlObject->ordertype}'";
   	$checksum_check_api = hash_hmac($algo_wallet, $checksum_string_checkapi, 
$WorkingKey);
   		if(($checksum_check_api == $outputXmlObject->checksum) && ((double)$outputXmlObject->amount == (double)$postdata['amount']) && 
			((double)$postdata['amount'] == (double)$total_amount) )
		{	
			$flag = true;
                }else{
                     $flag =  false;
                }
    }
    return $flag;
    
    }


}
