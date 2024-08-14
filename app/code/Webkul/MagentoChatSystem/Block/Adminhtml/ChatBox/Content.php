<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MagentoChatSystem
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MagentoChatSystem\Block\Adminhtml\ChatBox;

use Magento\Store\Model\ScopeInterface;

class Content extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface
     */
    protected $agreementsRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Webkul\MagentoChatSystem\Model\ChatDataConfigProvider
     */
    protected $configProvider;

    /**
     * Agreement constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Artera\Privacy\Model\Agreement                  $agreement
     * @param \Artera\Privacy\Model\Page                       $page
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\MagentoChatSystem\Model\AdminDataConfigProvider $configProvider,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->configProvider = $configProvider;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve information from carrier configuration.
     *
     * @param string $field
     *
     * @return void|false|string
     */
    public function getConfigData($field)
    {
        $path = 'chatsystem/chat_config/'.$field;
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );
    }

    public function getChatBoxConfig()
    {
        $configData = $this->configProvider->getConfig();
        $configData['loaderImage'] = $this->getViewFileUrl('Webkul_MagentoChatSystem::images/loader-2.gif');
        $configData['emojiImagesPath'] = $this->getViewFileUrl('Webkul_MagentoChatSystem::images/emojis');
        $configData['maxFileSize'] = $this->getConfigData('max_file_size');
        return $configData;
    }
}
