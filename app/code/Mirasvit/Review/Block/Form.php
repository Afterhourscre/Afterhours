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


declare(strict_types=1);


namespace Mirasvit\Review\Block;

use Mirasvit\Review\Model\ConfigProvider;

class Form extends \Magento\Review\Block\Form
{
    protected $configProvider;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ConfigProvider $configProvider,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Review\Helper\Data $reviewData,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    )
    {
        $this->configProvider = $configProvider;
        parent::__construct($context, $urlEncoder, $reviewData, $productRepository, $ratingFactory, $messageManager, $httpContext, $customerUrl, $data, $serializer);
    }

    public function displayProsAndCons(): bool
    {
        return $this->configProvider->displayProsAndCons();
    }

    public function getAction()
    {
        return $this->getUrl(
            'mst_review/review/post',
            [
                '_secure' => $this->getRequest()->isSecure(),
                'id' => $this->getProductId(),
            ]
        );
    }
}
