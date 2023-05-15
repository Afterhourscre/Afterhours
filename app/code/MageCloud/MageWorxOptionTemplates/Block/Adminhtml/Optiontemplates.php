<?php
namespace MageCloud\MageWorxOptionTemplates\Block\Adminhtml;

class Optiontemplates extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_optiontemplates';/*block grid.php directory*/
        $this->_blockGroup = 'Custom_Optiontemplates';
        $this->_headerText = __('Optiontemplates');
        $this->_addButtonLabel = __('Add New Item'); 
        parent::_construct();
        $this->buttonList->remove('add');		
    }
}
