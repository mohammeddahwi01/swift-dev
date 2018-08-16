<?php

/**
 * Product:       Xtento_OrderExport (2.3.7)
 * ID:            vuwMiuqT6hJFCgwIsMBM7iJwY9/E3ScMI/mHOqvUFvQ=
 * Packaged:      2017-10-04T08:30:08+00:00
 * Last Modified: 2016-02-25T15:17:49+00:00
 * File:          app/code/Xtento/OrderExport/Model/Destination/Custom.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Destination;

class Custom extends AbstractClass
{
    public function testConnection()
    {
        $this->initConnection();
        if (!$this->getDestination()->getBackupDestination()) {
            $this->getDestination()->setLastResult($this->getTestResult()->getSuccess())->setLastResultMessage($this->getTestResult()->getMessage())->save();
        }
        return $this->getTestResult();
    }

    public function initConnection()
    {
        $this->setDestination($this->destinationFactory->create()->load($this->getDestination()->getId()));
        $testResult = new \Magento\Framework\DataObject();
        $this->setTestResult($testResult);
        if (!@$this->objectManager->create($this->getDestination()->getCustomClass())) {
            $this->getTestResult()->setSuccess(false)->setMessage(__('Custom class NOT found.'));
        } else {
            $this->getTestResult()->setSuccess(true)->setMessage(__('Custom class found and ready to use.'));
        }
        return true;
    }

    public function saveFiles($fileArray)
    {
        if (empty($fileArray)) {
            return [];
        }
        // Init connection
        $this->initConnection();
        // Call custom class
        @$this->objectManager->create($this->getDestination()->getCustomClass())->saveFiles($fileArray);
        return array_keys($fileArray);
    }
}