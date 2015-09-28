<?php

namespace JeremyHarris\Build;

use JeremyHarris\Build\Application;
use JeremyHarris\Build\Parser\TwitterHandleParser;
use League\CommonMark\Environment;
use League\CommonMark\DocParser;
use League\CommonMark\HtmlRenderer;
use SplFileInfo;

/**
 * Simple view class
 */
class View
{

    /**
     * The view filepath
     *
     * @var SplFileInfo
     */
    protected $file = null;

    /**
     * Template engine
     *
     * @var \League\Plates\Engine
     */
    protected $engine;

    /**
     * Constructor
     *
     * @param string $filename Full path to view file
     * @throws \Exception
     */
    public function __construct($filename, $engine)
    {
        if (!file_exists($filename)) {
            throw new \Exception(sprintf('%s does not exist', $filename));
        }
        $this->file = new SplFileInfo($filename);

        $this->engine = $engine;
        $this->engine->setFileExtension(null);
        $this->engine->setDirectory($this->file->getPath());
    }

    /**
     * Returns rendered view
     *
     * @param string $layout Layout name
     * @param array $data Data
     * @return string
     */
    public function render($layout, $data = [])
    {
        $contents = null;

        if ($this->isMarkdown()) {
            $environment = Environment::createCommonMarkEnvironment();
            $environment->addInlineParser(new TwitterHandleParser());
            $parser = new DocParser($environment);
            $htmlRenderer = new HtmlRenderer($environment);
            $document = $parser->parse(file_get_contents($this->file->getRealPath()));
            $contents = $htmlRenderer->renderBlock($document);
        }

        if (!$this->isPhp()) {
            $contents = file_get_contents($this->file->getRealPath());
        }

        $layoutData = [
            'title' => $this->getTitle()
        ];

        if (!empty($contents)) {
            $layoutData += [
                'content' => $contents
            ];
        }

        $template = $this->engine->make($this->file->getBasename());
        $template->layout($layout, $layoutData);
        return $template->render($data);
    }

    /**
     * Checks for markdown extensions in the filename
     *
     * @return bool
     */
    public function isMarkdown()
    {
        $markdownExts = ['md', 'markdown'];
        $ext = $this->file->getExtension();
        return in_array($ext, $markdownExts);
    }

    /**
     * Returns true if this is a PHP file that should be rendered using Plates
     *
     * @return bool
     */
    public function isPhp()
    {
        return $this->file->getExtension() === 'php';
    }

    /**
     * Gets title based on the view slug
     *
     * @return string
     */
    public function getTitle()
    {
        return Application::slugToTitle($this->file->getBasename('.' . $this->file->getExtension()));
    }
}