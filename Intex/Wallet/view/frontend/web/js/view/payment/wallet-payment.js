define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
],function(Component,renderList){
    'use strict';
    renderList.push({
        type : 'wallet',
        component : 'Intex_Wallet/js/view/payment/method-renderer/wallet-method'
    });

    return Component.extend({});
})
