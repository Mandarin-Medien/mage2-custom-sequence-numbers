<?php
/**
 * Copyright © 2019 Mandarin Medien G.f.d.L. mbH <https://www.mandarin-medien.de/>
 * See LICENSE.txt bundled with this module for license details.
 * @license MIT license
 */

namespace Mandarin\CustomSequenceNumbers\Model\SalesSequence;

use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Model\AbstractModel;
use Magento\SalesSequence\Model\Meta;
use Magento\SalesSequence\Model\ResourceModel\Profile as ProfileResource;
use Magento\SalesSequence\Model\Sequence as MageSequence;
use Mandarin\CustomSequenceNumbers\Api\Helper\DataInterface;
use Mandarin\CustomSequenceNumbers\Api\Model\ResetInterface;

/**
 * CustomSequenceNumbers - Reset Model
 * Extends default sequence model by functions to reset data
 *
 * @package Mandarin\CustomSequenceNumbers\Model\SalesSequence
 */
class Sequence extends MageSequence implements ResetInterface
{
    /** @var string */
    private $lastIncrementId;

    /** @var Meta */
    private $meta;

    /** @var string */
    private $pattern;

    /** @var false|\Magento\Framework\DB\Adapter\AdapterInterface */
    private $connection;

    /** @var ProfileResource */
    private $_profileResource = null;

    /** @var DataInterface */
    private $_helper = null;

    /**
     * Sequence constructor.
     *
     * @param Meta            $meta
     * @param AppResource     $resource
     * @param ProfileResource $profileResource
     * @param DataInterface   $helper
     * @param string          $pattern
     */
    public function __construct(
        Meta            $meta,
        AppResource     $resource,
        ProfileResource $profileResource,
        DataInterface   $helper,
                        $pattern = MageSequence::DEFAULT_PATTERN
    )
    {
        MageSequence::__construct($meta, $resource, $pattern);

        $this->meta         = $meta;
        $this->connection   = $resource->getConnection('sales');
        $this->pattern      = $pattern;

        $this->_profileResource = $profileResource;
        $this->_helper          = $helper;
    }


    /**
     * Updates the profile data if needed
     *
     * @throws AlreadyExistsException
     */
    protected function verifyProfile()
    {
        $updatedProfile = false;

        if ($this->getHelper()->isPrefixUpdateRequired($this->getEntityType())) {
            $updatedProfile = true;
            $this->getActiveProfile()->setData('prefix', $this->getHelper()->getCurrentPrefix($this->getEntityType()));
        }

        if ($this->getHelper()->isResetRequired($this->getEntityType())) {
            $updatedProfile = true;
            $this->getActiveProfile()->setData('start_value', $this->getLastIncrementId());
            $this->getHelper()->updateResetValue($this->getEntityType());

            unset($this->lastIncrementId);
            $this->getLastIncrementId();
        }

        if ($updatedProfile) {
            $this->getProfileResource()->save($this->getActiveProfile());
        }
    }


    /**
     * @return ProfileResource
     */
    protected function getProfileResource()
    {
        return $this->_profileResource;
    }


    /**
     * @return DataInterface
     */
    protected function getHelper()
    {
        return $this->_helper;
    }


    /**
     * @return int
     */
    public function getLastIncrementId()
    {
        $select = $this->connection->select()->from(
            $this->getSequenceTable(),
            ['MAX(sequence_value) AS value']
        );
        $this->lastIncrementId = $this->connection->fetchOne($select);

        return $this->lastIncrementId;
    }


    /**
     * @return Meta
     */
    protected function getMeta()
    {
        return $this->meta;
    }


    /**
     * @return string
     */
    public function getEntityType()
    {
        return $this->getMeta()->getData('entity_type');
    }


    /**
     * @return mixed
     */
    protected function getSequenceTable()
    {
        return $this->getMeta()->getData('sequence_table');
    }


    /**
     * @return mixed|AbstractModel
     */
    protected function getActiveProfile()
    {
        return $this->getMeta()->getData('active_profile');
    }


    /**
     * @return int
     */
    public function getStartValue()
    {
        return $this->getActiveProfile()->getData('start_value');
    }


    /**
     * @return int
     */
    public function getStep()
    {
        return $this->getActiveProfile()->getData('step');
    }


    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->getActiveProfile()->getData('prefix');
    }


    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->getActiveProfile()->getData('suffix');
    }


    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }


    /**
     * @return string
     * @throws AlreadyExistsException
     */
    public function getNextValue()
    {
        $this->verifyProfile();

        return parent::getNextValue();
    }


    /**
     * Calculate current value depends on start value
     *
     * overrides parent method
     *
     * @return string
     */
    private function calculateCurrentValue()
    {
        $incrementId = $this->getLastIncrementId();
        $startValue = $this->getStartValue();
        $step = $this->getStep();

        return ($incrementId - $startValue) * $step;
    }


    /**
     * Retrieve current value
     *
     * @return string
     * @throws AlreadyExistsException
     */
    public function getCurrentValue()
    {
        $this->verifyProfile();

        $useCustomFormula = $this->getHelper()->isUsingCustomFormula($this->getEntityType());

        if (!$useCustomFormula) {
            return parent::getCurrentValue();
        }

        if ($this->getLastIncrementId() === null) {
            return null;
        }

        return sprintf(
            $this->getPattern(),
            $this->getPrefix(),
            $this->calculateCurrentValue(),
            $this->getSuffix()
        );
    }
}
