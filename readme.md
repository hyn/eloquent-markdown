# Eloquent markdown

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/hyn/eloquent-markdown/license.md)
[![Latest Stable Version](https://img.shields.io/packagist/v/hyn/eloquent-markdown.svg)](https://github.com/hyn/eloquent-markdown)
[![Build Status](https://img.shields.io/travis/hyn/eloquent-markdown/master.svg?maxAge=2592000&style=flat-square)](https://travis-ci.org/hyn/eloquent-markdown)
[![Total Downloads](https://img.shields.io/packagist/dt/hyn/eloquent-markdown.svg)](https://github.com/hyn/eloquent-markdown)
[![Donate](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://paypal.me/luceos)

Ever felt like your markdown files could use meta information.. And once you've added frontmatter logic, whether it
would be amazing to handle those files more humanely.. 

So let's combine markdown files, frontmatter and eloquent!

That something like the below:

```markdown
{
  "title": "Some elaborate .."
}
And of course your regular markdown nonsense.
```

Mutates into an object:

```php
echo $page->title; // Some elaborate ..
echo $page->getRenderedMarkdown(); // <p>And of course your regular markdown nonsense.</p>
echo $page->getMarkdown(); // And of course your regular markdown nonsense.
$page->setMarkdown('Foojaa'); // Yes update
$page->markdown = 'Foobar'; // Or on the assigned property
$page->save(); // Write the file to disk, YES!
```

## Installation

```bash
composer require hyn/eloquent-markdown
```

Now create a model you want to use for markdown files:

```php
class Page extends \Hyn\Eloquent\Markdown\Model
{}
```

And setup the filesystem and markdown parser resolution, add in  AppServiceProvider or somewhere:

```php
use Hyn\Eloquent\Markdown\Model;
use Hyn\Frontmatter\Parser;
use cebe\markdown\Markdown;

// ..

public function register() {
            Model::setMarkdownParser(new Parser(new Markdown));
            Model::setFilesystem($this->app->make('filesystem')->disk('content'));
}
```

> Set `content` to the disk you configured to load the markdown files from. Or instantiate your own filesystem
instance.

## Usage

So if you have a file `some/foo.md`, use `Page::find('some/foo.md');` to create a Page object, where any frontmatter
meta information is stored as properties, the markdown contents are stored in the original state as the `markdown` property
and the generated html is assigned to the `contents` attribute.
