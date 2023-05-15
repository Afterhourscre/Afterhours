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

use Mirasvit\SeoContent\Api\Data\RewriteInterface;

class Rewrite extends Content implements RewriteInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\Rewrite::class);
    }

    public function setUrl($value)
    {
        return $this->setData(self::URL, $value);
    }

    public function getUrl()
    {
        return $this->getData(self::URL);
    }

    public function setIsActive($value)
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    public function isActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function setSortOrder($value)
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    public function setStoreIds(array $value)
    {
        return $this->setData(self::STORE_IDS, implode(',', $value));
    }

    public function getStoreIds()
    {
        return explode(',', $this->getData(self::STORE_IDS));
    }
}
