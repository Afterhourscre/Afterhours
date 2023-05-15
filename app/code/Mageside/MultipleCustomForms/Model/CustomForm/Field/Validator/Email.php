<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\CustomForm\Field\Validator;

class Email extends \Zend_Validate_EmailAddress implements \Magento\Framework\Validator\ValidatorInterface
{
    /**
     * EmailAddress constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        parent::__construct($options);

        $this->getHostnameValidator()->setValidateTld(false);
    }

    /**
     * @param $shouldValidate
     */
    public function setValidateTld($shouldValidate)
    {
        $this->getHostnameValidator()->setValidateTld($shouldValidate);
    }
}
