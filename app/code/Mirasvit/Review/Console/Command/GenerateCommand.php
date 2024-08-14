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


use Mirasvit\Review\Service\GenerateReviewService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    const NICKNAME    = 'nickname';
    const RATING      = 'rating';
    const PRODUCT     = 'product';
    const STORE       = 'store';
    const ADDITIONAL  = 'additional';
    const QTY         = 'qty';
    const AUTOAPPROVE = 'autoapprove';


    protected $generateReviewService;

    public function __construct(
        GenerateReviewService $generateReviewService
    ) {
        $this->generateReviewService = $generateReviewService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('mirasvit:review:generate');
        $this->setDescription('Generate reviews');
        $this->addOption(
            self::NICKNAME,
            'nick',
            InputOption::VALUE_REQUIRED,
            'Nickname'
        );
        $this->addOption(
            self::RATING,
            'r',
            InputOption::VALUE_REQUIRED,
            'Rating , must be from 1 to 5'
        );
        $this->addOption(
            self::PRODUCT,
            'p',
            InputOption::VALUE_OPTIONAL,
            'Product id'
        );
        $this->addOption(
            self::STORE,
            null,
            InputOption::VALUE_OPTIONAL,
            'Store id'
        );
        $this->addOption(
            self::ADDITIONAL,
            null,
            InputOption::VALUE_OPTIONAL,
            'Additional'
        );
        $this->addOption(
            self::QTY,
            null,
            InputOption::VALUE_OPTIONAL,
            'Review Qty'
        );
        $this->addOption(
            self::AUTOAPPROVE,
            null,
            InputOption::VALUE_OPTIONAL,
            'Autoapprove review - 0 or 1'
        );

        parent::configure();
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {

        $result = false;

        if (!$input->getOption(self::NICKNAME)) {
            $output->writeln("<error>Nickname param is required </error>");

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        if (!$input->getOption(self::RATING) || !($input->getOption(self::RATING) >= 1 && $input->getOption(self::RATING) <= 5)) {
            $output->writeln("<error>Rating param is required and must be from 1 to 5</error>");

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        if ($input->getOption(self::PRODUCT) && !is_numeric($input->getOption(self::PRODUCT))) {
            $output->writeln("<error>Product param must be numeric </error>");

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        if ($input->getOption(self::STORE) && !is_numeric($input->getOption(self::STORE))) {
            $output->writeln("<error>Store param must be numeric </error>");

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        if ($input->getOption(self::QTY) && !is_numeric($input->getOption(self::QTY))) {
            $output->writeln("<error>Qty param  must be numeric </error>");

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        if ($input->getOption(self::AUTOAPPROVE)
            && ($input->getOption(self::AUTOAPPROVE) === 1
                || $input->getOption(self::AUTOAPPROVE) === 0)) {
            $output->writeln("<error>Autoapprove param  must be 0 or 1 </error>");

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        $result = $this->generateReviewService->execute(
            (string)$input->getOption(self::NICKNAME),
            (int)$input->getOption(self::RATING),
            $input->getOption(self::PRODUCT) ? (int)$input->getOption(self::PRODUCT) : null,
            $input->getOption(self::STORE) ? (int)$input->getOption(self::STORE) : null,
            $input->getOption(self::ADDITIONAL),
            $input->getOption(self::QTY) ? (int)$input->getOption(self::QTY) : 1,
            $input->getOption(self::AUTOAPPROVE) ? true : false
        );


        if (is_array($result)) {
            $productIds = implode(',', $result);
            $output->writeln("<error>An error occurred during generating with some products ($productIds) , check the logs</error>");

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        } else {
            $output->writeln('<info>Success generation!</info>');

            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        }

    }
}
