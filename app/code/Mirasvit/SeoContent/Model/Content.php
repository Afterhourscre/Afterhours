<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.169
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoContent\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\SeoContent\Api\Data\ContentInterface;

class Content extends AbstractModel implements ContentInterface
{
    public function setTitle($value)
    {
        return $this->setData(self::TITLE, $value);
    }

    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    public function setMetaTitle($value)
    {
        return $this->setData(self::META_TITLE, $value);
    }

    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    public function setMetaKeywords($value)
    {
        return $this->setData(self::META_KEYWORDS, $value);
    }

    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    public function setMetaDescription($value)
    {
        return $this->setData(self::META_DESCRIPTION, $value);
    }

    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescriptionPosition($value)
    {
        return $this->setData(self::DESCRIPTION_POSITION, $value);
    }

    public function getDescriptionPosition()
    {
        return $this->getData(self::DESCRIPTION_POSITION);
    }

    public function setDescriptionTemplate($value)
    {
        return $this->setData(self::DESCRIPTION_TEMPLATE, $value);
    }

    public function getDescriptionTemplate()
    {
        return $this->getData(self::DESCRIPTION_TEMPLATE);
    }

    public function setShortDescription($value)
    {
        return $this->setData(self::SHORT_DESCRIPTION, $value);
    }

    public function getShortDescription()
    {
        return $this->getData(self::SHORT_DESCRIPTION);
    }

    public function setFullDescription($value)
    {
        return $this->setData(self::FULL_DESCRIPTION, $value);
    }

    public function getFullDescription()
    {
        return $this->getData(self::FULL_DESCRIPTION);
    }

    public function setCategoryDescription($value)
    {
        return $this->setData(self::CATEGORY_DESCRIPTION, $value);
    }

    public function getCategoryDescription()
    {
        return $this->getData(self::CATEGORY_DESCRIPTION);
    }

    public function setCategoryImage($value)
    {
        return $this->setData(self::CATEGORY_IMAGE, $value);
    }

    public function getCategoryImage()
    {
        return $this->getData(self::CATEGORY_IMAGE);
    }

    public function setData($key, $value = null)
    {
        if ($key == self::DESCRIPTION) {
//            \Magento\Framework\Debug::backtrace();
        }

        return parent::setData($key, $value);
    }
}
