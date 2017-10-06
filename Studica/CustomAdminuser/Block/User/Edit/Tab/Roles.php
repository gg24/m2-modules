<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Studica\CustomAdminuser\Block\User\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Framework\View\Element\Template;

class Roles extends \Magento\User\Block\User\Edit\Tab\Roles {

    /**
     * @param Column $column
     * @return $this
     */

	

    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'assigned_user_role') {
            $userRoles = $this->getSelectedRoles();
            if (empty($userRoles)) {
                $userRoles = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('role_id', ['in' => $userRoles]);
            } else {
                if ($userRoles) {
                    $this->getCollection()->addFieldToFilter('role_id', ['nin' => $userRoles]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_userRolesFactory->create();
        $collection->setRolesFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns() {  
        $this->addColumn(
                'assigned_user_role', [
            'header_css_class' => 'data-grid-actions-cell',
            'header' => __('Assigned'),
            'type' => 'checkbox',
            'name' => 'roles',
            'values' => $this->getSelectedRoles(),
            'align' => 'center',
            'index' => 'role_id'
                ]
        );

        $this->addColumn('role_name', ['header' => __('Role'), 'index' => 'role_name']);

        return $this; //parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl() {
        $userPermissions = $this->_coreRegistry->registry('permissions_user');
        return $this->getUrl('*/*/rolesGrid', ['user_id' => $userPermissions->getUserId()]);
    }

    /**
     * @param bool $json
     * @return array|string
     */
    public function getSelectedRoles($json = false) {
        if ($this->getRequest()->getParam('user_roles') != "") {
            return $this->getRequest()->getParam('user_roles');
        }
        /* @var $user \Magento\User\Model\User */
        $user = $this->_coreRegistry->registry('permissions_user');
        //checking if we have this data and we
        //don't need load it through resource model
        if ($user->hasData('roles')) {
            $uRoles = $user->getData('roles');
        } else {
            $uRoles = $user->getRoles();
        }

        if ($json) {
            $jsonRoles = [];
            foreach ($uRoles as $urid) {
                $jsonRoles[$urid] = 0;
            }
            return $this->_jsonEncoder->encode((object) $jsonRoles);
        } else {
            return $uRoles;
        }
    }

}
