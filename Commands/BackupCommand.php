<?php

namespace Codememory\Components\GlobalConfig\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\FileSystem\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BackupCommand
 * @package Codememory\Components\GlobalConfig\Commands
 *
 * @author  Codememory
 */
class BackupCommand extends Command
{

    /**
     * @var string|null
     */
    protected ?string $command = 'g-config:backup';

    /**
     * @var string|null
     */
    protected ?string $description = 'The command backs up the global configuration';

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $filesystem = new File();

        if (GlobalConfig::exist()) {
            $dataGlobalConfig = json_encode(GlobalConfig::getAll());
            $path = sprintf('%s%s', GlobalConfig::getPath(), GlobalConfig::getBackupFilename());

            $filesystem->writer->open($path, createFile: true)->put($dataGlobalConfig);

            $this->io->success('Successful copy of global configuration');

            return Command::SUCCESS;
        }

        $this->io->error('Unable to back up due to missing global configuration');

        return Command::FAILURE;

    }

}