<?php

namespace JeremyHarris\Build;

use JeremyHarris\Build\View;

/**
 * Simple build class
 */
class Build
{

    const VIEW_PATH = 'views';
    const ASSET_PATH = 'assets';
    const WEBROOT_PATH = 'webroot';

    /**
     * Site target
     *
     * @var string
     */
    protected $site = null;

    /**
     * Build target
     *
     * @var string
     */
    protected $build = null;

    /**
     * Force building all the files
     *
     * @var bool
     */
    protected $force = false;

    /**
     * Layout to use when rendiner views during build
     *
     * @var string
     */
    protected $layout = 'layout.php';

    /**
     * List of files that were built
     *
     * @var array
     */
    protected $builtFiles = [];

    /**
     * Constructor
     *
     * @param string $siteTarget  Site target
     * @param string $buildTarget Build target directory
     * @throws \Exception
     */
    public function __construct($siteTarget, $buildTarget)
    {
        if (!is_dir($buildTarget) || !is_writable($buildTarget)) {
            throw new \Exception(sprintf('%s is not a directory that can be used to build', $buildTarget));
        }
        if (!is_dir($siteTarget)) {
            throw new \Exception(sprintf('%s is not a valid site target', $siteTarget));
        }
        $this->site = rtrim($siteTarget, DIRECTORY_SEPARATOR);
        $this->build = rtrim($buildTarget, DIRECTORY_SEPARATOR);
    }

    /**
     * Builds the site
     *
     * @return void
     */
    public function build($force = false)
    {
        $this->force = $force;

        $webrootPath = self::WEBROOT_PATH . DIRECTORY_SEPARATOR;
        $webroot = $this->getFileTree($this->site . DIRECTORY_SEPARATOR . $webrootPath);
        foreach ($webroot as $file) {
            $this->addFileToBuild($this->site . DIRECTORY_SEPARATOR . $webrootPath . $file, dirname($file));
        }

        $viewPath = self::VIEW_PATH . DIRECTORY_SEPARATOR;
        $views = $this->getFileTree($this->site . DIRECTORY_SEPARATOR . $viewPath);
        foreach ($views as $file) {
            $this->addFileToBuild($this->site . DIRECTORY_SEPARATOR . $viewPath . $file, dirname($file), true);
        }

        $jsPath = self::ASSET_PATH . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR;
        $scripts = $this->getFileTree($this->site . DIRECTORY_SEPARATOR . $jsPath);
        array_walk($scripts, [$this, 'prependDirectory'], $jsPath);
        $concatenated = $this->concatFiles($scripts);
        $this->addRawFile('scripts.js', $concatenated);

        $cssPath = self::ASSET_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
        $styles = $this->getFileTree($this->site . DIRECTORY_SEPARATOR . $cssPath);
        array_walk($styles, [$this, 'prependDirectory'], $cssPath);
        $concatenated = $this->concatFiles($styles);
        $this->addRawFile('styles.css', $concatenated);
    }

    /**
     * Adds a file to the build
     *
     * @param  string $fullPath Full path to file
     * @param  bool   $isView   Use View class to render?
     * @return void
     */
    public function addFileToBuild($fullPath, $directory = '.', $isView = false)
    {
        $fileInfo = new \SplFileInfo($fullPath);
        $newFilename = $fileInfo->getBasename();
        $relativeFilepath = str_replace($this->site . DIRECTORY_SEPARATOR, '', $fullPath);

        $directory = trim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if ($directory === './' || $directory === DIRECTORY_SEPARATOR) {
            $directory = null;
        }

        if ($isView) {
            $newFilename = $fileInfo->getBasename($fileInfo->getExtension()) . 'html';
        }

        if (!$this->force && !$this->modified($relativeFilepath, $directory . $newFilename)) {
            return;
        }

        if ($isView) {
            $contents = $this->renderView(new View($fullPath));
        } else {
            $contents = file_get_contents($fullPath);
        }

        $this->addRawFile($directory . $newFilename, $contents);
    }

    /**
     * Tries rendering the view within the layout, if that fails (i.e., no layout
     * is defined) it just returns the rendered view
     *
     * @param View $view View to render
     * @return string
     */
    public function renderView(View $view)
    {
        $viewContents = $view->render();
        try {
            $layout = new View($this->site . DIRECTORY_SEPARATOR . $this->layout);
            $layout->set('post', $view->getPost());
            $layout->set('title', $view->getTitle());
            $layout->set('content', $viewContents);
            $viewContents = $layout->render();
        } catch (\Exception $e) {
        }
        return $viewContents;
    }

    /**
     * Gets a relative list of files in `$directory`
     *
     * @param  string $directory Directory path
     * @return array
     */
    public function getFileTree($directory)
    {
        $directoryIterator = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator);
        $files = [];
        foreach ($iterator as $file) {
            $files[] = trim(str_replace($directory, '', $file->getPathname()), DIRECTORY_SEPARATOR);
        }
        return $files;
    }

    /**
     * Concatenates an array of files (usually assets)
     *
     * @param array $paths
     * @return string
     */
    public function concatFiles(array $paths)
    {
        $concatenated = '';
        foreach ($paths as $path) {
            $concatenated .= file_get_contents($this->site . DIRECTORY_SEPARATOR . $path) . PHP_EOL;
        }
        return $concatenated;
    }

    /**
     * Sets the layout to use
     *
     * @param string $layout Relative path to layout
     */
    public function useLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Prepends a directory to a path (used by `array_walk`)
     *
     * @param  string $path      Path
     * @param  string $key       Array key value
     * @param  string $directory Directory
     * @return void
     */
    public function prependDirectory(&$path, $key, $directory)
    {
        $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * Checks if a file has been modified or built
     *
     * @param  string $siteFilepath  Relative path to original site file
     * @param  string $buildFilepath Relative path to file that will be built
     * @return bool
     */
    public function modified($siteFilepath, $buildFilepath)
    {
        $siteFilepath = $this->site . DIRECTORY_SEPARATOR . $siteFilepath;
        $buildFilepath = $this->build . DIRECTORY_SEPARATOR . $buildFilepath;

        if (!file_exists($buildFilepath)) {
            return true;
        }
        $siteFile = new \SplFileInfo($siteFilepath);
        $buildFile = new \SplFileInfo($buildFilepath);
        return $siteFile->getMTime() > $buildFile->getMTime();
    }

    /**
     * Gets a list of files that were built
     *
     * @return array
     */
    public function getBuiltFiles()
    {
        return $this->builtFiles;
    }

    /**
     * Resets the build
     *
     * @return void
     */
    public function reset()
    {
        $this->builtFiles = [];
    }

    /**
     * Adds a file to the build
     *
     * @param string $filepath Relative filepath (build file name)
     * @param string $contents File contents
     */
    protected function addRawFile($filepath, $contents)
    {
        $buildPath = $this->build . DIRECTORY_SEPARATOR . $filepath;
        $directory = dirname($buildPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }
        $this->builtFiles[] = $buildPath;
        file_put_contents($buildPath, $contents);
    }
}
