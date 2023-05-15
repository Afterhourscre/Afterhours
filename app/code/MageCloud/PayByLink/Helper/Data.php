<?php

namespace MageCloud\PayByLink\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @param array $txnArray
     *
     * @return string
     */
    public function toString($txnArray)
    {
        $result = '';
        foreach ($txnArray as $field => $value) {
            if ($value === null) {
                continue;
            }

            if ($result) {
                $result .= '&';
            }

            $result .= $field . '=' . $value;
        }

        return $result;
    }
}