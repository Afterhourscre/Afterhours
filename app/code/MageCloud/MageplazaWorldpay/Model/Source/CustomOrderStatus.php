<?php


namespace MageCloud\MageplazaWorldpay\Model\Source;


use Mageplaza\Worldpay\Model\Source\OrderStatus;

class CustomOrderStatus extends OrderStatus
{
    const PROCESSING = 'processing';
    const FRAUD      = 'fraud';
    const ARTWORK      = 'pending_artwork';

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::PROCESSING => __('Processing'),
            self::FRAUD      => __('Suspected Fraud'),
            self::ARTWORK      => __('Pending Artwork'),
        ];
    }
}