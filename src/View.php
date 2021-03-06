<?php

namespace JeremyHarris\Build;

use JeremyHarris\Build\Application;
use JeremyHarris\Build\Parser\TwitterHandleParser;
use JeremyHarris\Build\Blog\Post;
use League\CommonMark\Environment;
use League\CommonMark\DocParser;
use League\CommonMark\HtmlRenderer;

/**
 * Simple view class
 */
class View
{

    /**
     * Array of view vars to be passed to the view on render
     *
     * @var array
     */
    protected $vars = [];

    /**
     * The view filepath
     *
     * @var string
     */
    protected $filename = null;

    /**
     * Constructor
     *
     * @param string $filename Full path to view file
     * @throws \Exception
     */
    public function __construct($filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception(sprintf('%s does not exist', $filename));
        }
        $this->filename = $filename;
    }

    /**
     * Set a var for the view
     *
     * @param string $var Var name
     * @param mixed $value Value
     * @return void
     */
    public function set($var, $value)
    {
        $this->vars[$var] = $value;
    }

    /**
     * Gets a previously set view var
     *
     * @param string $var Var name
     * @return mixed The value
     * @throws \OutOfBoundsException
     */
    public function get($var)
    {
        if (!array_key_exists($var, $this->vars)) {
            throw new \OutOfBoundsException(sprintf('%s has not been set', $var));
        }
        return $this->vars[$var];
    }

    /**
     * Returns rendered view
     *
     * @return string
     */
    public function render()
    {
        if ($this->isMarkdown()) {
            $environment = Environment::createCommonMarkEnvironment();
            $environment->addInlineParser(new TwitterHandleParser());
            $parser = new DocParser($environment);
            $htmlRenderer = new HtmlRenderer($environment);
            $document = $parser->parse(file_get_contents($this->filename));
            return $htmlRenderer->renderBlock($document);
        }
        ob_start();
        extract($this->vars);
        require $this->filename;
        return ob_get_clean();
    }

    /**
     * Checks for markdown extensions in the filename
     *
     * @return bool
     */
    public function isMarkdown()
    {
        $markdownExts = ['md', 'markdown'];
        $ext = pathinfo($this->filename, \PATHINFO_EXTENSION);
        return in_array($ext, $markdownExts);
    }

    /**
     * Gets title based on the view slug
     *
     * @return string
     */
    public function getTitle()
    {
        try {
            $this->render();
            return $this->get('title');
        } catch (\OutOfBoundsException $ex) {
            $file = new \SplFileInfo($this->filename);
            return Application::slugToTitle($file->getBasename('.' . $file->getExtension()));
        }
    }

    /**
     * Gets the post object, if this view is a post
     *
     * @return bool|Post
     */
    public function getPost()
    {
        try {
            return new Post(new \SplFileObject($this->filename));
        } catch (\Exception $ex) {
            return false;
        }
    }
}
