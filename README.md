[![Build
Status](https://travis-ci.org/jeremyharris/build.svg?branch=master)](https://travis-ci.org/jeremyharris/build)

# Build

A little static site generator, built specifically to build little sites with
blog posts.

## Installation

`composer require jeremyharris/build`

## Features

- Stupid dumb easy setup (no config files)
- Concatenates assets
- Builds only modified files
- Some tools for basic blog functionality
- Flexible-ish

## Usage

```php
$build = new \JeremyHarris\Build\Build('/path/to/site_target', 'path/to/build_target');
$build->build();
```

Only files that have been modified since you last built will be built. You can
optionally pass `true` to `build()` to force build all files. You can then get
full paths to newly built files:

```php
$newlyBuiltFiles = $build->getBuiltFiles();
// now deploy them!
```

If you want to manually add build files that aren't within the expected structure,
you can do so:

```php
// add a file to the build root
$build->addFileToBuild('/full/path/to/file.html');
// add a file to a new directory within the build
$build->addFileToBuild('/full/path/to/file.html', 'some/directory');
// render a file as a view (wrap it in the layout)
$build->addFileToBuild('/path/to/my.php', 'some/directory', true);
$build->addFileToBuild('/path/to/my.md', '/', true);
```

## Blogging

Some blogging functionality is provided in the `\JeremyHarris\Build\Blog` class.
It assumes a `YYYY/MM` structure. The `Blog` class is helpful for building an
archive page or getting the latest post. Items returned are `\JeremyHarris\Build\Blog\Post`
objects that contain some helpful methods.

```php
$Blog = new Blog('/path/to/site_target');
$latest = $Blog->getLatest();
$allPosts = $Blog->getPosts();

$linkToLatest = $latest->link();
$latestTitle = $latest->title();
```

## Site target structure

The site target should have a layout like the one below. Anything in `/webroot`
is copied directly to webroot, allowing flexibility in not using views. Items
in `/views` are wrapped in `layout.php` and placed into the directories they
reside in. Views can be php or markdown.

Titles are assumed from the filename slug, so `interesting-article-about-things.md`
is titled "Interesting Article About Things".

```
site
|_ views
|  |
|  |_ about.php
|  |_ contact.md
|     |_ sub
|        |_ article.php
|_ assets
| |_ css
| |  |_ css1.css
| |  |_ css2.css
| |
| |_ js
|    |_ script1.js
|    |_ script2.js
|
|_ webroot
|  |_ robots.txt
|  |_ fonts
|     |_ font1.otf
|
|_ layout.php
```

And builds it into a site like this:

```
build
|_ fonts
|  |_ font1.otf
|
|_ sub
|  |_ article.html
|
|_ styles.css
|_ scripts.js
|_ about.html
|_ contact.html
|_ robots.txt
```

## Example site

- [someguyjeremy.com](https://github.com/jeremyharris/someguyjeremy)