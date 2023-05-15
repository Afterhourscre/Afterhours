<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model\Article;

use \Magento\Framework\Validator\AbstractValidator;
use \Aheadworks\Faq\Model\UrlKeyValidator;
use \Aheadworks\Faq\Model\Article;

/**
 * FAQ Article Validator
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
     * Validate article data
     *
     * @param Article $article
     * @return bool     Return FALSE if someone item is invalid
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function isValid($article)
    {
        $errors = [];
        $voteFieldsIsValid = 1;

        if (!\Zend_Validate::is($article->getTitle(), 'NotEmpty')) {
            $errors['title'] = __('Title can\'t be empty.');
        }

        if (!\Zend_Validate::is($article->getUrlKey(), 'NotEmpty')) {
            $errors['url_key'] = __('Url key can\'t be empty.');
        }

        if ($article->getSortOrder() && !\Zend_Validate::is($article->getSortOrder(), 'Digits')) {
            $errors['sort_order'] = __('Sort order must contain only digits.');
        }

        if ($article->getVotesYes() && !\Zend_Validate::is($article->getVotesYes(), 'Digits')) {
            $errors['votes_yes'] = __('Number of helpful votes must contain only digits.');
            $voteFieldsIsValid *= 0;
        }

        if ($article->getTotalVotes() && !\Zend_Validate::is($article->getTotalVotes(), 'Digits')) {
            $errors['total_votes'] = __('Number of total votes must contain only digits.');
            $voteFieldsIsValid *= 0;
        }

        if ($voteFieldsIsValid) {
            if ($article->getTotalVotes() < $article->getVotesYes()) {
                $errors['votes_not'] = __('Number of total votes can\'t be less Number of helpful votes.');
            }
        }

        if (!$this->urlKeyValidator->isValid($article)) {
            $errors = array_merge($errors, $this->urlKeyValidator->getMessages());
        }

        $this->_addMessages($errors);

        return empty($errors);
    }

    /**
     * Validate question form data
     *
     * @param array $formData
     * @return array
     */
    public function validateQuestionFormData($formData)
    {
        $errors = [];

        if (!isset($formData['name']) || trim($formData['name']) == '') {
            $errors[] = __('Name is required');
        }
        if (!isset($formData['email']) || trim($formData['email']) == '') {
            $errors[] = __('Email is required');
        }
        if (!isset($formData['question_content']) || trim($formData['question_content']) == '') {
            $errors[] = __('Question content is required');
        }

        return $errors;
    }
}
