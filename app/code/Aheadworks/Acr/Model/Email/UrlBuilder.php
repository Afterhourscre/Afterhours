<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Email;

use Magento\Framework\UrlInterface as FrameworkUrlInterface;
use Magento\Framework\App\Area;

/**
 * Class UrlBuilder
 * @package Aheadworks\Acr\Model
 */
class UrlBuilder
{
    /**
     * @var array
     */
    private $urlBuilders = [];

    /**
     * @param array $urlBuilders
     */
    public function __construct(
        array $urlBuilders = []
    ) {
        $this->urlBuilders = $urlBuilders;
    }

    /**
     * Get url
     *
     * @param string $routePath
     * @param string $scope
     * @param array $params
     * @param string $areaCode
     * @return string
     */
    public function getUrl($routePath, $scope, $params, $areaCode = Area::AREA_ADMINHTML)
    {
        $href = '';
        $urlBuilder = $this->getUrlBuilder($areaCode);

        if ($urlBuilder) {
            $urlBuilder->setScope($scope);
            $href = $urlBuilder->getUrl(
                $routePath,
                $params
            );
        }
        return $href;
    }

    /**
     * Get url builder object
     *
     * @param string $areaCode
     * @return FrameworkUrlInterface|null
     */
    private function getUrlBuilder($areaCode)
    {
        $urlBuilder = null;
        if (isset($this->urlBuilders[$areaCode])
            && $this->urlBuilders[$areaCode] instanceof FrameworkUrlInterface
        ) {
            $urlBuilder = $this->urlBuilders[$areaCode];
        }

        return $urlBuilder;
    }
}
