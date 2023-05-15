<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\DeferJs\Model\Source;

/**
 * Class GooglePageSpeedStrategy
 * @package MageCloud\Deferjs\Model\Source
 */
class GooglePageSpeedStrategy implements \Magento\Framework\Option\ArrayInterface
{
    const DEFER_JS_GOOGLE_PAGE_SPEED_STRATEGY_DESKTOP = 'Desktop';
    const DEFER_JS_GOOGLE_PAGE_SPEED_STRATEGY_MOBILE = 'Mobile';

    /**
     * @var array
     */
    protected $_options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = [
                [
                    'value' => strtolower(self::DEFER_JS_GOOGLE_PAGE_SPEED_STRATEGY_DESKTOP),
                    'label' => __(self::DEFER_JS_GOOGLE_PAGE_SPEED_STRATEGY_DESKTOP)
                ],
                [
                    'value' => strtolower(self::DEFER_JS_GOOGLE_PAGE_SPEED_STRATEGY_MOBILE),
                    'label' => __(self::DEFER_JS_GOOGLE_PAGE_SPEED_STRATEGY_MOBILE)
                ],
            ];
        }

        return $this->_options;
    }
}