<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\DataProvider\Label;

use Aheadworks\OnSale\Model\Label\Image\Info;
use Aheadworks\OnSale\Model\ResourceModel\Label\CollectionFactory;
use Aheadworks\OnSale\Model\ResourceModel\Label\Collection;
use Aheadworks\OnSale\Model\Source\Label\Position;
use Aheadworks\OnSale\Model\Source\Label\Type;
use Aheadworks\OnSale\Model\Source\Label\Shape\Type as ShapeType;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Size as LabelSize;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\OnSale\Ui\DataProvider\Label
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_onsale_label';

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
     * @var Info
     */
    private $imageInfo;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param Info $imageInfo
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
        Info $imageInfo,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->imageInfo = $imageInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $preparedData = [];
        $dataFromForm = $this->dataPersistor->get(self::DATA_PERSISTOR_FORM_DATA_KEY);

        if (!empty($dataFromForm) && (is_array($dataFromForm)) && (!empty($dataFromForm[LabelInterface::LABEL_ID]))) {
            $id = $dataFromForm[LabelInterface::LABEL_ID];
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
            $preparedData[$id] = $dataFromForm;
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            $labels = $this->getCollection()->addFieldToFilter(LabelInterface::LABEL_ID, $id)->getItems();
            /** @var \Aheadworks\OnSale\Model\Label $label */
            foreach ($labels as $label) {
                if ($id == $label->getId()) {
                    $preparedData[$id] = $this->getPreparedLabelData($label->getData());
                }
            }
        }

        if (empty($preparedData)) {
            $preparedData[$id] = $this->getPreparedDefaultLabelData();
        }

        return $preparedData;
    }

    /**
     * Retrieve array with prepared label data
     *
     * @param array $labelData
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getPreparedLabelData($labelData)
    {
        if ($labelData[LabelInterface::TYPE] == Type::PICTURE) {
            $imageFileName = $labelData[LabelInterface::IMG_FILE];
            $labelData[LabelInterface::IMG_FILE] = [
                0 => [
                    'file' => $imageFileName,
                    'url' => $this->imageInfo->getMediaUrl($imageFileName),
                    'size' => $this->imageInfo->getStat($imageFileName)['size'],
                    'type' => 'image'
                ]
            ];
        }
        if ($labelData[LabelInterface::TYPE] != Type::SHAPE) {
            $labelData[LabelInterface::SHAPE_TYPE] = ShapeType::RECTANGLE;
        }
        $labelData['size_switcher'] = LabelSize::LARGE;

        return $labelData;
    }

    /**
     * Retrieve array with prepared label data on default
     *
     * @return array
     */
    private function getPreparedDefaultLabelData()
    {
        $labelData = [
            LabelInterface::POSITION => Position::TOP_LEFT,
            LabelInterface::TYPE => Type::SHAPE,
            LabelInterface::SHAPE_TYPE => ShapeType::RECTANGLE,
            'size_switcher' => LabelSize::LARGE
        ];
        return $labelData;
    }
}
