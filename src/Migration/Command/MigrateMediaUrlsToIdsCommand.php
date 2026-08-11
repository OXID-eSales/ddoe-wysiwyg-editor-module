<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Command;

use OxidEsales\WysiwygModule\Migration\Factory\MigrationReporterFactoryInterface;
use OxidEsales\WysiwygModule\Migration\Service\FieldMigrationServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateMediaUrlsToIdsCommand extends Command
{
    private const COMMAND_NAME = 'ddoewysiwyg:migrate:urls-to-ids';
    private const COMMAND_DESCRIPTION = 'Migrates media urls of one field in one table to the new (id relation) format';

    public function __construct(
        private readonly FieldMigrationServiceInterface $fieldMigrationService,
        private readonly MigrationReporterFactoryInterface $reporterFactory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION)
            ->addArgument(
                'table',
                InputArgument::REQUIRED,
                'Table to migrate field from'
            )
            ->addArgument(
                'field',
                InputArgument::REQUIRED,
                'Field to migrate'
            )
            ->addArgument(
                'tableKey',
                InputArgument::OPTIONAL,
                'Unique table key field to use during migration',
                'OXID'
            )
            ->addOption(
                'report-file',
                'r',
                InputOption::VALUE_REQUIRED,
                'Write the report as csv to this file instead of printing it to the screen.'
                . ' Relative paths are written to the shop log directory'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $report = $this->fieldMigrationService->migrate(
            (string)$input->getArgument('table'),
            (string)$input->getArgument('field'),
            (string)$input->getArgument('tableKey'),
        );

        $reportFilePath = $input->getOption('report-file');
        $reporter = $this->reporterFactory->create(is_string($reportFilePath) ? $reportFilePath : null);
        $reporter->report($report, $output);

        return $report->getFailures() ? Command::FAILURE : Command::SUCCESS;
    }
}
