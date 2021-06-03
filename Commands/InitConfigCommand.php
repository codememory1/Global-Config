<?php

namespace Codememory\Components\GlobalConfig\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InitConfigCommand
 * @package Codememory\Components\GloablConfig\Commands
 *
 * @author  Codememory
 */
class InitConfigCommand extends Command
{

    /**
     * @var string|null
     */
    protected ?string $command = 'g-config:init';

    /**
     * @var string|null
     */
    protected ?string $description = 'Initialize global configuration';

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        if (!GlobalConfig::exist()) {
            if (!GlobalConfig::getFilesystem()->exist(GlobalConfig::PATH)) {
                GlobalConfig::getFilesystem()->mkdir(GlobalConfig::PATH);
            }

            $fullPath = GlobalConfig::PATH . GlobalConfig::FILENAME;

            GlobalConfig::getFilesystem()->writer->open($fullPath, 'r', true);

            $this->io->success(sprintf('Generated global config file %s', $fullPath));

        } else {
            $this->io->warning('The global config file already exists, it cannot be reinitialized');
        }

        return Command::SUCCESS;

    }

}