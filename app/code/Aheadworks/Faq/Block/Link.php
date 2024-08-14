<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Html\Link as LinkElement;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Model\Config;

/**
 * Class Link
 */
class Link extends LinkElement
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param Url $url
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Url $url,
        Config $config,
        array $data = []
    ) {
        $this->url = $url;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve FAQ Homepage URL
     *
     * @return string
     */
    public function getHref()
    {
        return $this->url->getFaqHomeUrl();
    }

    /**
     * Retrieve FAQ Storefront Name
     *
     * @return string
     */
    public function getFaqName()
    {
        return $this->config->getFaqName();
    }

    /**
     * Get relevant path to template
     *
     * @return bool|string
     */
    public function getTemplate()
    {
        if ($this->config->isDisabledFaqForCurrentCustomer()) {
            return false;
        } else {
            return parent::getTemplate();
        }
    }
}
