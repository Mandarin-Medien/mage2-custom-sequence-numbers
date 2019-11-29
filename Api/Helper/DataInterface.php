<?php
/**
 * Copyright © 2019 Mandarin Medien G.f.d.L. mbH <https://www.mandarin-medien.de/>
 * See LICENSE.txt bundled with this module for license details.
 * @license MIT license
 */

namespace Mandarin\CustomSequenceNumbers\Api\Helper;

use Magento\Framework\DB\Sequence\SequenceInterface;
use Magento\Sales\Model\Order;

/**
 * Interface DataInterface
 * @package Mandarin\CustomSequenceNumbers\Api\Helper
 */
interface DataInterface
{
    /**#@+ Flag codes */
    const
        FLAG_CODE               = 'customsequencenumbers';
    /**#@-*/

    /**#@+ Config keys */
    const
        KEY_RESETPATTERN        = 'resetpattern',
        KEY_RESETREFERENCE      = 'resetreference',
        KEY_PREFIXPATTERN       = 'prefixpattern',
        KEY_USECUSTOMFORMULA    = 'usecustomformula';
    /**#@-*/


    /**
     * @return int
     */
    public function getCurrentStoreId();


    /**
     * @param null|int|string $storeId
     * @return int|null
     */
    public function getStoreId($storeId = null);


    /**
     * @return string
     */
    public function getOrderEntityType();


    /**
     * @return string
     */
    public function getInvoiceEntityType();


    /**
     * @return string
     */
    public function getShipmentEntityType();


    /**
     * @return string
     */
    public function getCreditmemoEntityType();


    /**
     * @param null|string $entityType
     * @return array|bool|float|int|null|string
     */
    public function getConfig($entityType = null);


    /**
     * @param string $entityType
     * @param null|int|string $storeId
     * @return SequenceInterface
     */
    public function getSequence($entityType = Order::ENTITY, $storeId = null);


    /**
     * @param string $entityType
     * @param null|int|string $storeId
     * @return int
     */
    public function getSequenceCounter($entityType = Order::ENTITY, $storeId = null);


    /**
     * @param string $entityType
     * @param null|int|string $storeId
     * @return string
     */
    public function getSequencePrefix($entityType = Order::ENTITY, $storeId = null);


    /**
     * @param string $entityType
     * @return string
     */
    public function getCurrentPrefix($entityType = Order::ENTITY);


    /**
     * @param string $entityType
     * @return string
     */
    public function getPrefixPattern($entityType = Order::ENTITY);


    /**
     * @param string $entityType
     * @param null|int|string $storeId
     * @return bool
     */
    public function isPrefixUpdateRequired($entityType = Order::ENTITY, $storeId = null);


    /**
     * @param string $entityType
     * @return string
     */
    public function getResetPattern($entityType = Order::ENTITY);


    /**
     * @param string $entityType
     * @return string
     */
    public function getCurrentResetValue($entityType = Order::ENTITY);


    /**
     * @param string $entityType
     * @return string
     */
    public function getReferenceResetValue($entityType = Order::ENTITY);


    /**
     * @param string $entityType
     * @return $this
     */
    public function updateResetValue($entityType = Order::ENTITY);


    /**
     * @param string $entityType
     * @return bool
     */
    public function isResetRequired($entityType = Order::ENTITY);


    /**
     * @param string $entityType
     * @return bool
     */
    public function isUsingCustomFormula($entityType = Order::ENTITY);
}
