<?php

namespace Codememory\Components\GlobalConfig\Interfaces;

/**
 * Interface GlobalConfigInterface
 * @package Codememory\Components\GlobalConfig\Interfaces
 *
 * @author  Codememory
 */
interface GlobalConfigInterface
{

    /**
     * @param string $path
     *
     * @return GlobalConfigInterface
     */
    public static function setPath(string $path): GlobalConfigInterface;

    /**
     * @param string $filename
     *
     * @return GlobalConfigInterface
     */
    public static function setFilename(string $filename): GlobalConfigInterface;

    /**
     * @param string $filename
     *
     * @return GlobalConfigInterface
     */
    public static function setBackupFilename(string $filename): GlobalConfigInterface;

    /**
     * @return string
     */
    public static function getPath(): string;

    /**
     * @return string
     */
    public static function getFilename(): string;

    /**
     * @return string
     */
    public static function getExtension(): string;

    /**
     * @return string
     */
    public static function getBackupFilename(): string;

    /**
     * @param string $keys
     *
     * @return mixed
     */
    public static function get(string $keys): mixed;

    /**
     * @return array
     */
    public static function getAll(): array;

    /**
     * @return bool
     */
    public static function exist(): bool;

}