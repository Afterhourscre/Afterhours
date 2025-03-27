<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\AdvancedReviews\Model\Review;
use Magento\Store\Model\Store;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\Digits;

/**
 * Class Common
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Validator
 */
class Common extends AbstractValidator
{
    /**
     * Validate required review data
     *
     * @param Review $review
     * @return bool
     */
    public function isValid($review)
    {
        $errors = [];
        $notEmptyValidator = new NotEmpty();
        $digitsValidator = new Digits();

        if (!$notEmptyValidator->isValid($review->getCreatedAt())) {
            $errors[] = __('Created At can\'t be empty.');
        }
        if (!$notEmptyValidator->isValid($review->getRating())) {
            $errors[] = __('Rating can\'t be empty.');
        }
        if (!$notEmptyValidator->isValid($review->getNickname())) {
            $errors[] = __('Nickname can\'t be empty.');
        }
        if (!$notEmptyValidator->isValid($review->getContent())) {
            $errors[] = __('Content can\'t be empty.');
        }
        if (!$notEmptyValidator->isValid($review->getStoreId())) {
            $errors[] = __('Store ID can\'t be empty.');
        }
        if (!$notEmptyValidator->isValid($review->getProductId())) {
            $errors[] = __('Product ID can\'t be empty.');
        }
        if (!$notEmptyValidator->isValid($review->getStatus())) {
            $errors[] = __('Status can\'t be empty.');
        }
        if (!$notEmptyValidator->isValid($review->getAuthorType())) {
            $errors[] = __('Author Type can\'t be empty.');
        }
        if ($review->getVotesPositive() && !$digitsValidator->isValid($review->getVotesPositive())) {
            $errors[] = __('Votes Positive must contain only digits.');
        }
        if ($review->getVotesNegative() && !$digitsValidator->isValid($review->getVotesNegative())) {
            $errors[] = __('Votes Negative must contain only digits.');
        }
        if (!$notEmptyValidator->isValid($review->getAuthorType())) {
            $errors[] = __('Author Type can\'t be empty.');
        }
        if ($review->getStoreId() == Store::DEFAULT_STORE_ID
            && !$notEmptyValidator->isValid($review->getSharedStoreIds())) {
            $errors[] = __('You need to select at least one store view to publish it on frontend.');
        }
        $this->_addMessages($errors);

        return empty($errors);
    }
}
