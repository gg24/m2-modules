<?php

namespace Intex\Wallet\Model;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    protected $methodCode = \Intex\Wallet\Model\Wallet::PAYMENT_WALLET_CODE;
    
    
    protected $method;
	

    public function __construct(\Magento\Payment\Helper\Data $paymenthelper){
        $this->method = $paymenthelper->getMethodInstance($this->methodCode);
    }

    public function getConfig(){

        return $this->method->isAvailable() ? [
            'payment'=>['wallet'=>[
                'redirectUrl'=>$this->method->getRedirectUrl()  
            ]
        ]
        ]:[];
    }
}
