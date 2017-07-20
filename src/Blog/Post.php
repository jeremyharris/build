<?php
namespace JeremyHarris\Build\Blog;

use JeremyHarris\Build\Application;

/**
 * Post
 *
 * Expects posts to specifically be placed in SOME_DIR/YEAR/MONTH/post-file.ext
 */
class Post
{

    /**
     * Slug
     *
     * @var string
     */
    protected $slug;

    /**
     * Year
     *
     * @var string
     */
    protected $year;

    /**
     * Month
     *
     * @var string
     */
    protected $month;

    /**
     * Post file object
     *
     * @var \SplFileObject
     */
    protected $source;

    /**
     * Constructor
     *
     * @param \SplFileObject $post Post source file
     */
    public function __construct(\SplFileObject $post)
    {
        $this->source = $post;

        if (!preg_match('/[\d]{4}\/[\d]{2}\/(.+)$/', $post->getPathname())) {
            throw new \Exception(sprintf('%s is not a valid post', $post->getPathname()));
        }

        $paths = explode(DIRECTORY_SEPARATOR, $post->getPath());

        $this->month = $paths[count($paths) - 1];
        $this->year = $paths[count($paths) - 2];
        $this->slug = $post->getBasename('.' . $post->getExtension());
    }

    /**
     * Gets an HTML link to the post, where it will be built
     *
     * @return string
     */
    public function link()
    {
        $url = $this->url();
        $title = $this->title();
        return "<a href=\"$url\">$title</a>";
    }

    /**
     * Gets the URL to the post, starting with /
     *
     * @return string
     */
    public function url()
    {
        return "/$this->year/$this->month/$this->slug.html";
    }

    /**
     * Gets post title based on slug
     *
     * @return string
     */
    public function title()
    {
        return Application::slugToTitle($this->slug);
    }

    /**
     * Gets slug
     *
     * @return string
     */
    public function slug()
    {
        return $this->slug;
    }

    /**
     * Gets post year
     *
     * @return string
     */
    public function year()
    {
        return $this->year;
    }

    /**
     * Gets post month
     *
     * @return string
     */
    public function month()
    {
        return $this->month;
    }

    /**
     * Gets post source file object
     *
     * @return \SplFileObject
     */
    public function source()
    {
        return $this->source;
    }

}
