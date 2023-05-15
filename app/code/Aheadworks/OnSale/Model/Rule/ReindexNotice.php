<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule;

use Aheadworks\OnSale\Model\Rule\ReindexNotice\Flag as ReindexFlag;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ReindexNotice
 *
 * @package Aheadworks\OnSale\Model\Rule
 */
class ReindexNotice
{
    /**
     * @var ReindexFlag
     */
    private $flag;

    /**
     * @param ReindexFlag $flag
     */
    public function __construct(ReindexFlag $flag)
    {
        $this->flag = $flag;
    }

    /**
     * Enable notice
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    public function setEnabled()
    {
        $this->flag
            ->loadSelf()
            ->setState(1)
            ->save();
    }

    /**
     * Disable notice
     * @throws LocalizedException
     * @throws \Exception
     */
    public function setDisabled()
    {
        $this->flag
            ->loadSelf()
            ->setState(0)
            ->save();
    }

    /**
     * Check if reindex notice is enabled
     *
     * @return int
     */
    public function isEnabled()
    {
        $state = 0;
        try {
            $state = $this->flag
                ->loadSelf()
                ->getState();
        } catch (LocalizedException $e) {
        }

        return $state;
    }

    /**
     * Get reindex notice text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getText()
    {
        return __('Some rules are updated but not applied. Please click "Apply Rules" to update catalog.');
    }
}
