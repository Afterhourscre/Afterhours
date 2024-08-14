<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MagentoChatSystem
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MagentoChatSystem\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ViewAction.
 */
class ReceiverName extends Column
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
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
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    $chatCustomer = $objectManager->create('Webkul\MagentoChatSystem\Model\CustomerData')
                        ->getCollection()
                        ->addFieldToFilter('unique_id', ['eq' => $item['receiver_unique_id']]);
                    if ($chatCustomer->getSize()) {
                        $customer = $objectManager->create(
                            'Magento\Customer\Model\Customer'
                        )->load($chatCustomer->getFirstItem()->getCustomerId());
                        $item[$this->getData('name')] = $customer->getName();
                    } else {
                        $agentCustomer = $objectManager->create('Webkul\MagentoChatSystem\Model\AgentData')
                            ->getCollection()
                            ->addFieldToFilter('agent_unique_id', ['eq' => $item['receiver_unique_id']]);

                        $agent = $objectManager->create(
                            'Magento\User\Model\User'
                        )->load($agentCustomer->getFirstItem()->getAgentId());
                        $item[$this->getData('name')] = $agent->getName();
                    }
                }
            }
        }

        return $dataSource;
    }
}
