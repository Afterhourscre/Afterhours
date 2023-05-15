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



namespace Mirasvit\Seo\Controller\Adminhtml\CanonicalRewrite;

class MassEnable extends MassActions
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $ids = $this->getActionIds();

        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = $this->canonicalRewriteRepository->get($id);
                    $model = $model->setIsActive(true);
                    $this->canonicalRewriteRepository->save($model);
                }
                $this->messageManager->addSuccess(
                    __(
                        'Total of %1 record(s) were successfully enabled',
                        count($ids)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}