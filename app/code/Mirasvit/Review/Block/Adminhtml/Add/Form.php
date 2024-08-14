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
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Block\Adminhtml\Add;


use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Mirasvit\Review\Block\Adminhtml\Element\ImageUploader;
use Mirasvit\Review\Model\ConfigProvider;

class Form extends \Magento\Review\Block\Adminhtml\Add\Form
{
    private $imageUploader;

    private $configProvider;

    private $secureRenderer;

    public function __construct(
        ImageUploader                           $imageUploader,
        ConfigProvider                          $configProvider,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry             $registry,
        \Magento\Framework\Data\FormFactory     $formFactory,
        \Magento\Store\Model\System\Store       $systemStore,
        \Magento\Review\Helper\Data             $reviewData,
        array                                   $data = [],
        ?SecureHtmlRenderer                     $htmlRenderer = null
    ) {
        $this->imageUploader  = $imageUploader;
        $this->configProvider = $configProvider;
        $this->secureRenderer = $htmlRenderer ? : ObjectManager::getInstance()->get(SecureHtmlRenderer::class);

        parent::__construct($context, $registry, $formFactory, $systemStore, $reviewData, $data, $htmlRenderer);
    }

    /**
     * Prepare add review form
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset   = $form->addFieldset('add_review_form', ['legend' => __('Review Details')]);
        $beforeHtml = $this->secureRenderer->renderStyleAsTag('display: none;', '#edit_form');
        $fieldset->setBeforeElementHtml($beforeHtml);

        $fieldset->addField('product_name', 'note', ['label' => __('Product'), 'text' => 'product_name']);

        $fieldset->addField(
            'detailed-rating',
            'note',
            [
                'label'    => __('Product Rating'),
                'required' => true,
                'text'     => '<div id="rating_detail">' . $this->getLayout()->createBlock(
                        \Magento\Review\Block\Adminhtml\Rating\Detailed::class
                    )->toHtml() . '</div>',
            ]
        );

        $fieldset->addField(
            'status_id',
            'select',
            [
                'label'    => __('Status'),
                'required' => true,
                'name'     => 'status_id',
                'values'   => $this->_reviewData->getReviewStatusesOptionArray(),
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field    = $fieldset->addField(
                'select_stores',
                'multiselect',
                [
                    'label'    => __('Visibility'),
                    'required' => true,
                    'name'     => 'select_stores[]',
                    'values'   => $this->_systemStore->getStoreValuesForForm(),
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
            );
            $field->setRenderer($renderer);
        }

        $fieldset->addField(
            'nickname',
            'text',
            [
                'name'      => 'nickname',
                'title'     => __('Nickname'),
                'label'     => __('Nickname'),
                'maxlength' => '50',
                'required'  => true,
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'name'      => 'title',
                'title'     => __('Summary of Review'),
                'label'     => __('Summary of Review'),
                'maxlength' => '255',
                'required'  => true,
            ]
        );

        $fieldset->addField(
            'detail',
            'textarea',
            [
                'name'     => 'detail',
                'title'    => __('Review'),
                'label'    => __('Review'),
                'required' => true,
            ]
        );

        if ($this->configProvider->displayProsAndCons()) {
            $fieldset->addField(
                'pros',
                'textarea',
                [
                    'name'     => 'pros',
                    'title'    => __('Pros'),
                    'label'    => __('Pros'),
                    'required' => false,
                ]
            );

            $fieldset->addField(
                'cons',
                'textarea',
                [
                    'name'     => 'cons',
                    'title'    => __('Cons'),
                    'label'    => __('Cons'),
                    'required' => false,
                ]
            );
        }

        $fieldset->addElement($this->imageUploader);

        $fieldset->addField('product_id', 'hidden', ['name' => 'product_id']);

        if ($this->configProvider->isAutowriteEnabled()) {
            $autoWriteFieldset = $form->addFieldset('autowrite_review_form', ['legend' => __('Autowrite')]);
            $scriptTag         = $this->secureRenderer->renderTag('script', ['type' => 'text/javascript'], $this->getAutowriteScript(), false);
            $autoWriteFieldset->setBeforeElementHtml($scriptTag);

            $autoWriteFieldset->addField(
                'instruct',
                'textarea',
                [
                    'name'     => 'instruct',
                    'title'    => __('Review instructions'),
                    'label'    => __('Review instructions'),
                    'required' => false,
                ]
            );

            $autoWriteFieldset->addField(
                'generate',
                'button', [
                'name'  => 'generate',
                'title' => __('Generate Review'),
                'label' => ' ',
                'value' => __('Generate Review'),
            ])->addClass('action-basic');

            $autoWriteFieldset->addField(
                'notice',
                'note', [
                'name'  => 'notice',
                'title' => ' ',
                'label' => ' ',
                'text'  => '<div class="messages"><div class="message message-error error" style="display: none"></div></div>',
            ]);
        }

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('mst_review/review/save'));

        $this->setForm($form);
    }

    private function getAutowriteScript()
    {
        $requestUrl = $this->_urlBuilder->getUrl('mst_review/review/generate');

        $script = <<<script
            document.addEventListener("DOMContentLoaded", function() {
                document.addEventListener('click', function(e) {
                    if (e.target.id != 'generate') {
                        return;
                    }

                    jQuery('.loading-mask').show();

                    const messageElem = jQuery('.field-notice .message.message-error.error');
                    messageElem.hide();

                    let postData = new FormData();
                    const stores = jQuery('#select_stores').val();

                    if (stores && stores.length == 1) {
                        postData.append('store_id', stores[0]);
                    }

                    postData.append('product_id', jQuery('#product_id').val());
                    postData.append('instruction', jQuery('#instruct').val());
                    postData.append('form_key', FORM_KEY);

                    fetch('$requestUrl', {
                        method: "POST",
                        body: postData
                    }).then(function(response) {
                        return response.json();
                    }).then(function(data) {
                        if (data.success) {
                            for (key in data.data.review) {
                                let value = data.data.review[key];
                                if (Array.isArray(value)) {
                                    value = value.join('\\r\\n');
                                }

                                if (jQuery('#' + key).length) {
                                    jQuery('#' + key).val(value);
                                }
                            }
                        } else {
                            messageElem.text(data.message);
                            messageElem.show();
                            console.error(data.message);
                        }
                    }).catch(function (response) {
                        messageElem.text(response);
                        messageElem.show();
                        console.error(response);
                    }).finally(function () {
                        jQuery('.loading-mask').hide();
                    });
                });
            });
        script;

        return $script;
    }
}
