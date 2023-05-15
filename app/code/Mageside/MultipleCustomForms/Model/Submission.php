<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model;

class Submission extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var null|\Mageside\MultipleCustomForms\Model\CustomForm
     */
    protected $_customForm = null;

    protected function _construct()
    {
        $this->_init('Mageside\MultipleCustomForms\Model\ResourceModel\Submission');
    }

    /**
     * @param $form
     * @return $this
     */
    public function setFormModel($form)
    {
        $this->_customForm = $form;
        return $this;
    }

    /**
     * @return null|\Mageside\MultipleCustomForms\Model\CustomForm
     */
    public function getFormModel()
    {
        return $this->_customForm;
    }
}
