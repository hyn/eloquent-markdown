<?php

namespace Hyn\Eloquent\Markdown\Tests;

use Hyn\Eloquent\Markdown\Tests\Models\Example;

class ModelTest extends TestCase
{
    /**
     * @test
     */
    public function reads()
    {
        $foo = Example::find('foo');
        $this->assertEquals('foo', $foo->title);
        $this->assertEquals('foo', $foo->id);
        $this->assertEquals("<p>bar</p>\n", $foo->getRenderedMarkdown());
    }

    /**
     * @test
     */
    public function writes_and_deletes()
    {
        $foo = new Example();
        $foo->id = 'write';
        $foo->setMarkdownContents('bar');
        $foo->save();

        $this->assertFileExists(__DIR__ . "/files/{$foo->getPath()}");
        $this->assertTrue($foo->exists);

        $foo->delete();

        $this->assertFalse($foo->exists);
        $this->assertFileNotExists(__DIR__ . "/files/{$foo->getPath()}");
    }
}
