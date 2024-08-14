<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-comment
 * @version   1.0.1
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Comment\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class DateFormatSource implements OptionSourceInterface
{
    private $timezone;

    public function __construct(TimezoneInterface $timezone)
    {
        $this->timezone = $timezone;
    }

    public function getAllOptions(): array
    {
        return $this->toOptionArray();
    }

    public function toOptionArray(): array
    {
        return [
            [
                'label' => $this->timezone->formatDate(new \DateTime(), \IntlDateFormatter::MEDIUM, false),
                'value' => \IntlDateFormatter::MEDIUM . '_0',
            ],
            [
                'label' => $this->timezone->formatDate(new \DateTime(), \IntlDateFormatter::FULL, false),
                'value' => \IntlDateFormatter::FULL . '_0',
            ],
            [
                'label' => $this->timezone->formatDate(new \DateTime(), \IntlDateFormatter::LONG, false),
                'value' => \IntlDateFormatter::LONG . '_0',
            ],
            [
                'label' => $this->timezone->formatDate(new \DateTime(), \IntlDateFormatter::SHORT, false),
                'value' => \IntlDateFormatter::SHORT . '_0',
            ],
            [
                'label' => $this->timezone->formatDate(new \DateTime(), \IntlDateFormatter::MEDIUM, true),
                'value' => \IntlDateFormatter::MEDIUM . '_1',
            ],
            [
                'label' => $this->timezone->formatDate(new \DateTime(), \IntlDateFormatter::FULL, true),
                'value' => \IntlDateFormatter::FULL . '_1',
            ],
            [
                'label' => $this->timezone->formatDate(new \DateTime(), \IntlDateFormatter::LONG, true),
                'value' => \IntlDateFormatter::LONG . '_1',
            ],
            [
                'label' => $this->timezone->formatDate(new \DateTime(), \IntlDateFormatter::SHORT, true),
                'value' => \IntlDateFormatter::SHORT . '_1',
            ],


        ];
    }
}
