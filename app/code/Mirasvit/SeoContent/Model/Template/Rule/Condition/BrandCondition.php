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
 * @version   2.9.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\SeoContent\Model\Template\Rule\Condition;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

/**
 * @method getAttribute()
 * @method getJsFormObject()
 */
class BrandCondition extends AbstractCondition
{
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Context $context,
        array $data = []
    ) {
        $this->objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function loadAttributeOptions(): BrandCondition
    {
        $attributes = [
            'brand_id' => (string)__('Brand')
        ];

        $this->setData('attribute_option', $attributes);

        return $this;
    }

    public function getExplicitApply(): bool
    {
        return $this->getAttribute() === 'brand_id';
    }

    public function getValueElementType(): string
    {
        if ($this->getAttribute() === 'brand_id') {
            return 'multiselect';
        }

        return parent::getValueElementType();
    }

    public function getValueSelectOptions(): array
    {
        $options = [];

        if (
            !$this->hasData('value_select_options')
            && $this->getAttribute() === 'brand_id'
            && class_exists('\Mirasvit\Brand\Repository\BrandPageRepository')
        ) {
            $brandRepository = $this->objectManager->get('\Mirasvit\Brand\Repository\BrandPageRepository');

            foreach ($brandRepository->getCollection() as $brand) {
                $options[] = [
                    'label' => __($brand->getDataFromGroupedField(\Mirasvit\Brand\Api\Data\BrandPageStoreInterface::BRAND_TITLE, 'content', 0)),
                    'value' => $brand->getId(),
                ];
            }

            $this->setData('value_select_options', $options);
        }

        return $options;
    }

    public function validate(AbstractModel $model): bool
    {
        if ($model instanceof \Mirasvit\Brand\Api\Data\BrandPageInterface && $this->getAttribute() === 'brand_id') {
            return $this->validateAttribute($model->getId());
        }

        return false;
    }
}
