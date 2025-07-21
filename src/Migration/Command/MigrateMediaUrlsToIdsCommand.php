<?php

/**
 * Copyright © . All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Command;

use OxidEsales\WysiwygModule\Migration\Repository\FieldMigrationRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateMediaUrlsToIdsCommand extends Command
{
    private const COMMAND_NAME = 'ddoewysiwyg:migrate:urls-to-ids';
    private const COMMAND_DESCRIPTION = 'Migrates media urls of one field in one table to the new (id relation) format';

    public function __construct(
        private readonly FieldMigrationRepositoryInterface $fieldMigrationRepository,
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
        $tableName = $input->getArgument('table');
        $fieldName = $input->getArgument('field');
        $tableKey = $input->getArgument('tableKey');

        $this->fieldMigrationRepository->migrateTableField(
            $tableName,
            $fieldName,
            $tableKey
        );

        $output->writeln("Done for $tableName::$fieldName using key $tableKey");

        return COMMAND::SUCCESS;
    }
}