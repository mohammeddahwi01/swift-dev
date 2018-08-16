<?php

/**
 * Product:       Xtento_TrackingImport (2.3.0)
 * ID:            HdWKOY0KdgGaRx+26HyONH06+SvSVZH7A2yQmSKRHJU=
 * Packaged:      2017-10-04T08:30:19+00:00
 * Last Modified: 2017-09-06T14:54:43+00:00
 * File:          app/code/Xtento/TrackingImport/Controller/Adminhtml/Profile/Edit.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Controller\Adminhtml\Profile;

class Edit extends \Xtento\TrackingImport\Controller\Adminhtml\Profile
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $healthCheck = $this->healthCheck();
        if ($healthCheck !== true) {
            $resultRedirect = $this->resultFactory->create(
                \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
            );
            return $resultRedirect->setPath($healthCheck);
        }

        $id = $this->getRequest()->getParam('id');
        $model = $this->profileFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This profile no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(
                    \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
                );
                return $resultRedirect->setPath('*/*/');
            }
            if (!$model->getEntity()) {
                $this->messageManager->addErrorMessage(__('No import entity has been set for this profile.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(
                    \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
                );
                return $resultRedirect->setPath('*/*/');
            }
        }

        $session = $this->_session;
        $data = $session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $this->registry->unregister('trackingimport_profile');
        $this->registry->register('trackingimport_profile', $model);

        // Add check if no mapping/actions have been defined for the profile
        $profileConfiguration = $model->getConfiguration();
        if (!isset($profileConfiguration['action']) || empty($profileConfiguration['action'])) {
            $this->messageManager->addWarningMessage(
                __(
                    'Warning: You haven\'t defined any import actions for this profile yet! Do not forget to define actions to execute for imported orders in the "Actions" tab below.'
                )
            );
        }
        if (!isset($profileConfiguration['mapping']) || empty($profileConfiguration['mapping'])) {
            $this->messageManager->addWarningMessage(
                __(
                    'Warning: You haven\'t defined the import mapping for this profile yet! Do not forget to define the mapping to map imported files in the "File Mapping" tab below.'
                )
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $this->updateMenu($resultPage);

        if ($this->registry->registry('trackingimport_profile') &&
            $this->registry->registry('trackingimport_profile')->getId()
        ) {
            $resultPage->getConfig()->getTitle()->prepend(
                __(
                    'Edit Import Profile \'%1\'',
                    $this->escaper->escapeHtml($this->registry->registry('trackingimport_profile')->getName())
                )
            );
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Profile'));
        }

        if ($session->getProfileDuplicated()) {
            $session->setProfileDuplicated(0);
        }

        return $resultPage;
    }
}