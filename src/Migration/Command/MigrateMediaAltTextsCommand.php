<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Command;

use OxidEsales\WysiwygModule\Migration\Repository\AltTextMigrationRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateMediaAltTextsCommand extends Command
{
    private const COMMAND_NAME = 'ddoewysiwyg:migrate:alt-texts';
    private const COMMAND_DESCRIPTION = 'Adds oeMediaAlt placeholders to media image alt attributes in one table field';

    public function __construct(
        private readonly AltTextMigrationRepositoryInterface $altTextMigrationRepository,
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

        $customAltTextTags = $this->altTextMigrationRepository->migrateTableField(
            $tableName,
            $fieldName,
            $tableKey
        );

        if (count($customAltTextTags) > 0) {
            $output->writeln(
                '<comment>Warning: The following media images have custom alt text that was NOT modified:</comment>'
            );
            $output->writeln('');

            foreach ($customAltTextTags as $entry) {
                $output->writeln(sprintf(
                    '  [%s=%s] media-id="%s" alt="%s"',
                    $tableKey,
                    $entry['key'],
                    $entry['mediaId'],
                    $entry['altText']
                ));
            }

            $output->writeln('');
            $output->writeln(
                '<comment>Please review these entries and update alt texts manually if needed.</comment>'
            );
        }

        $output->writeln("<info>Done for $tableName::$fieldName using key $tableKey</info>");

        return Command::SUCCESS;
    }
}
