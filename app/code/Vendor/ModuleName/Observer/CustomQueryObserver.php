<?php
namespace Vendor\ModuleName\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Vendor\ModuleName\Model\CustomQuery;

class CustomQueryObserver implements ObserverInterface
{
    protected $customQuery;

    public function __construct(CustomQuery $customQuery)
    {
        $this->customQuery = $customQuery;
    }

    public function execute(Observer $observer)
    {
        $this->customQuery->execute();
    }
}
