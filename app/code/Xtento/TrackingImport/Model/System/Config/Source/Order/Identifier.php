<?php

/**
 * Product:       Xtento_TrackingImport (2.3.0)
 * ID:            HdWKOY0KdgGaRx+26HyONH06+SvSVZH7A2yQmSKRHJU=
 * Packaged:      2017-10-04T08:30:20+00:00
 * Last Modified: 2016-03-13T19:40:24+00:00
 * File:          app/code/Xtento/TrackingImport/Model/System/Config/Source/Order/Identifier.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\System\Config\Source\Order;

use Magento\Framework\Option\ArrayInterface;

/**
 * @codeCoverageIgnore
 */
class Identifier implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $identifiers[] = ['value' => 'order_increment_id', 'label' => __('Order Increment ID')];
        $identifiers[] = [
            'value' => 'order_entity_id',
            'label' => __('Order Entity ID (entity_id, internal Magento ID)')
        ];
        $identifiers[] = ['value' => 'invoice_increment_id', 'label' => __('Invoice Increment ID')];
        $identifiers[] = ['value' => 'shipment_increment_id', 'label' => __('Shipment Increment ID')];
        $identifiers[] = ['value' => 'creditmemo_increment_id', 'label' => __('Credit Memo Increment ID')];
        return $identifiers;
    }
}
