<?php

namespace Extait\Cookie\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\App\RequestInterface;

/**
 * Abstract Resource Model for Cookie and Cookie Category.
 */
abstract class AbstractResourceModel extends AbstractDb
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * AbstractResourceModel constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\App\RequestInterface $request
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);

        $this->request = $request;
    }
}
