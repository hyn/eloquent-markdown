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

    const FILE_TYPE = '.md';

    /**
     * @var Markdown
     */
    protected static $markdownParser;

    /**
     * @var Filesystem
     */
    protected static $filesystem;

    /**
     * @param Filesystem $filesystem
     */
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
     * @param Markdown $parser
     */
    public static function setMarkdownParser(Markdown $parser)
    {
        static::$markdownParser = $parser;
    }

    /**
     * @return Markdown
     */
    public static function getMarkdownParser()
    {
        return static::$markdownParser;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $saved = $this->getFilesystem()->put(
            $this->getPath(),
            json_encode($this->getMeta(), JSON_PRETTY_PRINT) . "\n" . $this->getMarkdown()
        );

        $this->exists = true;

        if ($saved) {
            $this->finishSave($options);
        }

        return $saved;
    }

    /**
     * The storage location for the file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return sprintf("%s/%s%s",
            $this->getTable(),
            $this->getKey(),
            static::FILE_TYPE
        );
    }
}
