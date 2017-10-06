<?php

namespace Intex\Wallet\Controller\Standard;

class Cancel extends \Intex\Wallet\Controller\WalletAbstract {

	public function execute() {
		$this->getOrder()->cancel()->save();

		$this->messageManager->addErrorMessage(__('Your order has been cancelled'));
		$this->getResponse()->setRedirect(
			$this->getCheckoutHelper()->getUrl('checkout')
		);
	}

}
