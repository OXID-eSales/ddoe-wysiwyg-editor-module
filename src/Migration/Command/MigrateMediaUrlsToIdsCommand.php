<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Command;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\Service\MigrationReportCsvWriterInterface;
use OxidEsales\WysiwygModule\Migration\Service\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\Repository\FieldMigrationRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateMediaUrlsToIdsCommand extends Command
{
    private const COMMAND_NAME = 'ddoewysiwyg:migrate:urls-to-ids';
    private const COMMAND_DESCRIPTION = 'Migrates media urls of one field in one table to the new (id relation) format';

    public function __construct(
        private readonly FieldMigrationRepositoryInterface $fieldMigrationRepository,
        private readonly MigrationReportInterface $report,
        private readonly MigrationReportCsvWriterInterface $reportCsvWriter,
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tableName = $input->getArgument('table');
        $fieldName = $input->getArgument('field');
        $tableKey = $input->getArgument('tableKey');

        $this->report->reset();

        $this->fieldMigrationRepository->migrateTableField($tableName, $fieldName, $tableKey);

        $this->renderSummary($io, $tableName, $fieldName, $tableKey);
        $this->writeReportFile($io, $tableName, $fieldName);

        return $this->report->getFailures() ? Command::FAILURE : Command::SUCCESS;
    }

    private function renderSummary(SymfonyStyle $io, string $tableName, string $fieldName, string $tableKey): void
    {
        $converted = $this->report->countByOutcome(MigrationOutcome::ConvertedExisting);
        $imported = $this->report->countByOutcome(MigrationOutcome::Imported);
        $failed = $this->report->countByOutcome(MigrationOutcome::Failed);

        $io->section("$tableName::$fieldName (key $tableKey)");
        $io->writeln(sprintf('Rows scanned:           %d', $this->report->getRowsScanned()));
        $io->writeln(sprintf('Media references found: %d', count($this->report->getEntries())));
        $io->writeln(sprintf(
            'Converted:              %d (existing: %d, imported: %d)',
            $converted + $imported,
            $converted,
            $imported
        ));
        $io->writeln(sprintf('Failed:                 %d', $failed));
    }

    private function writeReportFile(SymfonyStyle $io, string $tableName, string $fieldName): void
    {
        if (!$this->report->shouldWriteFile()) {
            return;
        }

        $path = $this->reportCsvWriter->getDefaultPath($tableName, $fieldName);
        $writtenPath = $this->reportCsvWriter->write($this->report, $path);
        $io->writeln("For details, see: $writtenPath");
    }
}
