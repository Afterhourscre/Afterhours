<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model\Category;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Faq\Model\UrlKeyValidator;
use Aheadworks\Faq\Model\Category;

/**
 * FAQ Category Validator
 */
class Validator extends AbstractValidator
{
    /**
     * @var UrlKeyValidator
     */
    private $urlKeyValidator;

    /**
     * @param UrlKeyValidator $urlKeyValidator
     */
    public function __construct(UrlKeyValidator $urlKeyValidator)
    {
        $this->urlKeyValidator = $urlKeyValidator;
    }

    /**
     * Validate Edit Category form fields
     *
     * Return FALSE if someone item is invalid
     *
     * @param Category $category
     * @return bool
     */
    public function isValid($category)
    {
        $errors = [];

        if (!\Zend_Validate::is($category->getName(), 'NotEmpty')) {
            $errors['title'] = __('Title can\'t be empty.');
        }

        if (!\Zend_Validate::is($category->getUrlKey(), 'NotEmpty')) {
            $errors['url_key'] = __('Url key can\'t be empty.');
        }

        if ($category->getSortOrder() && !\Zend_Validate::is($category->getSortOrder(), 'Digits')) {
            $errors['sort_order'] = __('Sort order must contain only digits.');
        }

        if ($category->getNumArticlesToDisplay() &&
            !\Zend_Validate::is($category->getNumArticlesToDisplay(), 'Digits')
        ) {
            $errors['num_articles'] = __('Number of articles to display must contain only digits.');
        }

        if (!$this->urlKeyValidator->isValid($category)) {
            $errors = array_merge($errors, $this->urlKeyValidator->getMessages());
        }

        $this->_addMessages($errors);

        return empty($errors);
    }
}
