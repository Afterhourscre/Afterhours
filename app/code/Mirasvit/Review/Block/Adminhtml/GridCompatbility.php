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


namespace Mirasvit\Review\Block\Adminhtml;

/** mp comment start **/
$path = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\Filesystem\DirectoryList')->getRoot();
$reviewPath1 = $path . '/vendor/magento/module-review/Block/Adminhtml/Edit/Tab/Reviews.php';
$reviewPath2 = $path . '/app/code/Magento/Review/Block/Adminhtml/Edit/Tab/Reviews.php';

$customerPath1 = $path . '/vendor/magento/module-customer/Block/Adminhtml/Edit/Tab/Reviews.php';
$customerPath2 = $path . '/app/code/Magento/Customer/Block/Adminhtml/Edit/Tab/Reviews.php';

if (file_exists($reviewPath1) || file_exists($reviewPath2)) {
    /** mp comment end **/
    class GridCompatbility extends \Magento\Review\Block\Adminhtml\Edit\Tab\Reviews
    {

    }
    /** mp comment start **/
} elseif (file_exists($customerPath1) || file_exists($customerPath2)) {
    class GridCompatbility extends \Magento\Customer\Block\Adminhtml\Edit\Tab\Reviews
    {

    }
}
/** mp comment end **/
