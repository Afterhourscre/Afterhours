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


use Mirasvit\Review\Service\ReviewAggregatorService;
use Mirasvit\Review\Service\ReviewAggregatorServiceFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AggregateCommand extends Command
{
    const PRODUCT = 'product';

    protected $aggregatorServiceFactory;

    public function __construct(
        ReviewAggregatorServiceFactory $aggregatorServiceFactory
    ) {
        $this->aggregatorServiceFactory = $aggregatorServiceFactory;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('mirasvit:review:aggregate');
        $this->setDescription('Reindex reviews for verifying.');
        $this->addOption(
            self::PRODUCT,
            'p',
            InputOption::VALUE_OPTIONAL,
            'Product id'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $options = [];

        $result = false;

        foreach ($input->getOptions() as $key => $option) {
            if ($option) {
                $options[$key] = $option;
            }
        }

        $aggregatorService = $this->aggregatorServiceFactory->create();

        if (isset($options[self::PRODUCT])) {
            $result = $aggregatorService->execute((int)$options[self::PRODUCT]);
        } else {
            $result = $aggregatorService->execute();
        }

        if (is_array($result)) {
            $productIds = implode(',', $result);
            $output->writeln("<error>An error occurred during aggregation with some products ($productIds) , check the logs</error>");

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        } elseif (!$result) {
            $output->writeln('<error>Aggregation is disabled</error>');

            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        } else {
            $output->writeln('<info>Success aggregation!</info>');

            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        }

    }
}
