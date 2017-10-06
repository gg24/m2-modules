define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Intex_Wallet/js/action/set-payment-method',
    ],
    function(Component,setPaymentMethod){
    'use strict';

    return Component.extend({
        defaults:{
            'template':'Intex_Wallet/payment/wallet'
        },
        redirectAfterPlaceOrder: false,
        
        afterPlaceOrder: function () {
            setPaymentMethod();    
        }

    });
});
