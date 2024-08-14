<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_MagentoChatSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Webkul\MagentoChatSystem\Block\Adminhtml\Edit\Tab\View;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Adminhtml agent view personal information block.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PersonalInfo extends \Magento\Backend\Block\Template
{
    protected $cumulative = 0;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Registry $registry,
        \Webkul\MagentoChatSystem\Helper\Data $helper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->dateTime = $dateTime;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get current Edit Agent Data.
     *
     * @return void
     */
    public function getAgent()
    {
        return $this->coreRegistry->registry('agent_data');
    }

    /**
     * get ratings array
     *
     * @return void
     */
    public function getAgentRatings()
    {
        return $this->helper->getAgentRating($this->getAgent()->getAgentId());
    }

    public function getTotalRating()
    {
        $ratings = $this->getAgentRatings();
        $total = 0;
        foreach ($ratings as $key => $value) {
            $total+= $value;
        }
        if ($total == 0) {
            $total = 1;
        }
        return $total;
    }

    public function getRatingTotalCount($index)
    {
        return $this->getAgentRatings()[$index];
    }

    public function getRatingPercentByValue($value)
    {
        $percent = ((int) $this->getAgentRatings()[$value]) * 100 / $this->getTotalRating();
        return $percent;
    }

    public function getAverageRating()
    {
        $ratings = $this->getAgentRatings();
        $total = 0;
        foreach ($ratings as $key => $value) {
            $total+= (int) $key * $value;
        }
        $aveg = ($total/ $this->getTotalRating());
        return $aveg;
    }

    public function getAveragePercentage()
    {
        $ratings = $this->getAgentRatings();
        $total = 0;
        foreach ($ratings as $key => $value) {
            $total+= (int) $key * $value;
        }
        
        $averageRating = $total / $this->getTotalRating();
        $maxRating = ($this->getTotalRating()) * 5;
        $totalRating = $averageRating * $this->getTotalRating();
        return ($totalRating / $maxRating) * 100;
    }
}
