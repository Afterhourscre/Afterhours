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
        foreach ($dataSource['data']['items'] as & $item) {
            $fieldName = $this->getData('name');
            $item[$fieldName] = $this->renderer->render(
                $item[RuleInterface::LABEL_ID],
                $item[RuleInterface::FRONTEND_LABEL_TEXT][LabelTextInterface::VALUE_LARGE]
            );
        }
        return $dataSource;
    }
}
