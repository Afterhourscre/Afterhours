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


namespace Mirasvit\Review\Console\Command;


use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Console\Cli;
use Mirasvit\Review\Repository\ReviewRepository;
use Mirasvit\Review\Service\Email\NotificationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyPendingCommand extends Command
{
    private $notificationService;

    private $reviewRepository;

    private $appState;

    public function __construct(
        AppState               $appState,
        NotificationService $notificationService,
        ReviewRepository $reviewRepository
    ) {
        $this->notificationService = $notificationService;
        $this->reviewRepository    = $reviewRepository;
        $this->appState            = $appState;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('mirasvit:review:notify');
        $this->setDescription('Reindex reviews for verifying.');
        $this->addOption(
            'id',
            null,
            InputOption::VALUE_REQUIRED,
            'Review Id'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        try {
            $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Exception $e) {
        }

        $reviewId = $input->getOption('id');
        $review   = $this->reviewRepository->get((int)$reviewId);

        if (!$review) {
            $output->writeln('<error>Review with id ' . $reviewId . ' does not exist</error>');

            return Cli::RETURN_FAILURE;
        }

        $output->writeln('<comment>Sending notification for review with ID ' . $reviewId . '</comment>');

        $this->notificationService->notifyPendingReview((int)$reviewId, true);

        $output->writeln('<info>Notification sent</info>');

        return Cli::RETURN_SUCCESS;
    }
}
