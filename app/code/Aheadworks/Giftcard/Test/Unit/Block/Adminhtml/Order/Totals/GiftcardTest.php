<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Block\Adminhtml\Order\Totals;

use Aheadworks\Giftcard\Block\Adminhtml\Order\Totals\Giftcard;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Block\Order\Totals as SalesTotals;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\DataObject\Factory;
use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\OrderInterface as GiftcardOrderInterface;

/**
 * Class GiftcardTest
 * Test for \Aheadworks\Giftcard\Block\Adminhtml\Order\Totals\Giftcard
 *
 * @package Aheadworks\Giftcard\Test\Unit\Block\Adminhtml\Order\Totals
 */
class GiftcardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Giftcard
     */
    private $object;

    /**
     * @var Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $factoryMock;

    /**
     * @var LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->factoryMock = $this->getMock(Factory::class, ['create'], [], '', false);
        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'layout' => $this->layoutMock
            ]
        );

        $this->object = $objectManager->getObject(
            Giftcard::class,
            [
                'context' => $contextMock,
                'factory' => $this->factoryMock
            ]
        );
    }

    /**
     * Testing of initTotals method
     */
    public function testInitTotals()
    {
        $nameInLayout = 'aw_giftcard_order_totals';
        $parentName = 'order_totals';

        $this->object->setNameInLayout($nameInLayout);
        $this->layoutMock->expects($this->exactly(2))
            ->method('getParentName')
            ->with($nameInLayout)
            ->willReturn($parentName);

        $orderMock = $this->getMockForAbstractClass(OrderInterface::class);
        $blockSalesOrderTotalsMock = $this->getMock(SalesTotals::class, ['getSource', 'addTotal'], [], '', false);
        $blockSalesOrderTotalsMock->expects($this->once())
            ->method('getSource')
            ->willReturn($orderMock);
        $this->layoutMock->expects($this->exactly(2))
            ->method('getBlock')
            ->with($parentName)
            ->willReturn($blockSalesOrderTotalsMock);

        $giftcardOrderMock = $this->getMockForAbstractClass(GiftcardOrderInterface::class);
        $orderExtensionAttributesMock = $this->getMockForAbstractClass(
            OrderExtensionInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getAwGiftcardCodes']
        );
        $orderExtensionAttributesMock->expects($this->exactly(2))
            ->method('getAwGiftcardCodes')
            ->willReturn([$giftcardOrderMock]);
        $orderMock->expects($this->exactly(4))
            ->method('getExtensionAttributes')
            ->willReturn($orderExtensionAttributesMock);

        $dataObjectMock = $this->getMock(DataObject::class, [], [], '', false);
        $this->factoryMock->expects($this->once())
            ->method('create')
            ->willReturn($dataObjectMock);
        $blockSalesOrderTotalsMock->expects($this->once())
            ->method('addTotal')
            ->with($dataObjectMock);

        $this->object->initTotals();
    }
}
