<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageCloud\MGSPortfolioSort\Block\Adminhtml\Edit;

/**
 * Sitemap edit form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Items extends \Magento\Backend\Block\Widget\Form\Generic
{
	/**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
	
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
	
	/**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
		\Magento\Framework\Convert\DataObject $objectConverter,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
		$this->_objectConverter = $objectConverter;
		$this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('portfolio__form');
        $this->setTitle(__('Portfolio Information'));
    }
	
	public function getCategoryModel(){
		return $this->_objectManager->create('MGS\Portfolio\Model\Category');
	}

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('portfolio_portfolio');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data']]
        );

        $fieldset = $form->addFieldset('add_portfolio_form', ['legend' => __('Portfolio Item')]);

        if ($model->getId()) {
            $fieldset->addField('portfolio_id', 'hidden', ['name' => 'portfolio_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'label' => __('Name'),
                'name' => 'name',
                'required' => true,
                'value' => $model->getName()
            ]
        );

        $fieldset->addField(
            'identifier',
            'text',
            [
                'label' => __('Identifier'),
                'name' => 'identifier',
				'class' => 'validate-identifier',
                'value' => $model->getIdentifier()
            ]
        );
		
		$categories = $this->getCategoryModel()
			->getCollection();
		$catOptions = [];
		if(count($categories)>0){
			foreach($categories as $category){
				$catOptions[] = [
					'label' => $category->getCategoryName(),
					'value' => $category->getId()
				];
			}
		}
		
		$fieldset->addField(
            'category_id',
            'multiselect',
            [
                'label' => __('Category'),
                'title' => __('Category'),
                'name' => 'categories[]',
                'required' => false,
                'values' => $catOptions
            ]
        );
		
		$fieldset->addField(
            'thumbnail_image',
            'file',
            [
                'label' => __('Thumbnail Image'),
                'name' => 'thumbnail_image',
                'required' => false,
                'value' => $model->getThumbnailImage()
            ]
        );
		
		$fieldset->addField(
            'base_image',
            'file',
            [
                'label' => __('Base Image'),
                'name' => 'base_image',
                'required' => false,
                'value' => $model->getBaseImage()
            ]
        );

		$fieldset->addField(
            'gallery_image1',
            'file',
            [
                'label' => __('Gallery Image 1'),
                'name' => 'gallery_image1',
                'required' => false,
                'value' => $model->getGalleryImage1()
            ]
        );

		$fieldset->addField(
            'gallery_image2',
            'file',
            [
                'label' => __('Gallery Image 2'),
                'name' => 'gallery_image2',
                'required' => false,
                'value' => $model->getGalleryImage2()
            ]
        );

		$fieldset->addField(
            'gallery_image3',
            'file',
            [
                'label' => __('Gallery Image 3'),
                'name' => 'gallery_image3',
                'required' => false,
                'value' => $model->getGalleryImage3()
            ]
        );

		$fieldset->addField(
            'gallery_image4',
            'file',
            [
                'label' => __('Gallery Image 4'),
                'name' => 'gallery_image4',
                'required' => false,
                'value' => $model->getGalleryImage4()
            ]
        );

		$fieldset->addField(
            'gallery_image5',
            'file',
            [
                'label' => __('Gallery Image 5'),
                'name' => 'gallery_image5',
                'required' => false,
                'value' => $model->getGalleryImage5()
            ]
        );
		
		$fieldset->addField(
            'client',
            'text',
            [
                'label' => __('Client'),
                'name' => 'client',
                'required' => false,
                'value' => $model->getClient()
            ]
        );
		
		$fieldset->addField(
            'services',
            'text',
            [
                'label' => __('Project'),
                'name' => 'services',
                'required' => false,
                'value' => $model->getServices()
            ]
        );
		
		$fieldset->addField(
            'project_url',
            'text',
            [
                'label' => __('Project Url'),
                'name' => 'project_url',
                'required' => false,
                'value' => $model->getProjectUrl()
            ]
        );

        $fieldset->addField(
            'seo_title',
            'text',
            [
                'label' => __('SEO Title'),
                'name' => 'seo_title',
                'required' => false,
                'value' => $model->getSeoTitle()
            ]
        );

        $fieldset->addField(
            'seo_keywords',
            'text',
            [
                'label' => __('SEO Keywords'),
                'name' => 'seo_keywords',
                'required' => false,
                'value' => $model->getMetaKeywords()
            ]
        );

        $fieldset->addField(
            'seo_descriptions',
            'textarea',
            [
                'label' => __('SEO Descriptions'),
                'name' => 'seo_descriptions',
                'required' => false,
                'value' => $model->getMetaDescription()
            ]
        );
		
		$dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
		
		$fieldset->addField(
            'portfolio_date',
            'date',
            [
                'name' => 'portfolio_date',
                'label' => __('Date'),
                'title' => __('Date'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            ]
        );
		
		$fieldset->addField(
            'skills',
            'text',
            [
                'label' => __('Skills'),
                'name' => 'skills',
                'required' => false,
                'value' => $model->getSkills()
            ]
        );
		
		$fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'name' => 'status',
                'required' => false,
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );
		
		$fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Content'),
                'title' => __('Content'),
                'style' => 'height:25em',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'label' => __('Position'),
                'name' => 'sort_order',
                'required' => false,
                'value' => $model->getSortOrder()
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
