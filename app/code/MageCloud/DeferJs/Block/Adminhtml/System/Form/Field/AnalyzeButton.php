<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\DeferJs\Block\Adminhtml\System\Form\Field;

/**
 * Class AnalyzeButton
 * @package MageCloud\DeferJs\Block\Adminhtml\System\Form\Field
 */
class AnalyzeButton extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * TestButton constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->_urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $data);
    }

    /**
     * Set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('MageCloud_DeferJs::system/config/analyze_button.phtml');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'analyze_site_optimization_button',
                'label' => __('Start Analyze'),
                'onclick' => 'javascript:analyzeSiteOptimization(); return false;',
            ]
        );

        return $button->toHtml();
    }

    /**
     * @return string
     */
    public function getAnalyzeSiteOptimizationUrl()
    {
        return $this->_urlBuilder->getUrl(
            'magecloud_deferjs/optimization/analyze',
            [
                'store' => $this->_request->getParam('store')
            ]
        );
    }

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
