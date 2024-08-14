<?php
namespace WeltPixel\InstagramWidget\Block\Widget;

class Instagram extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @return string
     */
    public function getTemplate()
    {
        $instagramApiType = $this->getData('instagram_api_type');
        switch ($instagramApiType) {
            case 'javascript_parser':
                $template = 'widget/js/instagram_widget.phtml';
                break;
            default:
                $template = 'widget/instagram_widget.phtml';
                break;
        }

        $this->setTemplate($template);
        return parent::getTemplate();
    }
}
