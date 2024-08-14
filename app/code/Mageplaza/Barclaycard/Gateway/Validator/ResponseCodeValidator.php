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

namespace Mageplaza\Barclaycard\Gateway\Validator;

use InvalidArgumentException;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Mageplaza\Barclaycard\Helper\Response;

/**
 * Class ResponseCodeValidator
 * @package Mageplaza\Barclaycard\Gateway\Validator
 */
class ResponseCodeValidator extends AbstractValidator
{
    /**
     * @var Response
     */
    private $helper;

    /**
     * ResponseCodeValidator constructor.
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param Response $helper
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        Response $helper
    ) {
        $this->helper = $helper;

        parent::__construct($resultFactory);
    }

    /**
     * Performs validation of result code
     *
     * @param array $validationSubject
     *
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        if (empty($validationSubject['response'])) {
            throw new InvalidArgumentException(__('Response does not exist'));
        }

        if ($error = $this->helper->hasError($validationSubject['response'])) {
            return $this->createResult(false, [__($error)]);
        }

        return $this->createResult(true);
    }
}
