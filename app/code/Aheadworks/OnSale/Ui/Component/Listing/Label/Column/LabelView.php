<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\Component\Listing\Label\Column;

use Aheadworks\OnSale\Ui\Component\Listing\Label\Column\LabelView\Renderer;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Aheadworks\OnSale\Model\Source\Label\Position as PositionSource;
use Aheadworks\OnSale\Model\Source\Label\Type;
use Aheadworks\OnSale\Api\Data\LabelInterface;

/**
 * Class LabelView
 *
 * @package Aheadworks\OnSale\Ui\Component\Listing\Label\Column
 */
class LabelView extends Column
{
    /**
     * Preview text printed on label
     */
    const LABEL_PREVIEW_TEXT = 'sale';

    /**
     * @var PositionSource
     */
    private $positionSource;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PositionSource $positionSource
     * @param Renderer $renderer
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PositionSource $positionSource,
        Renderer $renderer,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->positionSource = $positionSource;
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $config['positionClassesMap'] = $this->positionSource->getPositionClassesMap();

        $this->setData('config', (array)$config);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as & $item) {
            $fieldName = $this->getData('name');
            $labelText = $item[LabelInterface::TYPE] != Type::PICTURE ? self::LABEL_PREVIEW_TEXT : '';
            $item[$fieldName] = $this->renderer->render($item, $labelText);
        }
        return $dataSource;
    }
}
