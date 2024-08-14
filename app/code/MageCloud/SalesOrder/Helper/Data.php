<?php
namespace MageCloud\SalesOrder\Helper;

/**
 * Class Data
 * @package MageCloud\SalesOrder\Helper
 */
class Data extends \WeltPixel\ThankYouPage\Helper\Data
{

    /**
     * @return string
     */
    public function getOrderDetailTemplate()
    {
        if ($this->isOrderDetailsEnabled()) {
            return 'MageCloud_SalesOrder::success.phtml';
        }

        return '';
    }
}
