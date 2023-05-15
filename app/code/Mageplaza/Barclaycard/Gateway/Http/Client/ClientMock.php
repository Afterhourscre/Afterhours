<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Barclaycard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Barclaycard\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Mageplaza\Barclaycard\Gateway\Config\Direct;
use Mageplaza\Barclaycard\Helper\Request;

/**
 * Class ClientMock
 * @package Mageplaza\Barclaycard\Gateway\Http\Client
 */
class ClientMock implements ClientInterface
{
    /**
     * @var Request
     */
    private $helper;

    /**
     * @var Direct
     */
    private $config;

    /**
     * ClientMock constructor.
     *
     * @param Request $helper
     * @param Direct $config
     */
    public function __construct(Request $helper, Direct $config)
    {
        $this->helper = $helper;
        $this->config = $config;
    }

    /**
     * @param TransferInterface $transferObject
     *
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $body = $transferObject->getBody();

        $url = $body['url'];

        unset($body['url']);

        $this->helper->appendShaSign($body, $this->config->getShaIn());

        return $this->helper->sendRequest($url, [], $body);
    }
}
