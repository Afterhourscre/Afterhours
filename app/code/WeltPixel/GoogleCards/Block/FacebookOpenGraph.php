<?php
namespace WeltPixel\GoogleCards\Block;

class FacebookOpenGraph extends GoogleCards {

   /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getDescription($product)
    {
        $description = $this->_helper->getFacebookDescriptionType() ? $product->getData('description') : $product->getData('short_description');

        // Check if $description is not null and is a string before passing it to nl2br
        if (!is_null($description) && is_string($description)) {
            return nl2br($description);
        } else {
            return '';
        }
    }


    /**
     * @return string
     */
    public function getSiteName() {
        return $this->_helper->getFacebookSiteName();
    }

    /**
     * @return string
     */
    public function getAppId() {
        return $this->_helper->getFacebookAppId();
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        $priceOption = $this->_helper->getFacebookOpenGraphPrice();
        return $this->_calculatePrice($priceOption);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getRetailerId($product)
    {
        $idOption = $this->_helper->getFacebookRetailerId();
        $retailerItemId = '';
        switch ($idOption) {
            case 'sku' :
                $retailerItemId = $product->getData('sku');
                break;
            case 'id' :
            default:
                $retailerItemId = $product->getId();
                break;
        }
        return $retailerItemId;
    }
}
