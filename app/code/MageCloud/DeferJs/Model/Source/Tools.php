<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\DeferJs\Model\Source;

/**
 * Class Tools
 * @package MageCloud\Deferjs\Model\Source
 */
class Tools implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Tools values
     */
    const DEFER_JS_TOOLS_GTMETRIX_VALUE = 1;
    const DEFER_JS_TOOLS_GOOGLE_PAGE_SPEED_VALUE = 2;

    /**
     * Tools labels
     */
    const DEFER_JS_TOOLS_GTMETRIX_LABEL = 'GTMetrix';
    const DEFER_JS_TOOLS_GOOGLE_PAGE_SPEED_LABEL = 'Google Page Speed';

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
                    'value' => self::DEFER_JS_TOOLS_GTMETRIX_VALUE,
                    'label' => __(self::DEFER_JS_TOOLS_GTMETRIX_LABEL)
                ],
                [
                    'value' => self::DEFER_JS_TOOLS_GOOGLE_PAGE_SPEED_VALUE,
                    'label' => __(self::DEFER_JS_TOOLS_GOOGLE_PAGE_SPEED_LABEL)
                ],
            ];
        }

        array_unshift($this->_options, [
            'value' => '',
            'label' => (string)new \Magento\Framework\Phrase('-- Please Select --')
        ]);

        return $this->_options;
    }
}