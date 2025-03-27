<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\Component\Listing\Rule\Column;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\Data\LabelTextInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Grid\Collection;
use Aheadworks\OnSale\Ui\Component\Listing\Label\Column\LabelView as LabelColumnView;

/**
 * Class LabelView
 *
 * @package Aheadworks\OnSale\Ui\Component\Listing\Rule\Column
 */
class LabelView extends LabelColumnView
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $config['positionFieldName'] = Collection::LABEL_POSITION;

        $this->setData('config', (array)$config);
    }

    /**
     * {@inheritdoc}
     */
  public function prepareDataSource(array $dataSource)
{
    foreach ($dataSource['data']['items'] as &$item) {
        $fieldName = $this->getData('name');

        $labelId = $item[RuleInterface::LABEL_ID] ?? null;
        $frontendLabelText = $item[RuleInterface::FRONTEND_LABEL_TEXT][LabelTextInterface::VALUE_LARGE] ?? null;

        if ($labelId !== null && $frontendLabelText !== null) {
            $item[$fieldName] = $this->renderer->render(
                $labelId,
                $frontendLabelText
            );
        } else {
            // Handle the case where the necessary data is not available
            $item[$fieldName] = ''; // Or any default value or handling logic
        }
    }
    return $dataSource;
}

}
