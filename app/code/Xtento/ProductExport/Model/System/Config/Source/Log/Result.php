<?php

/**
 * Product:       Xtento_ProductExport (2.3.9)
 * ID:            cb9PRAWlxmJOwg/jsj5X3dDv0+dPZORkauC/n26ZNAU=
 * Packaged:      2017-10-04T08:29:55+00:00
 * Last Modified: 2016-04-14T15:37:35+00:00
 * File:          app/code/Xtento/ProductExport/Model/System/Config/Source/Log/Result.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Model\System\Config\Source\Log;

use Magento\Framework\Option\ArrayInterface;

/**
 * @codeCoverageIgnore
 */
class Result implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $values = [
            \Xtento\ProductExport\Model\Log::RESULT_NORESULT => __('No Result'),
            \Xtento\ProductExport\Model\Log::RESULT_SUCCESSFUL => __('Successful'),
            \Xtento\ProductExport\Model\Log::RESULT_WARNING => __('Warning'),
            \Xtento\ProductExport\Model\Log::RESULT_FAILED => __('Failed')
        ];
        return $values;
    }
}
