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

use Mirasvit\Review\Service\VerifyingService;
use Mirasvit\Review\Service\VerifyingServiceFactory;
use Mirasvit\Review\Service\LocationSevice;
use Mirasvit\Review\Service\LocationSeviceFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReindexCommand extends Command
{
    const VERIFY = 'verify';

    const LOCATION = 'location';

    const FORCE = 'force';

    protected $verifyingServiceFactory;

    protected $locationSeviceFactory;

    public function __construct(
        LocationSeviceFactory $locationSeviceFactory,
        VerifyingServiceFactory $verifyingServiceFactory
    ) {
        $this->locationSeviceFactory   = $locationSeviceFactory;
        $this->verifyingServiceFactory = $verifyingServiceFactory;

        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('mirasvit:review:reindex');
        $this->setDescription('Reindex reviews for verifying.');
        $this->addOption(
            self::VERIFY,
            null,
            InputOption::VALUE_NONE,
            'Verifying review'
        );
        $this->addOption(
            self::LOCATION,
            null,
            InputOption::VALUE_NONE,
            'Update Location of review'
        );
        $this->addOption(
            self::FORCE,
            null,
            InputOption::VALUE_NONE,
            'Force reindexing verified buyer'
        );

        parent::configure();
    }


    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $options = [];

        foreach ($input->getOptions() as $key => $option) {
            if ($option) {
                $options[$key] = $option;
            }
        }

        if (
            empty($options)
            || (count($options) == 1 && isset($options[self::FORCE]))
        ) {
            $this->verifyingServiceFactory->create()->updateIsVerified(isset($options[self::FORCE]));
            $this->locationSeviceFactory->create()->execute();
        }

        if (isset($options[self::VERIFY])) {
            $this->verifyingServiceFactory->create()->updateIsVerified(isset($options[self::FORCE]));
        }

        if (isset($options[self::LOCATION])) {
            $this->locationSeviceFactory->create()->execute();
        }

        $output->writeln('<info>Success reindex !</info>');

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }

}
