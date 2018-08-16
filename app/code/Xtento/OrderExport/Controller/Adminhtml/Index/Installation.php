<?php

/**
 * Product:       Xtento_OrderExport (2.3.7)
 * ID:            vuwMiuqT6hJFCgwIsMBM7iJwY9/E3ScMI/mHOqvUFvQ=
 * Packaged:      2017-10-04T08:30:08+00:00
 * Last Modified: 2016-10-27T14:07:54+00:00
 * File:          app/code/Xtento/OrderExport/Controller/Adminhtml/Index/Installation.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Controller\Adminhtml\Index;

class Installation extends \Xtento\OrderExport\Controller\Adminhtml\Index
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
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
        
        $this->messageManager->addComplexWarningMessage(
            'backendHtmlMessage',
            [
                'html' => (string)__(
                    'The extension has not been installed properly. The required database tables have not been created yet. Please check out our <a href="http://support.xtento.com/wiki/Troubleshooting:_Database_tables_have_not_been_initialized_(Magento_2)" target="_blank">wiki</a> for instructions. After following these instructions access the module at Sales > Sales Export again.'
                )
            ]
        );
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $this->updateMenu($resultPage);
        return $resultPage;
    }
}