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

namespace Mirasvit\Review\Cron;

use Mirasvit\Review\Service\VerifyingService;
use Mirasvit\Review\Service\VerifyingServiceFactory;
use Mirasvit\Review\Service\LocationSevice;
use Mirasvit\Review\Service\LocationSeviceFactory;

class ReindexReviewCron
{
    protected $verifyingServiceFactory;

    protected $locationServiceFactory;

    public function __construct(
        VerifyingServiceFactory $verifyingServiceFactory,
        LocationSeviceFactory $locationSeviceFactory
    ) {
        $this->locationServiceFactory  = $locationSeviceFactory;
        $this->verifyingServiceFactory = $verifyingServiceFactory;
    }

    public function execute(): void
    {
        $this->verifyingServiceFactory->create()->updateIsVerified();
        $this->locationServiceFactory->create()->execute();
    }

}
