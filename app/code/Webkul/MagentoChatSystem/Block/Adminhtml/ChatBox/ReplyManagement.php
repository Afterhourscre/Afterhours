<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MagentoChatSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MagentoChatSystem\Block\Adminhtml\ChatBox;

use Magento\Store\Model\ScopeInterface;

class ReplyManagement extends \Magento\Backend\Block\Template
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
        \Webkul\MagentoChatSystem\Model\ChatDataConfigProvider $configProvider,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->configProvider = $configProvider;
        parent::__construct($context, $data);
    }

    public function getAttachementImage()
    {
        return $this->getViewFileUrl('Webkul_MagentoChatSystem::images/attachment.png');
    }

    public function getDownloadImage()
    {
        return $this->getViewFileUrl('Webkul_MagentoChatSystem::images/download.png');
    }
}
