<?php
namespace WeltPixel\GoogleCards\Block;

class TwitterCards extends GoogleCards
{

   /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getDescription($product)
    {
        $description = $this->_helper->getTwitterCardDescriptionType() ? $product->getData('description') : $product->getData('short_description');

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
    public function getTwitterCreator()
    {
        return $this->_helper->getTwitterCreator();
    }

    /**
     * @return string
     */
    public function getTwitterSite()
    {
        return $this->_helper->getTwitterSite();
    }

    /**
     * @return string
     */
    public function getShippingCountry()
    {
        return $this->_helper->getTwitterShippingCountry();
    }

    /**
     * @return string
     */
    public function getTwitterCardType()
    {
        return $this->_helper->getTwitterCardType();
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        $priceOption = $this->_helper->getTwitterCardsPrice();
        return $this->_calculatePrice($priceOption);
    }
}
