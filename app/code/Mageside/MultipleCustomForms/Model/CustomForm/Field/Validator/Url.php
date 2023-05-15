<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\CustomForm\Field\Validator;

class Url extends \Zend_Validate_Abstract implements \Magento\Framework\Validator\ValidatorInterface
{
    const INVALID = 'urlInvalid';

    /**
     * @var array
     */
    protected $_messageTemplates = [
        self::INVALID => "Url is not valid.",
    ];

    /**
     * @var array
     */
    protected $_allowedSchemes = [];

    /**
     * Url constructor.
     * @param array $allowedSchemes
     */
    public function __construct($allowedSchemes = [])
    {
        if (is_array($allowedSchemes)) {
            if (array_key_exists('allowedSchemes', $allowedSchemes)) {
                $this->_allowedSchemes = $allowedSchemes['allowedSchemes'];
            } else {
                $this->_allowedSchemes = [];
            }
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $isValid = true;

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $isValid = false;
        }

        if ($isValid && !empty($this->_allowedSchemes)) {
            $url = parse_url($value);
            if (empty($url['scheme']) || !in_array($url['scheme'], $this->_allowedSchemes)) {
                $isValid = false;
            }
        }

        if (!$isValid) {
            $this->_error(self::INVALID);
        }

        return $isValid;
    }
}
