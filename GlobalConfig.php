<?php

namespace Codememory\Components\GlobalConfig;

use Codememory\Components\GlobalConfig\Interfaces\GlobalConfigInterface;
use Codememory\Components\Markup\Interfaces\MarkupInterface;
use Codememory\Components\Markup\Markup;
use Codememory\Components\Markup\Types\YamlType;
use Codememory\FileSystem\File;
use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\Support\Arr;
use Codememory\Support\Str;

/**
 * Class GlobalConfig
 * @package Codememory\Components\GloablConfig
 *
 * @author  Codememory
 */
class GlobalConfig implements GlobalConfigInterface
{

    public const PATH = '.config/';
    public const FILENAME = '.codememory.yaml';
    public const BACKUP_FILENAME = 'codememory.backup';

    /**
     * @var FileInterface|null
     */
    private static ?FileInterface $filesystem = null;

    /**
     * @var MarkupInterface|null
     */
    private static ?MarkupInterface $markup = null;

    /**
     * @inheritDoc
     */
    public static function get(string $keys): mixed
    {

        return Arr::set(self::getAll())::get($keys);

    }

    /**
     * @inheritDoc
     */
    public static function getAll(): array
    {

        $gluedPathFile = self::PATH . self::FILENAME;
        $path = Str::cut($gluedPathFile, mb_stripos($gluedPathFile, '.yaml'));
        $data = [];

        if (self::exist()) {
            $data = self::getYamlMarkup()->open($path)->get();
        }

        return $data;

    }

    /**
     * @inheritDoc
     */
    public static function exist(): bool
    {

        return self::getFilesystem()->exist(self::PATH . self::FILENAME);

    }

    /**
     * @return FileInterface
     */
    public static function getFilesystem(): FileInterface
    {

        if (!self::$filesystem instanceof File) {
            self::$filesystem = new File();
        }

        return self::$filesystem;

    }

    /**
     * @return MarkupInterface
     */
    public static function getYamlMarkup(): MarkupInterface
    {

        if (!self::$markup instanceof Markup) {
            self::$markup = new Markup(new YamlType());
        }

        return self::$markup;

    }

}