<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse;

use Aheadworks\Helpdesk\Api\Data\QuickResponseInterface;

/**
 * Class PostDataProcessor
 *
 * @package Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse
 */
class PostDataProcessor
{
    /**
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     */
    public function prepareEntityData($data)
    {
        $data = $this->prepareResponseValues($data);

        return $data;
    }

    /**
     * Prepare response values
     *
     * @param array $data
     * @return array
     */
    private function prepareResponseValues($data)
    {
        $responseValues = isset($data[QuickResponseInterface::STORE_RESPONSE_VALUES])
            ? $data[QuickResponseInterface::STORE_RESPONSE_VALUES]
            : [];
        foreach ($responseValues as $key => $responseValue) {
            if (isset($responseValue['delete']) && $responseValue['delete']) {
                unset($data[QuickResponseInterface::STORE_RESPONSE_VALUES][$key]);
            }
        }

        return $data;
    }
}
