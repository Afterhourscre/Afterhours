<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @package MageCloud_Base
 */
namespace MageCloud\ExcludeJsFromMinification\Plugin;

use Magento\Framework\View\Asset\Minification;

/**
 * Class ExcludeAuthorizeAcceptJsFromMinification
 * @package MageCloud\Base\Plugin
 */
class ExcludeJsFromMinification
{
    /**
     * @var string
     */
    protected $acceptJs = 'https://cdn.worldpay.com/v1/worldpay.js';

    /**
     * @param Minification $subject
     * @param array $result
     * @param $contentType
     * @return array
     */
    public function afterGetExcludes(
        Minification $subject,
        array $result,
        $contentType
    ) {
        if (($contentType == 'js')
            && (!in_array('Accept', $result)
            || !in_array($this->acceptJs, $result))
        ) {
            $result[] = $this->acceptJs;
        }

        return $result;
    }
}