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

namespace Mirasvit\Review\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class ReviewActions extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')]['edit'] = [
                'href'   => $this->context->getUrl(
                    'mst_review/review/edit',
                    ['review_id' => $item['review_id'], 'product_id' => $item['entity_id']]
                ),
                'label'  => __('Edit'),
                'hidden' => false,
            ];
        }

        return $dataSource;
    }
}
