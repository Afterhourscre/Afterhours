<?php
namespace MageCloud\MageWorxOptionTemplates\Block\Adminhtml\Optiontemplates\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('checkmodule_optiontemplates_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Optiontemplates Information'));
    }
}
