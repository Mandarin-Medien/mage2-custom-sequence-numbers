<?php
/**
 * Copyright © 2019 Mandarin Medien G.f.d.L. mbH <https://www.mandarin-medien.de/>
 * See LICENSE.txt bundled with this module for license details.
 * @license MIT license
 */

namespace MandarinMedien\CustomSequenceNumbers\Helper;

use Magento\Framework\DB\Sequence\SequenceInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\FlagManager;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;
use Magento\SalesSequence\Model\Manager as SequenceManager;
use Magento\Store\Model\StoreManagerInterface;
use MandarinMedien\CustomSequenceNumbers\Api\Helper\DataInterface;
use MandarinMedien\CustomSequenceNumbers\Api\Model\ResetInterface;

/**
 * CustomSequenceNumbers - Default Helper
 * Helps customizing increment ids
 *
 * @package MandarinMedien\CustomSequenceNumbers\Helper
 */
class Data implements DataInterface
{
    /** @var FlagManager */
    private $_flagManager = null;

    /** @var Order */
    private $_order = null;

    /** @var Invoice */
    private $_invoice = null;

    /** @var Shipment */
    private $_shipment = null;

    /** @var Creditmemo */
    private $_creditmemo = null;

    /** @var SequenceManager */
    private $_sequenceManager = null;

    /** @var StoreManagerInterface */
    private $_storeManager = null;

    /**
     * Data constructor.
     *
     * @param FlagManager           $flagManager
     * @param SequenceManager       $sequenceManager
     * @param StoreManagerInterface $storeManager
     * @param Order                 $order
     * @param Invoice               $invoice
     * @param Shipment              $shipment
     * @param Creditmemo            $creditmemo
     */
    public function __construct(
        FlagManager             $flagManager,
        SequenceManager         $sequenceManager,
        StoreManagerInterface   $storeManager,
        Order                   $order,
        Invoice                 $invoice,
        Shipment                $shipment,
        Creditmemo              $creditmemo
    )
    {
        $this->_flagManager     = $flagManager;
        $this->_sequenceManager = $sequenceManager;
        $this->_storeManager    = $storeManager;
        $this->_order           = $order;
        $this->_invoice         = $invoice;
        $this->_shipment        = $shipment;
        $this->_creditmemo      = $creditmemo;
    }


    /**
     * @return FlagManager
     */
    protected function getFlagManager()
    {
        return $this->_flagManager;
    }


    /**
     * @return SequenceManager
     */
    protected function getSequenceManager()
    {
        return $this->_sequenceManager;
    }


