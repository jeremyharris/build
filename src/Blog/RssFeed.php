<?php
namespace JeremyHarris\Build\Blog;

use DateTime;
use JeremyHarris\Build\Blog;
use SimpleXMLElement;
use RuntimeException;

/**
 * RSS feed builder
 */
class RssFeed
{
    private $title;
    private $link;
    private $description;

    /**
     * Sets the title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Sets the link
     *
     * @param string $link
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function build($sitepath)
    {
        if ($this->title === null || $this->link === null || $this->description === null)
        {
            throw new RuntimeException('Cannot build RSS feed without setting all required settings.');
        }

        $xml = new SimpleXMLElement('<rss version="2.0"></rss>');
        $channel = $xml->addChild('channel');
        $channel->addChild('title', htmlspecialchars($this->title));
        $channel->addChild('link', htmlspecialchars($this->link));
        $channel->addChild('description', htmlspecialchars($this->description));

        $blog = new Blog($sitepath);

        $allPosts = array_reverse($blog->getPosts(), true);
        $posts = [];
        foreach ($allPosts as $postsByYear) {
            foreach ($postsByYear as $postsByMonth) {
                foreach ($postsByMonth as $post) {
                    array_push($posts, $post);
                }
            }
        }
        $posts = array_slice($posts, 0, 20);

        $linkPrefix = rtrim($this->link, '/');
        foreach ($posts as $post) {
            $postFile = $post->source();
            $mtime = (new DateTime())->setTimestamp($postFile->getMTime());

            $title = $post->title();
            $pubDate = (new DateTime)
                ->setTime(0, 0, 0)
                ->setDate($post->year(), $post->month(), date('j', $mtime->format('j')))
                ->format(DATE_RFC2822);
            $link = $linkPrefix . $post->url();

            $item = $channel->addChild('item');
            $item->addChild('title', htmlspecialchars($title));
            $item->addChild('link', $link);
            $item->addChild('guid', $link);
            $item->addChild('pubDate', $pubDate);
        }

        return $xml;
    }
}

