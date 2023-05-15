<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageCloud\MageWorxOptionTemplates\Controller\Adminhtml\Group;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\OptionTemplates\Model\GroupFactory;
use Magento\Framework\Registry;
//use MageCloud\MageWorxOptionTemplates\Controller\Adminhtml\Group\BuilderBulk as Builder;
use MageWorx\OptionTemplates\Controller\Adminhtml\Group\Builder as Builder;
use MageWorx\OptionTemplates\Controller\Adminhtml\Group;
//use MageCloud\MageWorxOptionTemplates\Controller\Adminhtml\Group;

class Editbulk extends Group
{
    /**
     * Backend session
     *
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Construct
     *
     * @param Builder $groupBuilder
     * @param PageFactory $resultPageFactory
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Builder $groupBuilder,
        PageFactory $resultPageFactory,
        Context $context,
        Registry $registry
    ) {
        $this->backendSession = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;

        parent::__construct($groupBuilder, $context);
    }

    /**
     * Is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_OptionTemplates::groups');
    }

    public function execute()
    {
        $this->registry->unregister('mageworx_optiontemplates_group');

        /** @var \MageWorx\OptionTemplates\Model\Group $group */
        $group = $this->groupBuilder->build($this->getRequest());
        $this->registry->register('current_product', $group);

        // 1. Get ID and create model
        $groupId = $this->getRequest()->getParam('id');

        $model = $this->_objectManager->create('MageWorx\OptionTemplates\Model\Group');

        $registryObject = $this->_objectManager->get('Magento\Framework\Registry');

        // 2. Initial checking
        if ($groupId) {
            $model->load($groupId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This row no longer exists.'));
                $this->_redirect(
                    '*/*/',
                    [
                        'group_id' => $group->getId(),
                        '_current' => true,
                    ]
                );
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $registryObject->register('mageworx_optiontemplates_optiontemplates', $model);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
