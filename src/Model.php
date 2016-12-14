<?php

namespace Hyn\Eloquent\Markdown;

use cebe\markdown\Parser as Markdown;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Model extends Eloquent
{
    /**
     * Overrides the type of the key.
     *
     * @info Not overriding this will cast an id property to integer.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var Filesystem
     */
    protected static $filesystem;

    /**
     * @var string
     */
    protected static $disk;

    public static function setFilesystem(Filesystem $filesystem)
    {
        static::$filesystem = $filesystem;
    }

    /**
     * @return Filesystem
     */
    public static function getFilesystem(): Filesystem
    {
        return static::$filesystem;
    }

    /**
     * @param string $disk
     */
    public static function setDisk(string $disk)
    {
        static::$disk = $disk;
    }

    /**
     * @return string
     */
    public static function getDisk()
    {
        return static::$disk;
    }

    /**
     * @param Markdown $parser
     */
    public static function setMarkdownParser(Markdown $parser)
    {
        static::$markdownParser = $parser;
    }
}
