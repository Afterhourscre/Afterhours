<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\IndexManagement\Controller\Adminhtml\Indexer;

/**
 * Class Indexer
 * @package MageCloud\IndexManagement\Controller\Adminhtml\Indexer
 */
abstract class Indexer extends \Magento\Backend\App\Action
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Indexer::reindex');
    }
}