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

namespace Mirasvit\Review\Block\Customer;

use Mirasvit\Review\Repository\ReviewRepository;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Review\Model\ReviewFactory;
use Magento\Review\Model\Rating\Option\VoteFactory;
use Magento\Review\Model\RatingFactory;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Review\Block\Customer\View as CustomerView;
use Mirasvit\Review\Model\ConfigProvider;

class View extends CustomerView
{
    protected $reviewRepository;

    protected $configProvider;

    protected $_template = 'Mirasvit_Review::customer/view.phtml';

    public function __construct(
        ReviewRepository           $reviewRepository,
        ConfigProvider             $configProvider,
        Context                    $context,
        ProductRepositoryInterface $productRepository,
        ReviewFactory              $reviewFactory,
        VoteFactory                $voteFactory,
        RatingFactory              $ratingFactory,
        CurrentCustomer            $currentCustomer,
        array                      $data = []
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->configProvider   = $configProvider;
        parent::__construct($context, $productRepository, $reviewFactory, $voteFactory, $ratingFactory, $currentCustomer, $data);
    }

    public function displayProsAndCons(): bool
    {
        return $this->configProvider->displayProsAndCons();
    }

    public function getReviewData()
    {
        if ($this->getReviewId() && !$this->getReviewCachedData()) {
            $this->setReviewCachedData($this->reviewRepository->get((int)$this->getReviewId()));
        }

        return $this->getReviewCachedData();
    }
}
