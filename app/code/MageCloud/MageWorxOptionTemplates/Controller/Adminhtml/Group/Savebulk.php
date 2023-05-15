<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageCloud\MageWorxOptionTemplates\Controller\Adminhtml\Group;

use function GuzzleHttp\Psr7\str;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\OptionBase\Model\Product\Attributes as ProductAttributes;
use MageWorx\OptionTemplates\Controller\Adminhtml\Group as GroupController;
//use MageCloud\MageWorxOptionTemplates\Controller\Adminhtml\Group as GroupController;
use MageWorx\OptionTemplates\Model\Group\Source\AssignType;
use MageWorx\OptionTemplates\Model\OptionSaver;
use MageWorx\OptionTemplates\Model\GroupFactory;
use MageWorx\OptionTemplates\Model\Group\OptionFactory as GroupOptionFactory;
use MageWorx\OptionTemplates\Model\Group\Option as GroupOptionModel;
use MageWorx\OptionTemplates\Controller\Adminhtml\Group\Builder as GroupBuilder;
//use MageCloud\MageWorxOptionTemplates\Controller\Adminhtml\Group\BuilderBulk as GroupBuilder;
use MageWorx\OptionDependency\Model\Attribute\Dependency;
use MageWorx\OptionTemplates\Model\Group;

class Savebulk extends GroupController
{
    /**
     * @var OptionSaver
     */
    protected $optionSaver;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var JsHelper
     */
    protected $jsHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var GroupFactory
     */
    protected $groupFactory;

    /**
     * @var GroupOptionModel
     */
    protected $groupOptionModel;

    /**
     * @var GroupOptionFactory
     */
    protected $groupOptionFactory;

    /**
     * @var ProductAttributes
     */
    protected $productAttributes;

    /**
     * @var array
     */
    protected $formData = [];

    /**
     * @var Group
     */
    protected $entity;

    /**
     * @var Dependency
     */
    protected $dependency;

