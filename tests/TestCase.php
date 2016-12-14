<?php

namespace Hyn\Eloquent\Markdown\Tests;

use cebe\markdown\Markdown;
use Hyn\Eloquent\Markdown\Tests\Models\Example;
use Hyn\Frontmatter\Parser;
use Illuminate\Filesystem\FilesystemManager;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        Example::setFilesystem(
            (new FilesystemManager(null))
                ->createLocalDriver([
                    'root' => __DIR__ . '/files'
                ])
        );
        Example::setMarkdownParser(new Parser(new Markdown));
    }
}
