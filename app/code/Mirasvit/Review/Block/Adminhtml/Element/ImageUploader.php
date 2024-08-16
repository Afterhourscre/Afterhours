<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Review\Block\Adminhtml\Element;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Math\Random;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\Dynamic\Category;

class ImageUploader extends AbstractElement
{
    public $layout;

    public function __construct(
        LayoutInterface $layout,
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = [],
        ?SecureHtmlRenderer $secureRenderer = null,
        ?Random $random = null
    ) {
        $this->setId('photos');
        $this->layout = $layout;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data, $secureRenderer, $random);
    }

    public function toHtml(): string
    {
        return $this->layout
            ->createBlock('Mirasvit\Review\Block\Product\Review\Uploader')
            ->setTemplate('Mirasvit_Review::review/image-uploader.phtml')
            ->toHtml();
    }

}
