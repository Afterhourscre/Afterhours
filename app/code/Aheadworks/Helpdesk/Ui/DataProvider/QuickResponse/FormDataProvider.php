<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Ui\DataProvider\QuickResponse;

use Aheadworks\Helpdesk\Model\ResourceModel\QuickResponse\CollectionFactory;
use Aheadworks\Helpdesk\Model\ResourceModel\QuickResponse\Collection;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\Helpdesk\Ui\DataProvider\QuickResponse
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get('aw_helpdesk_quick_response');
        $id = $this->request->getParam($this->getRequestFieldName());
        if (!empty($dataFromForm) && isset($dataFromForm['id'])) {
            $id = $dataFromForm['id'];
            $data = $dataFromForm;
            $this->dataPersistor->clear('aw_helpdesk_quick_response');
        } else {
            $quickResponseCollection = $this->getCollection()->addFieldToFilter('id', $id)->getItems();
            /** @var \Aheadworks\Helpdesk\Model\QuickResponse $quickResponse */
            foreach ($quickResponseCollection as $quickResponse) {
                if ($id == $quickResponse->getId()) {
                    $data = $quickResponse->getData();
                }
            }
        }
        $preparedData[$id] = $this->prepareQuickResponseData($data);

        return $preparedData;
    }

    /**
     * Retrieve array with prepared quick response data
     *
     * @param array $quickResponseData
     * @return array
     */
    private function prepareQuickResponseData($quickResponseData)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            $quickResponseData['newResponse'] = false;
            $quickResponseData['editResponse'] = true;
        } else {
            $quickResponseData['newResponse'] = true;
            $quickResponseData['editResponse'] = false;
        }
        return $quickResponseData;
    }
}
