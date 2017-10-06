<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Studica\CustomAdminuser\Controller\Adminhtml\User;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\App\ResourceConnection;

class Save extends \Magento\User\Controller\Adminhtml\User\Save {

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute() {
        $custresource = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\App\ResourceConnection');
        $customcon = $custresource->getConnection();
        $userId = (int) $this->getRequest()->getParam('user_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        /* @var $model \Magento\User\Model\User */
        $model = $this->_userFactory->create()->load($userId);
        if ($userId && $model->isObjectNew()) {
            $this->messageManager->addError(__('This user no longer exists.'));
            $this->_redirect('adminhtml/*/');
            return;
        }
        $model->setData($this->_getAdminUserData($data));
        $uRoles = $this->getRequest()->getParam('roles', []);

        /* @var $currentUser \Magento\User\Model\User */
        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
        if ($userId == $currentUser->getId() && $this->_objectManager->get(
                        'Magento\Framework\Validator\Locale'
                )->isValid(
                        $data['interface_locale']
                )
        ) {
            $this->_objectManager->get(
                    'Magento\Backend\Model\Locale\Manager'
            )->switchBackendInterfaceLocale(
                    $data['interface_locale']
            );
        }

        /* Before updating admin user data, ensure that password of current admin user is entered and is correct */
        $currentUserPasswordField = \Magento\User\Block\User\Edit\Tab\Main::CURRENT_USER_PASSWORD_FIELD;
        $isCurrentUserPasswordValid = isset($data[$currentUserPasswordField]) && !empty($data[$currentUserPasswordField]) && is_string($data[$currentUserPasswordField]);
        try {
            if (!($isCurrentUserPasswordValid && $currentUser->verifyIdentity($data[$currentUserPasswordField]))) {
                throw new AuthenticationException(__('You have entered an invalid password for current user.'));
            }

            $model->save();
            $userIdafr = $model->getUserId();
            $this->messageManager->addSuccess(__('You saved the user.'));
            $this->_getSession()->setUserData(false);

            if (count($uRoles) > 0) {
                $customcon->query("delete from authorization_role where user_id='" . $userIdafr . "'");
            }
            foreach ($uRoles as $roleValue) {
                $sql = "insert into authorization_role(parent_id,tree_level,sort_order,role_type,user_id,user_type,role_name) values ('" . $roleValue . "','2', '0', 'U', '" . $userIdafr . "', '2', 'Operator')";

                $customcon->query($sql);
            }

            $this->_redirect('adminhtml/*/');
        } catch (\Magento\Framework\Validator\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
            $this->redirectToEdit($model, $data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->redirectToEdit($model, $data);
        }
    }

    /**
     * @param \Magento\User\Model\User $model
     * @param array $data
     * @return void
     */
    protected function redirectToEdit(\Magento\User\Model\User $model, array $data) {
        $this->_getSession()->setUserData($data);
        $arguments = $model->getId() ? ['user_id' => $model->getId()] : [];
        $arguments = array_merge($arguments, ['_current' => true, 'active_tab' => '']);
        $this->_redirect('adminhtml/*/edit', $arguments);
    }

}
