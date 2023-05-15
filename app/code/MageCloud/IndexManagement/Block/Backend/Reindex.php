<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\IndexManagement\Block\Backend;

/**
 * Class Reindex
 * @package MageCloud\IndexManagement\Block\Backend
 */
class Reindex extends \Magento\Indexer\Block\Backend\Container
{
    /**
     * Initialize object state with incoming parameters
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_addButton('Reindex All');
        parent::_construct();
    }

    /**
     * @param $label
     */
    protected function _addButton($label){

        $message = __('Are you sure that you want to reindex all indexers. This operation can take a some time?');

        $this->buttonList->add(
            strtolower(str_replace(' ', '_', $label)),
            [
                'label' => __($label),
                'onclick' => 'confirmSetLocation(\'' . $message . '\', \'' . $this->getActionUrl() . '\')',
                'class' => 'reindex primary'
            ]
        );
    }

    /**
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('indexmanagement/indexer/reindex');
    }
}