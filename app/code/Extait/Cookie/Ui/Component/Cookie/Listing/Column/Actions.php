<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Cookie\Ui\Component\Cookie\Listing\Column;

use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory as CookieCollectionFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Actions extends Column
{
    /**
     * @var \Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory
     */
    protected $cookieCollectionFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Actions constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory $cookieCollectionFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CookieCollectionFactory $cookieCollectionFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->cookieCollectionFactory = $cookieCollectionFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Add an edit action to the cookie_listing.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $cookieCollection = $this->cookieCollectionFactory->create();

            foreach ($dataSource['data']['items'] as &$item) {
                /** @var \Extait\Cookie\Api\Data\CookieInterface $cookie */
                $cookie = $cookieCollection->getItemById($item[CookieInterface::ID]);

                $item[$this->getData('name')]['delete'] = [
                    'href' => $this->urlBuilder->getUrl('cookie/cookie/edit', ['id' => $cookie->getId()]),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
