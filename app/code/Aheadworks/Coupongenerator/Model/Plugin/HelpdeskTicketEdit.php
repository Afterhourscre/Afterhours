<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Plugin;

use Aheadworks\Helpdesk\Block\Adminhtml\Ticket\Edit\Tabs\General as GeneralTab;
use Magento\Framework\Data\Form;
use Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\CollectionFactory as SalesruleCollectionFactory;
use Aheadworks\Helpdesk\Api\TicketRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class HelpdeskTicketEdit
 * @package Aheadworks\Coupongenerator\Model\Plugin
 * @codeCoverageIgnore
 */
class HelpdeskTicketEdit
{
    /**
     * @var SalesruleCollectionFactory
     */
    private $salesruleCollectionFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @param SalesruleCollectionFactory $salesruleCollectionFactory
     * @param ObjectManagerInterface $objectManager
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        SalesruleCollectionFactory $salesruleCollectionFactory,
        ObjectManagerInterface $objectManager,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->salesruleCollectionFactory = $salesruleCollectionFactory;
        $this->objectManager = $objectManager;
        $this->storeRepository = $storeRepository;
    }

    /**
     * Add coupon info to General tab
     *
     * @param GeneralTab $subject
     * @param \Closure $proceed
     * @param Form $form
     * @return GeneralTab
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSetForm(
        GeneralTab $subject,
        \Closure $proceed,
        $form
    ) {
        /** @var GeneralTab $result */
        $result = $proceed($form);

        $ticketElement = $form->getElement('id');
        if ($ticketElement) {
            $ticketId = $ticketElement->getValue();

            /** @var TicketRepositoryInterface $ticketRepository */
            $ticketRepository = $this->objectManager->create(TicketRepositoryInterface::class);

            /** @var \Aheadworks\Helpdesk\Api\Data\TicketInterface $ticket */
            $ticket = $ticketRepository->getById($ticketId);

            $storeId = $ticket->getStoreId();
            /** @var \Magento\Store\Api\Data\StoreInterface $store */
            $store = $this->storeRepository->getById($storeId);
            $websiteId = $store->getWebsiteId();

            $fieldset = $form->addFieldset('coupongenerator_fieldset', ['legend' => __('Generate Coupon')]);

            $activeRules = $this->salesruleCollectionFactory->create()
                ->setActiveRules()
                ->addWebsiteFilter($websiteId)
                ->toOptionArray()
            ;
            array_unshift(
                $activeRules,
                ['value' => 0, 'label' => __('Please select')]
            );

            $fieldset->addField(
                'rule_id',
                'select',
                [
                    'name'  => 'rule_id',
                    'label' => __("Rule"),
                    'title' => __("Rule"),
                    'values' => $activeRules,
                    'after_element_html' => "
                        <script type='text/javascript'>
                            require(['jquery'], function($){                                
                                $('#{$form->getHtmlIdPrefix()}'+'rule_id').change(function(){
                                    formSelector = '#aw-helpdesk-admin-reply-form';
                                    var ruleId = $(this).val();
                                    var replyForm = $(formSelector);
                                    var ruleElements = $(formSelector + ' input[name=awcg_rule_id]');
                                    if (ruleElements.length == 0) {
                                        var ruleElement = $('<input>')
                                            .attr('type', 'hidden')
                                            .attr('name', 'awcg_rule_id').val(ruleId);
                                        replyForm.append($(ruleElement));    
                                    } else {
                                        ruleElements.each(function() {
                                            this.value = ruleId;
                                        });
                                    }                                    
                                });                                
                            });
                        </script>"
                ]
            );
        }

        return $result;
    }
}
