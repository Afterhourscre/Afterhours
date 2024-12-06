<?php

namespace BronzeByte\ImgFix\Plugin;

use Magento\MediaStorage\Service\ImageResize;

class ImageResizePlugin
{
    public function aroundResizeFromImageName(
        ImageResize $subject,
        \Closure $proceed,
        string $originalImageName
    ) {
        try {
            // Proceed with the original method
            return $proceed($originalImageName);
        } catch (\Exception $e) {
            // Catch any exception and throw a custom error
            throw new \Error("The Resource Is missing");
        }
    }
}
