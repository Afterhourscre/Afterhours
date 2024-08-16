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

namespace Mirasvit\Review\Controller\Adminhtml\Review;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Review\Model\Uploader;
use Magento\Framework\App\RequestInterface;

class Upload extends \Magento\Framework\App\Action\Action
{
    protected $uploader;

    public function __construct(
        Context  $context,
        Uploader $uploader
    ) {
        $this->uploader = $uploader;
        parent::__construct($context);
    }

    public function execute()
    {

        $inputName = 'photo';
        $photos    = $this->getRequest()->getFiles($inputName);

        $result = $this->uploader->uploadFiles($photos, $inputName);

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
