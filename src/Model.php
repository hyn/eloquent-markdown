<?php

namespace Hyn\Eloquent\Markdown;

use Hyn\Frontmatter\Parser as Markdown;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
     * The attribute that holds the raw markdown contents.
     *
     * @var string
     */
    protected $markdownAttribute = 'markdown';

    /**
     * The attribute that holds the rendered markdown contents.
     *
     * @var string
     */
    protected $renderedMarkdownAttribute = 'contents';

    /**
     * The file type of the markdown file.
     */
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
     * @param static $model
     * @param null $markdown
     * @return array
     */
    protected static function extractAttributes(&$model, $markdown = null): array
    {
        if (!$markdown && $model->exists) {
            $markdown = static::getFilesystem()->get($model->getPath());
        }

        $parsed = static::getMarkdownParser()->parse($markdown);

        if (count($parsed['meta'])) {
            $model->forceFill(Arr::except(
                $parsed['meta'], [
                $model->getKeyName(),
                $model->markdownAttribute,
                $model->renderedMarkdownAttribute
            ]));
        }
        return $parsed;
    }

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
     * @param string|null $markdown
     * @param bool $render
     */
    public function setMarkdownContents(string $markdown = null, $render = true)
    {
        if ($render) {
            static::extractAttributes($this, $markdown);
        }

        $this->setAttribute($this->markdownAttribute, $markdown);
    }

    /**
     * @return string|null
     */
    public function getMarkdownContents()
    {
        return $this->getAttribute($this->markdownAttribute);
    }

    /**
     * @return string|null
     */
    public function getRenderedMarkdown()
    {
        return $this->getAttribute($this->renderedMarkdownAttribute);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getFrontmatterProperties()
    {
        return collect(Arr::except($this->getAttributes(), [
            $this->renderedMarkdownAttribute,
            $this->markdownAttribute
        ]));
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $saved = $this->getFilesystem()->put(
            $this->getPath(),
            $this->getFrontmatterProperties()->toJson(JSON_PRETTY_PRINT) . "\n" . $this->getMarkdownContents()
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
        return sprintf("%s%s",
            $this->getKey(),
            static::FILE_TYPE
        );
    }

    /**
     * @param string $path
     * @return static|null
     */
    public static function find(string $path)
    {
        if (Str::endsWith($path, static::FILE_TYPE)) {
            $path = substr($path, 0, -(strlen(static::FILE_TYPE)));
        }

        $obj = new Static;
        $obj->id = $path;

        if (!static::getFilesystem()->exists($obj->getPath())) {
            return null;
        }

        $obj->exists = true;

        $parsed = static::extractAttributes($obj);

        $obj->setAttribute($obj->markdownAttribute, Arr::get($parsed, 'markdown'));
        $obj->setAttribute($obj->renderedMarkdownAttribute, Arr::get($parsed, 'html'));

        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    protected function performDeleteOnModel()
    {
        $this->getFilesystem()->delete($this->getPath());
    }
}
