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