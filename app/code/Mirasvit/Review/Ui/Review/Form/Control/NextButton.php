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
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Ui\Review\Form\Control;


use Mirasvit\Review\Api\Data\ReviewInterface;

class NextButton extends ButtonAbstract
{
    public function getButtonData()
    {
        if ($action = $this->getNextAction()) {
            return [
                'label'      => __('Next'),
                'on_click'   => $action,
                'class'      => 'next',
                'sort_order' => 50,
            ];
        }

        return [];
    }

    private function getNextAction(): ?string
    {
        if ($nextReview = $this->getNextReview()) {
            return sprintf(
                "location.href = '%s';",
                $this->getUrl('*/*/edit', [ReviewInterface::ID => $nextReview->getId()])
            );
        }

        return null;
    }
}
