<?php
namespace JeremyHarris\Build\Test\TestCase;

use JeremyHarris\Build\Blog\Post;

/**
 * Post test
 */
class PostTest extends \PHPUnit_Framework_TestCase
{

    /**
     * testPost
     *
     * @return void
     */
    public function testPost()
    {
        $filepath = TEST_APP . DS . 'views' . DS . '2012' . DS . '01' . DS . 'post-markdown.md';
        $file = new \SplFileObject($filepath);
        $Post = new Post($file);

        $this->assertEquals('2012', $Post->year());
        $this->assertEquals('01', $Post->month());
        $this->assertEquals('Post Markdown', $Post->title());
        $this->assertEquals('post-markdown', $Post->slug());
        $this->assertEquals($file, $Post->source());

        $link = $Post->link();
        $this->assertRegExp('/<a/', $link);
        $this->assertRegExp('/href=\"\/2012\/01\/post-markdown\.html\"/', $link);

        $url = $Post->url();
        $this->assertSame('/2012/01/post-markdown.html', $url);

        $filepath = TEST_APP . DS . 'views' . DS . '2012' . DS . '01' . DS . 'post.html';
        $file = new \SplFileObject($filepath);
        $Post = new Post($file);

        $this->assertEquals('Post', $Post->title());
        $this->assertEquals('post', $Post->slug());
    }

    /**
     * testInvalidPost
     *
     * @expectedException \Exception
     * @return void
     */
    public function testInvalidPost()
    {
        $filepath = TEST_APP . DS . 'views' . DS . 'html.php';
        $file = new \SplFileObject($filepath);
        new Post($file);
    }
}