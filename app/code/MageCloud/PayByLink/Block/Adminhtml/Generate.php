<?php

namespace MageCloud\PayByLink\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Paypal\Model\Config;

/**
 * Class Generate
 * @package MageCloud\PayByLink\Block\Adminhtml
 */
class Generate extends Field
{
    /**
     * @var string
     */
    protected $_template = 'MageCloud_PayByLink::system/config/generate.phtml';

    /**
     * @var Config
     */
    private $config;

    /**
     * Credential constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        $this->config = $config;

        parent::__construct($context, $data);
    }

    /**
     * Remove scope label
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getButtonHtml()
    {
        /** @var Button $button */
        $button = $this->getLayout()->createBlock(Button::class);

        $button->setData([
            'id'    => 'mpbarclaycard-generate-button',
            'label' => __('Generate link'),
            'class' => 'primary'
        ]);

        return $button->toHtml();
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('paybylink/index/generate', ['form_key' => $this->getFormKey()]);
    }

    /**
     * @return string
     */
    public function getMerchantCountry()
    {
        return strtolower($this->config->getMerchantCountry());
    }
}
