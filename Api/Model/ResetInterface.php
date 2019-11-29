<?php
/**
 * Copyright © 2019 Mandarin Medien G.f.d.L. mbH <https://www.mandarin-medien.de/>
 * See LICENSE.txt bundled with this module for license details.
 * @license MIT license
 */

namespace MandarinMedien\CustomSequenceNumbers\Api\Model;

use Magento\Framework\DB\Sequence\SequenceInterface;

/**
 * Interface ResetInterface
 * @package MandarinMedien\CustomSequenceNumbers\Api\Model
 */
interface ResetInterface extends SequenceInterface
{
    /**
     * @return int
     */
    public function getLastIncrementId();


    /**
     * @return string
     */
    public function getEntityType();


    /**
     * @return int
     */
    public function getStartValue();


    /**
     * @return int
     */
    public function getStep();


    /**
     * @return string
     */
    public function getPrefix();


    /**
     * @return string
     */
    public function getSuffix();


    /**
     * @return string
     */
    public function getPattern();
}