    /**
     * @return StoreManagerInterface
     */
    protected function getStoreManager()
    {
        return $this->_storeManager;
    }


    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->getStoreManager()->getStore()->getId();
    }


    /**
     * @param null|int|string $storeId
     * @return int|null
     * @throws NoSuchEntityException
     */
    public function getStoreId($storeId = null)
    {
        return is_null($storeId) ? $this->getCurrentStoreId() : $storeId;
    }


    /**
     * @return Order
     */
    protected function getOrder()
    {
        return $this->_order;
    }


    /**
     * @return Invoice
     */
    protected function getInvoice()
    {
        return $this->_invoice;
    }


    /**
     * @return Shipment
     */
    protected function getShipment()
    {
        return $this->_shipment;
    }


    /**
     * @return Creditmemo
     */
    protected function getCreditmemo()
    {
        return $this->_creditmemo;
    }


    /**
     * @return string
     */
    public function getOrderEntityType()
    {
        return $this->getOrder()->getEntityType();
    }


    /**
     * @return string
     */
    public function getInvoiceEntityType()
    {
        return $this->getInvoice()->getEntityType();
    }


    /**
     * @return string
     */
    public function getShipmentEntityType()
    {
        return $this->getShipment()->getEntityType();
    }


    /**
     * @return string
     */
    public function getCreditmemoEntityType()
    {
        return $this->getCreditmemo()->getEntityType();
    }


    /**
     * @param null|string $entityType
     * @return array|bool|float|int|null|string
     * @throws \Exception
     */
    public function getConfig($entityType = null)
    {
        $data = $this->getFlagManager()->getFlagData(static::FLAG_CODE);
        if (!$data) {
            $data = [
                $this->getOrderEntityType() => [
                    static::KEY_RESETPATTERN     => 'Y',
                    static::KEY_RESETREFERENCE   => null,
                    static::KEY_PREFIXPATTERN    => 'ymd',
                    static::KEY_USECUSTOMFORMULA => true,
                ],
                $this->getInvoiceEntityType() => [
                    static::KEY_RESETPATTERN     => 'Y',
                    static::KEY_RESETREFERENCE   => null,
                    static::KEY_PREFIXPATTERN    => 'ymd',
                    static::KEY_USECUSTOMFORMULA => true,
                ],
                $this->getShipmentEntityType() => [
                    static::KEY_RESETPATTERN     => 'Y',
                    static::KEY_RESETREFERENCE   => null,
                    static::KEY_PREFIXPATTERN    => 'ymd',
                    static::KEY_USECUSTOMFORMULA => true,
                ],
                $this->getCreditmemoEntityType() => [
                    static::KEY_RESETPATTERN     => 'Y',
                    static::KEY_RESETREFERENCE   => null,
                    static::KEY_PREFIXPATTERN    => 'ymd',
                    static::KEY_USECUSTOMFORMULA => true,
                ],
            ];

            $this->getFlagManager()->saveFlag(static::FLAG_CODE, $data);
        }

        if (!is_null($entityType)) {
            if (!array_key_exists($entityType, $data)) {
                throw new \Exception(sprintf('Entity type "%s" is not configured for sales sequences', $entityType));
            }

            $data = $data[$entityType];
        }

        return $data;
    }


    /**
     * @param array $config
     */
    protected function updateConfig($config)
    {
        $this->getFlagManager()->saveFlag(static::FLAG_CODE, $config);
    }

    /**
     * @param string          $entityType
     * @param null|int|string $storeId
     * @return SequenceInterface|ResetInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSequence($entityType = Order::ENTITY, $storeId = null)
    {
        $storeId = $this->getStoreId($storeId);
        return $this->getSequenceManager()->getSequence($entityType, $storeId);
    }

    /**
     * @param string          $entityType
     * @param null|int|string $storeId
     * @return int
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSequenceCounter($entityType = Order::ENTITY, $storeId = null)
    {
        return $this->getSequence($entityType, $storeId)->getLastIncrementId();
    }

    /**
     * @param string          $entityType
     * @param null|int|string $storeId
     * @return string
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSequencePrefix($entityType = Order::ENTITY, $storeId = null)
    {
        return $this->getSequence($entityType, $storeId)->getPrefix();
    }


    /**
     * @param string $entityType
     * @return string
     * @throws \Exception
     */
    public function getCurrentPrefix($entityType = Order::ENTITY)
    {
        return date($this->getPrefixPattern($entityType));
    }


    /**
     * @param string $entityType
     * @return string
     * @throws \Exception
     */
    public function getPrefixPattern($entityType = Order::ENTITY)
    {
        $config = $this->getConfig($entityType);
        return $config[static::KEY_PREFIXPATTERN];
    }


    /**
     * @param string $entityType
     * @param null|int|string $storeId
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function isPrefixUpdateRequired($entityType = Order::ENTITY, $storeId = null)
    {
        return $this->getCurrentPrefix($entityType) != $this->getSequencePrefix($entityType, $storeId);
    }


    /**
     * @param string $entityType
     * @return string
     * @throws \Exception
     */
    public function getResetPattern($entityType = Order::ENTITY)
    {
        $config = $this->getConfig($entityType);
        return $config[static::KEY_RESETPATTERN];
    }


    /**
     * @param string $entityType
     * @return string
     * @throws \Exception
     */
    public function getCurrentResetValue($entityType = Order::ENTITY)
    {
        return date($this->getResetPattern($entityType));
    }


    /**
     * @param string $entityType
     * @return string
     * @throws \Exception
     */
    public function getReferenceResetValue($entityType = Order::ENTITY)
    {
        $config = $this->getConfig($entityType);
        return $config[static::KEY_RESETREFERENCE];
    }


    /**
     * @param string $entityType
     * @return $this
     * @throws \Exception
     */
    public function updateResetValue($entityType = Order::ENTITY)
    {
        $current = $this->getCurrentResetValue($entityType);

        $config = $this->getConfig();
        $config[$entityType][static::KEY_RESETREFERENCE] = $current;

        $this->updateConfig($config);

        return $this;
    }


    /**
     * @param string $entityType
     * @return bool
     * @throws \Exception
     */
    public function isResetRequired($entityType = Order::ENTITY)
    {
        return $this->getCurrentResetValue($entityType) != $this->getReferenceResetValue($entityType);
    }


    /**
     * @param string $entityType
     * @return bool
     * @throws \Exception
     */
    public function isUsingCustomFormula($entityType = Order::ENTITY)
    {
        $config = $this->getConfig($entityType);
        return (bool)$config[static::KEY_USECUSTOMFORMULA];
    }
}
