<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

/**
 * Class Actions
 */
class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    private $_urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            $config = $this->getData();
            $indexField = isset($config['config']['indexField']) ?
                $config['config']['indexField'] :
                'id';

            if (isset($this->getData('config')['actions'])) {
                $actions = $this->getData('config')['actions'];
                foreach ($dataSource['data']['items'] as &$item) {
                    reset($actions);
                    $columnName = $this->getData('name');
                    foreach ($actions as $name => $action) {
                        if (!empty($action['frontendUrl'])) {
                            $href = $this->_urlBuilder->getBaseUrl() . $action['href'] . '/id/' . $item[$indexField];
                        } else {
                            $href = $this->_urlBuilder->getUrl(
                                $action['href'],
                                ['id' => $item[$indexField], 'store' => $storeId]
                            );
                        }
                        $item[$columnName][$name] = [
                            'href' => $href,
                            'label' => $action['label'],
                            'hidden' => false,
                        ];
                        if (isset($action['confirm'])) {
                            $item[$columnName][$name]['confirm'] = $action['confirm'];
                        }
                    }
                }
            }
        }

        return $dataSource;
    }
}
