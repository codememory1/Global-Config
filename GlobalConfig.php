<?php

namespace Codememory\Components\GlobalConfig;

use Codememory\Components\GlobalConfig\Interfaces\GlobalConfigInterface;
use Codememory\Components\Markup\Interfaces\MarkupInterface;
use Codememory\Components\Markup\Markup;
use Codememory\Components\Markup\Types\JsonType;
use Codememory\FileSystem\File;
use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\Support\Arr;
use Codememory\Support\Str;

/**
 * Class GlobalConfig
 *
 * @package Codememory\Components\GloablConfig
 *
 * @author  Codememory
 */
final class GlobalConfig implements GlobalConfigInterface
{

    private const EXTENSION = '.json';
    private const PATH = '.config/';
    private const FILENAME = '.codememory' . self::EXTENSION;
    private const BACKUP_FILENAME = 'codememory.backup';

    /**
     * @var string
     */
    private static string $path = self::PATH;

    /**
     * @var string
     */
    private static string $filename = self::FILENAME;

    /**
     * @var string
     */
    private static string $extension = self::EXTENSION;

    /**
     * @var string
     */
    private static string $backupFilename = self::BACKUP_FILENAME;

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
    public static function setPath(string $path): GlobalConfigInterface
    {

        self::$path = $path;

        return new self();

    }

    /**
     * @inheritDoc
     */
    public static function setFilename(string $filename): GlobalConfigInterface
    {

        self::$filename = $filename;
        self::$extension = Str::trimToSymbol($filename, '.');

        return new self();

    }

    /**
     * @inheritDoc
     */
    public static function setBackupFilename(string $filename): GlobalConfigInterface
    {

        self::$backupFilename = $filename;

        return new self();

    }

    /**
     * @inheritDoc
     */
    public static function getPath(): string
    {

        return self::$path;

    }

    /**
     * @inheritDoc
     */
    public static function getFilename(): string
    {

        return self::$filename;

    }

    /**
     * @inheritDoc
     */
    public static function getExtension(): string
    {

        return self::$extension;

    }

    /**
     * @inheritDoc
     */
    public static function getBackupFilename(): string
    {

        return self::$backupFilename;

    }

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

        $gluedPathFile = self::getPath() . self::getFilename();
        $path = Str::cut($gluedPathFile, mb_stripos($gluedPathFile, self::EXTENSION));
        $data = [];

        if (self::exist()) {
            $data = self::getMarkupType()->open($path)->get();
            self::getMarkupType()->close();
        }

        return $data;

    }

    /**
     * @inheritDoc
     */
    public static function exist(): bool
    {

        return self::getFilesystem()->exist(self::getPath() . self::getFilename());

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
    public static function getMarkupType(): MarkupInterface
    {

        if (!self::$markup instanceof Markup) {
            self::$markup = new Markup(new JsonType());
        }

        return self::$markup;

    }

}