    /**
     * Savebulk constructor.
     * @param ProductCollectionFactory $productCollectionFactory
     * @param OptionSaver $optionSaver
     * @param JsHelper $jsHelper
     * @param GroupBuilder $groupBuilder
     * @param GroupFactory $groupFactory
     * @param Group\Option $groupOptionModel
     * @param ProductAttributes $productAttributes
     * @param Context $context
     * @param Registry $registry
     * @param Dependency $dependency
     * @param Group $entity
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        OptionSaver $optionSaver,
        JsHelper $jsHelper,
        GroupBuilder $groupBuilder,
        GroupFactory $groupFactory,
        GroupOptionModel $groupOptionModel,
        GroupOptionFactory $groupOptionFactory,
        ProductAttributes $productAttributes,
        Context $context,
        Registry $registry,
        Dependency $dependency,
        Group $entity
    ) {
        $this->optionSaver = $optionSaver;
        $this->jsHelper = $jsHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->registry = $registry;
        $this->groupFactory = $groupFactory;
        $this->groupOptionModel = $groupOptionModel;
        $this->groupOptionFactory       = $groupOptionFactory;
        $this->productAttributes = $productAttributes;
        $this->entity = $entity;
        $this->dependency = $dependency;
        parent::__construct($groupBuilder, $context);
    }

    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if($_FILES['csvfile'])
		{
			$dataArray = array();
			$currentTemplate 	= "";
			$currentOption 		= "";
			$currentValue 		= "";
			$row = 1;
			$tempMainArray = array();
			$tempValuesArray = array();
			$last_key = 0;
			$last_option_key = 0;
			$option_sort_order = 1;
			$value_sort_order = 1;
	
			if (($handle = fopen($_FILES['csvfile']['tmp_name'], "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					if($row > 1)
					{
						if($currentTemplate != $data[0] && $data[0]!="")
						{
							$tempMainArray = array();
							$currentTemplate = $data[0];
                            $tempMainArray['affect_product_custom_options'] = 1;
                            $tempMainArray['options']       	= array();
                            $tempMainArray['group_id'] 		= "";
                            $tempMainArray['title'] 		= $currentTemplate;
                            $tempMainArray['productids'] 	= "";
                            $tempMainArray['productsku'] 	= "";
                            $tempMainArray['assign_type'] 	= 1;
                            $tempMainArray['absolute_cost'] 	= 0;
                            $tempMainArray['absolute_weight'] 	= 0;
                            $tempMainArray['absolute_price'] 	= 0;
                            array_push($dataArray,$tempMainArray);
                            $last_key = key( array_slice( $dataArray, -1, 1, TRUE ) );
							$dataArray[$last_key]['options']    = array();
							$option_sort_order = +1;
						}
						
						if($currentOption != $data[1] && $data[1]!="")
						{
							$tempOptionsArray = array();
							$currentOption    =  $data[1];
                            $tempOptionsArray['sort_order'] = "$option_sort_order";
                            $tempOptionsArray['option_id'] 	= "";
                            $tempOptionsArray['is_require'] = 1;
                            $tempOptionsArray['one_time'] = 0;
                            $tempOptionsArray['description'] = "";
                            $tempOptionsArray['sku'] 		= "";
                            $tempOptionsArray['max_characters']	= "";
                            $tempOptionsArray['file_extension'] = "";
                            $tempOptionsArray['record_id'] = $option_sort_order-1;
                            $tempOptionsArray['title'] 		= $data[1];
                            $tempOptionsArray['type'] = $data[2];
                            $tempOptionsArray['is_swatch'] = isset($data[5]) ? $data[5] : 0;
                            $tempOptionsArray['price'] = "";
                            $tempOptionsArray['image_size_x'] = "";
                            $tempOptionsArray['image_size_y'] = "";
                            $tempOptionsArray['price_type'] = "fixed";
                            $tempOptionsArray['mageworx_option_gallery'] = 0;
                            $tempOptionsArray['mageworx_option_image_mode'] = 0;
							array_push($dataArray[$last_key]['options'],$tempOptionsArray);
							$last_option_key = key( array_slice($dataArray[$last_key]['options'], -1, 1, TRUE ) );
							$dataArray[$last_key]['options'][$last_option_key]['values'] = array();
							$option_sort_order++;
							$value_sort_order = 1;
						}
						
						if($currentValue != $data[3] && $data[3]!="")
						{
							$tempValuesArray 	= array();
//							$currentValue 		= $data[3];
                            $tempValuesArray['record_id'] 	= $value_sort_order-1;
                            $tempValuesArray['title'] 		= $data[3];
                            $tempValuesArray['price_type']	= 'fixed';
                            $tempValuesArray['sku'] 		= "";
                            $tempValuesArray['sort_order'] 	= $value_sort_order;
                            $tempValuesArray['mageworx_option_title'] = "";
                            $tempValuesArray['qty'] 	= "";
                            $tempValuesArray['manage_stock'] 	= 0;
                            $tempValuesArray['disabled'] 	= 0;
                            $tempValuesArray['images_data'] 	= "";
                            $tempValuesArray['dependency_type'] 	= isset($data[6]) ? $data[6] : "";
                            $tempValuesArray['sku_is_valid'] 	= "";
                            $tempValuesArray['price'] 		= $data[4];
                            $tempValuesArray['cost'] 		= "";
                            $tempValuesArray['weight'] 		= "";
                            $tempValuesArray['is_default'] 		= 0;
                            (isset($data[6]) && !empty($data[7])) ? ($tempValuesArray['dependency'] = $data[7]) : false;
                            array_push($dataArray[$last_key]['options'][$last_option_key]['values'],$tempValuesArray);
							$value_sort_order++;
						}
					}
					$row++;
				}
				fclose($handle);
			}
		
			foreach($dataArray as $key=>$data)
			{
                $this->getRequest()->setParam('mageworx_optiontemplates_group', $dataArray, $_REQUEST['mageworx_optiontemplates_group'] = $dataArray[0], $_POST['mageworx_optiontemplates_group'] = $dataArray[0]);
                $resultRedirect = $this->resultRedirectFactory->create();
                $this->registry->unregister('mageworx_optiontemplates_group');
                $this->registry->unregister('current_store');
                $this->registry->unregister('mageworx_optiontemplates_group_save');
                $this->registry->register('mageworx_optiontemplates_group_save', true);

                $originalOptions   = [];
                $isTemplateChanged = true;
                if ($this->isExistingTemplate()) {
                    /** @var \MageWorx\OptionTemplates\Model\Group $originalGroup */
                    $originalGroup = $this->getOriginalGroup();
                    $originalOptions   = $originalGroup->getOptions();
                    $isTemplateChanged = $this->isTemplateChanged($originalGroup);
                }


                if ($isTemplateChanged) {

                    $data = $this->filterData($data);

                    /** @var \MageWorx\OptionTemplates\Model\Group $group */

                    $group = $this->groupBuilder->build($this->getRequest());

                    /**
                     * Initialize product options
                     */
                    if (isset($data['options']) && !$group->getOptionsReadonly()) {
                        $options = $this->mergeProductOptions(
                            $data['options'],
                            $originalOptions,
                            $this->_request->getPost('options_use_default')
                        );
                        $group->setOptions($options);
                        $group->setData('options', $options);
                    }

                    $group->addData($data);
                    $group->setCanSaveCustomOptions(
                        (bool)$group->getData('affect_product_custom_options') && !$group->getOptionsReadonly()
                    );

                    $currentGroup = $group;

					/**
					 * Initialize product relation
					 */
                    $productIdsData = $this->getRequest()->getParam('group_products');
                    if (is_null($productIdsData)) {
                        $productIds = $currentGroup->getProducts();
                    } else {
                        $productIds = $this->getProductIds($productIdsData);
                    }
                    $productIds = $this->addProductsByIdSku($data, $productIds);
                    $currentGroup->setProductsIds($productIds);

                    $oldGroupCustomOptions = $currentGroup->getOptionArray();

					try {
                        $this->registry->unregister('mageworx_optiontemplates_group_id');
                        $this->registry->unregister('mageworx_optiontemplates_group_option_ids');
                        if ($isTemplateChanged) {
                            $currentGroup->save();
                            $this->messageManager->addSuccessMessage(__('The options template has been saved.'));
                            $this->optionSaver->saveProductOptions(
                                $currentGroup,
                                $oldGroupCustomOptions,
                                OptionSaver::SAVE_MODE_UPDATE
                            );
                        }

                        $this->_getSession()->setMageWorxOptionTemplatesGroupData(false);

                        $resultRedirect->setPath('mageworx_optiontemplates/*/');

					} catch (LocalizedException $e) {
						$this->messageManager->addErrorMessage($e->getMessage());
					} catch (\RuntimeException $e) {
						$this->messageManager->addErrorMessage($e->getMessage());
					} catch (\Exception $e) {
						$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the options template.' . $e->getMessage()));
					} finally {
						$this->registry->unregister('mageworx_optiontemplates_group_save');
                        $this->registry->unregister('mageworx_optiontemplates_group_id');
					}
				}
			}
        }
        $resultRedirect->setPath('mageworx_optiontemplates/*/');
        return $resultRedirect;
    }

    /**
     * Check if it is existing template or the new one
     *
     * @return bool
     */
    protected function isExistingTemplate()
    {
        return !empty($this->formData['group_id']) ? true : false;
    }

    /**
     * Get original group by group ID
     *
     * @return \MageWorx\OptionTemplates\Model\Group
     */
    protected function getOriginalGroup()
    {
        return $this->groupFactory->create()->load($this->formData['group_id']);
    }

    /**
     * Get original group by group ID
     *
     * @param \MageWorx\OptionTemplates\Model\Group $originalGroup
     * @return bool
     */
    protected function isGroupAttributesChanged($originalGroup)
    {
        $keys                  = [];
        $productAttributesKeys = [];
        $attributes            = $this->productAttributes->getData();
        /** @var $attribute \MageWorx\OptionBase\Api\ProductAttributeInterface */
        foreach ($attributes as $attribute) {
            $productAttributesKeys[] = $attribute->getKeys();
        }
        foreach ($productAttributesKeys as $productAttributesKeyItems) {
            foreach ($productAttributesKeyItems as $productAttributesKey) {
                $keys[] = $productAttributesKey;
            }
        }
        foreach ($keys as $key) {
            if (isset($this->formData[$key]) && $originalGroup->getData($key) != $this->formData[$key]) {
                return true;
            }
        }
        return false;
    }

    /**
     * Compare original template options with form template options
     *
     * @param \MageWorx\OptionTemplates\Model\Group\Option[] $originalGroupOptions
     * @return bool
     */
    protected function isOptionsChanged($originalGroupOptions)
    {
        $originalOptions = [];
        foreach ($originalGroupOptions as $option) {
            $option->setData('values', $option->getValues());
            $priceType = $option->getData('price_type');
            if (!isset($priceType)) {
                $option->setData('price_type', 'fixed');
            }
            $originalOptions[$option['option_id']] = $option->getData();
        }
        $formOptions = [];
        foreach ($this->formData['options'] as $option) {
            if ($this->isNewOption($option) || $this->isDeleted($option)) {
                return true;
            }
            $formOptions[$option['option_id']] = $option;
        }

        foreach ($originalOptions as $origOptionKey => $origOptionData) {
            if (empty($formOptions[$origOptionKey])) {
                return true;
            }
            foreach ($formOptions[$origOptionKey] as $formOptionKey => $formOptionData) {
                if (in_array($formOptionKey, ['option_id', 'record_id'])) {
                    continue;
                }
                if ($formOptionKey == 'values') {
                    if ($formOptionData && is_array($formOptionData) && isset($origOptionData['values'])) {
                        if ($this->isValuesChanged($origOptionData['values'], $formOptionData)) {
                            return true;
                        }
                    }
                } elseif (array_key_exists($formOptionKey, $origOptionData)
                    && $formOptionData != $origOptionData[$formOptionKey]
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check for a new option
     *
     * @param array $option
     * @return bool
     */
    protected function isNewOption($option)
    {
        if (!isset($option['option_id'])) {
            return true;
        }
        return false;
    }

    /**
     * Check if option or value is deleted on form
     *
     * @param array $data
     * @return bool
     */
    protected function isDeleted($data)
    {
        if (isset($data['is_delete']) && $data['is_delete'] = 1) {
            return true;
        }
        return false;
    }

    /**
     * Compare original template option values with form template option values
     *
     * @param array $originalOptionValues
     * @param array $formOptionValues
     * @return bool
     */
    protected function isValuesChanged($originalOptionValues, $formOptionValues)
    {
        $originalValues = [];
        foreach ($originalOptionValues as $value) {
            $priceType = $value->getData('price_type');
            if (!isset($priceType)) {
                $value->setData('price_type', 'fixed');
            }
            $originalValues[$value['option_type_id']] = $value->getData();
        }
        $formValues = [];
        foreach ($formOptionValues as $value) {
            if ($this->isNewValue($value) || $this->isDeleted($value)) {
                return true;
            }
            $formValues[$value['option_type_id']] = $value;
        }

        foreach ($originalValues as $origValueKey => $origValueData) {
            if (empty($formValues[$origValueKey])) {
                return true;
            }
            foreach ($formValues[$origValueKey] as $formValueKey => $formValueData) {
                if (in_array($formValueKey, ['option_type_id', 'option_id', 'record_id'])) {
                    continue;
                }
                if (array_key_exists($formValueKey, $origValueData)
                    && $formValueData != $origValueData[$formValueKey]
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check for a new option's value
     *
     * @param array $value
     * @return bool
     */
    protected function isNewValue($value)
    {
        if (!isset($value['option_type_id'])) {
            return true;
        }
        return false;
    }

    /**
     * Check if template is changed
     *
     * @param \MageWorx\OptionTemplates\Model\Group\ $originalGroup
     * @return bool
     */
    protected function isTemplateChanged($originalGroup)
    {
        if (!$originalGroup) {
            return true;
        }
        if (count($originalGroup->getOptions()) != count($this->formData['options'])) {
            return true;
        }
        if ($this->isGroupAttributesChanged($originalGroup)) {
            return true;
        }
        return $this->isOptionsChanged($originalGroup->getOptions());
    }

    /**
     * Merge original options, if template is not new, form options and default options for group
     *
     * @param array $productOptions form options
     * @param array $originalOptions original template options
     * @param array $overwriteOptions default value options
     * @return array
     */
    protected function mergeProductOptions($productOptions, $originalOptions, $overwriteOptions)
    {
        if (!is_array($productOptions)) {
            $productOptions = [];
        }
        if (is_array($overwriteOptions)) {
            $options = array_replace_recursive($productOptions, $overwriteOptions);
            array_walk_recursive(
                $options,
                function (&$item) {
                    if ($item === "") {
                        $item = null;
                    }
                }
            );
        } else {
            $options = $productOptions;
        }

        $currentOptionIds      = [];
        $currentOptionValueIds = [];

        $recordIdCounter = 0;
        foreach ($options as $optionKey => $option) {
            if (!isset($option['record_id'])) {
                $options[$optionKey]['record_id'] = 'r' . $recordIdCounter;
            }
            $recordIdCounter++;
            if (!empty($option['option_id'])) {
                $currentOptionIds[$option['option_id']] = $option['option_id'];
            }
            if (!empty($option['values']) && is_array($option['values'])) {
                foreach ($option['values'] as $valueKey => $value) {
                    if (!isset($value['record_id'])) {
                        $options[$optionKey]['values'][$valueKey]['record_id'] = 'r' . $recordIdCounter;
                    }
                    $recordIdCounter++;
                    if (!empty($value['option_type_id'])) {
                        $currentOptionValueIds[$value['option_type_id']] = $value['option_type_id'];
                    }
                }
            }
        }

        foreach ($originalOptions as $originalOption) {
            foreach ($options as $optionKey => $option) {
                if (empty($option['option_id']) || empty($originalOption['option_id'])) {
                    continue;
                }
                if ($option['option_id'] != $originalOption['option_id']) {
                    if (!isset($currentOptionIds[$originalOption['option_id']])) {
                        $originalOption->setData('is_delete', 1);
                        $originalOption->setData('record_id', $originalOption['option_id']);
                        $options[]                                      = $originalOption->getData();
                        $currentOptionIds[$originalOption['option_id']] = true;
                        break;
                    }
                } else {
                    if (empty($originalOption->getValues()) || empty($option['values'])) {
                        continue;
                    }
                    foreach ($originalOption->getValues() as $originalOptionValue) {
                        foreach ($option['values'] as $optionValue) {
                            if (empty($optionValue['option_type_id']) || empty($originalOptionValue['option_type_id'])) {
                                continue;
                            }
                            $originalOptionValueId = $originalOptionValue['option_type_id'];
                            if ($optionValue['option_type_id'] != $originalOptionValueId) {
                                if (!isset($currentOptionValueIds[$originalOptionValueId])) {
                                    $originalOptionValue['is_delete']              = 1;
                                    $originalOptionValue['record_id']              = $originalOptionValueId;
                                    $options[$optionKey]['values'][]               = $originalOptionValue->getData();
                                    $currentOptionValueIds[$originalOptionValueId] = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        $processedOptions = [];
        foreach ($options as $option) {
            $processedOptions[] = $this->groupOptionFactory->create()->setData($option);
        }

        return $processedOptions;
    }

    /**
     *
     * @param string $data
     * @return array
     */
    protected function getProductIds($data)
    {
        if (!empty($data)) {
            $productIds = json_decode($data, true);
        } else {
            $productIds = [];
        }

        return $productIds;
    }

    /**
     *
     * @param array $data
     * @param array $assignedProductIds
     * @return array
     */
    protected function addProductsByIdSku($data, $assignedProductIds)
    {
        $productIds = [];

        if ($data['assign_type'] == AssignType::ASSIGN_BY_GRID) {
            return $assignedProductIds;
        } elseif ($data['assign_type'] == AssignType::ASSIGN_BY_IDS) {
            $productIds = $this->convertMultiStringToArray($data['productids'], 'intval');
        } elseif ($data['assign_type'] == AssignType::ASSIGN_BY_SKUS) {
            $productSkus = $this->convertMultiStringToArray($data['productskus']);

            if ($productSkus) {
                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
                $collection = $this->productCollectionFactory->create();
                $collection->addFieldToFilter('sku', ['in' => $productSkus]);
                $productIds = array_map('intval', $collection->getAllIds());
            }
        }

        return array_merge($assignedProductIds, $productIds);
    }

    /**
     *
     * @param string $string
     * @param string $finalFunction
     * @return array
     */
    protected function convertMultiStringToArray($string, $finalFunction = null)
    {
        if (!trim($string)) {
            return [];
        }

        $rawLines = array_filter(preg_split('/\r?\n/', $string));
        $rawLines = array_map('trim', $rawLines);
        $lines    = array_filter($rawLines);

        if (!$lines) {
            return [];
        }

        $array = [];
        foreach ($lines as $line) {
            $rawIds  = explode(',', $line);
            $rawIds  = array_map('trim', $rawIds);
            $lineIds = array_filter($rawIds);
            if (!$finalFunction) {
                $lineIds = array_map($finalFunction, $lineIds);
            }
            $array = array_merge($array, $lineIds);
        }

        return $array;
    }

    protected function filterData($data)
    {
        if (isset($data['group_id']) && !$data['group_id']) {
            unset($data['group_id']);
        }

        if (isset($data['options'])) {
            $updatedOptions = [];
            foreach ($data['options'] as $key => $option) {
                if (!isset($option['option_id'])) {
                    continue;
                }

                $optionId = $option['option_id'];
                if (!$optionId && !empty($option['record_id'])) {
                    $optionId = $option['record_id'] . '_';
                }
                $updatedOptions[$optionId] = $option;
                if (empty($option['values'])) {
                    continue;
                }

                $values = $option['values'];
                foreach ($option['values'] as $valueKey => $value) {
                    if (!isset($value['option_type_id'])) {
                        continue;
                    }
                    unset($updatedOptions[$optionId]['values'][$valueKey]);
                }
                foreach ($values as $valueKey => $value) {
                    if (!isset($value['option_type_id'])) {
                        continue;
                    }
                    $valueId                                       = $value['option_type_id'];
                    $updatedOptions[$optionId]['values'][$valueId] = $value;
                }
            }

            $data['options'] = $updatedOptions;
        }

        return $data;
    }
}
