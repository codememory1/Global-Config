<?php

namespace Codememory\Components\GlobalConfig\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\FileSystem\File;
use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\Support\Str;
use LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MergeConfigCommand
 * @package Codememory\Components\GlobalConfig\Commands
 *
 * @author  Codememory
 */
class MergeConfigCommand extends Command
{

    private const PATH_WITH_CONFIGS = [
        'vendor/codememory/config/',
        'vendor/codememory/environment/',
        'vendor/codememory/caching/',
        'vendor/codememory/routing/',
        'vendor/codememory/big/',
        'vendor/codememory/service-provider/'
    ];

    /**
     * @var string|null
     */
    protected ?string $command = 'g-config:merge';

    /**
     * @var string|null
     */
    protected ?string $description = 'Combine one configuration with the main one';

    /**
     * @inheritDoc
     */
    protected function wrapArgsAndOptions(): Command
    {

        $this
            ->option('configPath', null, InputOption::VALUE_REQUIRED, 'The path in which there is a .config folder that will be combined with the main configuration in the project')
            ->option('all', null, InputOption::VALUE_NONE, 'Merge all component configurations into one master');

        return $this;

    }

    /**
     * @inheritDoc
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $filesystem = new File();

        if(null === $input->getOption('configPath') && !$input->getOption('all')) {
            throw new LogicException("At least one of these options must be specified \n--configPath = <path>\n--all");
        }

        if (null !== $input->getOption('configPath')) {
            $this->merge($filesystem, $input->getOption('configPath'));
        }

        if ($input->getOption('all')) {
            foreach (self::PATH_WITH_CONFIGS as $pathWithConfigs) {
                if ($filesystem->exist($pathWithConfigs)) {
                    $this->merge($filesystem, $pathWithConfigs);
                }
            }
        }

        $this->io->success('A successful merge has been made to the global config');

        return Command::SUCCESS;

    }

    /**
     * @param FileInterface $filesystem
     * @param string        $configPath
     */
    private function merge(FileInterface $filesystem, string $configPath): void
    {

        $mainPath = GlobalConfig::PATH . GlobalConfig::FILENAME;
        $pathWithoutExpansion = Str::cut($mainPath, mb_stripos($mainPath, '.yaml'));
        $pathAdditionalConfig = sprintf(
            '%s/%s%s',
            trim($configPath, '/'),
            GlobalConfig::PATH,
            GlobalConfig::FILENAME
        );
        $pathAdditionalConfigWithoutExpansion = Str::cut($pathAdditionalConfig, mb_stripos($pathAdditionalConfig, '.yaml'));

        $additionalConfig = [];

        if ($filesystem->exist($pathAdditionalConfig)) {
            $additionalConfig = GlobalConfig::getYamlMarkup()->open($pathAdditionalConfigWithoutExpansion)->get();
        }

        $mainConfig = GlobalConfig::getAll();
        $mainConfig = array_merge_recursive($mainConfig, $additionalConfig);

        GlobalConfig::getYamlMarkup()->open($pathWithoutExpansion)->write($mainConfig);

    }

}