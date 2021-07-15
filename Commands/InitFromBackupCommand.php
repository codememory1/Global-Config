<?php

namespace Codememory\Components\GlobalConfig\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\FileSystem\File;
use Codememory\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InitFromBackupCommand
 * @package Codememory\Components\GlobalConfig\Commands
 *
 * @author  Codememory
 */
class InitFromBackupCommand extends Command
{

    /**
     * @var string|null
     */
    protected ?string $command = 'g-config:init-from-backup';

    /**
     * @var string|null
     */
    protected ?string $description = 'The command initializes the configuration from the backup';

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $filesystem = new File();

        $backupPath = sprintf('%s%s', GlobalConfig::PATH, GlobalConfig::BACKUP_FILENAME);
        $globalConfigPath = sprintf('%s%s', GlobalConfig::PATH, GlobalConfig::FILENAME);

        if ($filesystem->exist($backupPath)) {
            $backupData = json_decode(file_get_contents($filesystem->getRealPath($backupPath)), true);

            GlobalConfig::getMarkupType()->open(Str::trimAfterSymbol($globalConfigPath, GlobalConfig::EXTENSION))->write($backupData);

            $this->io->success('Global configuration successfully initialized from backup');

            return Command::SUCCESS;
        }

        $this->io->error('It is not possible to initialize the global configuration from a backup due to the lack of a configuration backup');

        return Command::FAILURE;

    }

}