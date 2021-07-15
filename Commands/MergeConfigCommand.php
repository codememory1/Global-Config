<?php

namespace Codememory\Components\GlobalConfig\Commands;

use Codememory\Components\Console\Command;
use Codememory\Components\Console\Exceptions\NotCommandException;
use Codememory\Components\Console\ResourcesCommand;
use Codememory\Components\Console\Running;
use Codememory\Components\Finder\Find;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\Components\Markup\Markup;
use Codememory\Components\Markup\Types\JsonType;
use Codememory\Components\Markup\Types\YamlType;
use Codememory\FileSystem\File;
use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\Support\Arr;
use Codememory\Support\Str;
use Exception;
use LogicException;
use ReflectionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MergeConfigCommand
 *
 * @package Codememory\Components\GlobalConfig\Commands
 *
 * @author  Codememory
 */
class MergeConfigCommand extends Command
{

    private const DEFAULT_TYPE_BACKUP = 'before';
    private const PATH_WITH_PACKAGES = 'vendor/codememory/';

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
            ->option('all', null, InputOption::VALUE_NONE, 'Merge all component configurations into one master')
            ->option('backup', null, InputOption::VALUE_REQUIRED, 'Back up configuration after or before merge', self::DEFAULT_TYPE_BACKUP);

        return $this;

    }

    /**
     * @inheritDoc
     * @throws NotCommandException
     * @throws ReflectionException
     */
    protected function handler(InputInterface $input, OutputInterface $output): int
    {

        $filesystem = new File();

        if (null === $input->getOption('configPath') && !$input->getOption('all')) {
            throw new LogicException("At least one of these options must be specified \n--configPath = <path>\n--all");
        }

        if ('before' === $input->getOption('backup')) {
            $this->executeBackup();
        }

        if (null !== $input->getOption('configPath')) {
            $this->merge($input->getOption('configPath'));
        }

        if ($input->getOption('all')) {
            foreach ($this->getAllPathPackages($filesystem) as $pathWithConfigs) {
                $pathToPackage = sprintf('%s%s/', self::PATH_WITH_PACKAGES, $pathWithConfigs);

                if ($filesystem->exist($pathToPackage . GlobalConfig::PATH)) {
                    $this->merge($pathToPackage);
                }
            }
        }

        $this->io->success('A successful merge has been made to the global config');

        if ('after' === $input->getOption('backup')) {
            $this->executeBackup();
        }

        return Command::SUCCESS;

    }

    /**
     * @param string $configPath
     */
    private function merge(string $configPath): void
    {

        $finder = new Find();
        $mainPath = GlobalConfig::PATH . GlobalConfig::FILENAME;
        $pathWithoutExpansion = Str::cut($mainPath, mb_stripos($mainPath, GlobalConfig::EXTENSION));
        $configs = $finder->setPathForFind(sprintf(
            '%s/%s',
            trim($configPath, '/'),
            GlobalConfig::PATH
        ))->file()->byRegex('.codememory.[a-z]+$')->get();
        $additionalConfig = [];

        foreach ($configs as $config) {
            $extension = Str::trimToSymbol($config, '.', false);
            $dataConfig = $this->getDataConfigByExtension($extension, $config);

            if (false !== $dataConfig) {
                $additionalConfig += $dataConfig;
            }
        }

        $mainConfig = GlobalConfig::getAll();
        $mainConfigToDot = Arr::dot($mainConfig);
        $additionalConfigToDot = Arr::dot($additionalConfig);

        foreach ($additionalConfigToDot as $key => $value) {
            if (!array_key_exists($key, $mainConfigToDot)) {
                $key = Str::trimAfterSymbol($key, '.', true);

                $mainConfig[$key] = $additionalConfig[$key];
            }
        }

        GlobalConfig::getMarkupType()->open($pathWithoutExpansion)->write($mainConfig);

    }

    /**
     * @param string $extension
     * @param string $config
     *
     * @return bool|array
     */
    private function getDataConfigByExtension(string $extension, string $config): bool|array
    {

        $config = Str::trimAfterSymbol($config, '.', false);

        switch ($extension) {
            case 'yaml':
                $markup = new Markup(new YamlType());
                $data = $markup->open($config)->get();
                break;
            case 'json':
                $markup = new Markup(new JsonType());
                $data = $markup->open($config)->get();
                break;
            default:
                $data = false;
                break;
        }

        return $data;

    }

    /**
     * @throws NotCommandException
     * @throws ReflectionException
     * @throws Exception
     */
    private function executeBackup(): void
    {

        $run = new Running();
        $runBackup = $run
            ->addCommands([new BackupCommand()])
            ->addCommand(function (ResourcesCommand $resourcesCommand) {
                $resourcesCommand->commandToExecute('g-config:backup');
            })
            ->run();
        $response = str_replace(['[OK]', "\n"], '', trim($runBackup->getResponse()));

        $this->io->block($response, 'OK', 'fg=black;bg=green', ' ', true);

    }

    /**
     * @param FileInterface $filesystem
     *
     * @return array
     */
    private function getAllPathPackages(FileInterface $filesystem): array
    {

        return $filesystem->scanning(self::PATH_WITH_PACKAGES);

    }


}