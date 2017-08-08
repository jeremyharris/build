<?php
namespace JeremyHarris\Build\Test\TestCase;

use JeremyHarris\Build\Blog;
use JeremyHarris\Build\Blog\Post;

/**
 * Blog test
 */
class BlogTest extends \PHPUnit_Framework_TestCase
{

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->Blog = new Blog(TEST_APP . DS . 'views');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown() {
        unset($this->Blog);
        parent::tearDown();
    }

    /**
     * testGetPosts
     *
     * @return void
     */
    public function testGetPosts()
    {
        $posts = $this->Blog->getPosts();

        $result = array_keys($posts);
        $expected = [
            '2012',
            '2013',
        ];
        $this->assertEquals($expected, $result);

        $result = array_keys($posts['2012']);
        $expected = [
            '01',
            '02',
        ];
        $this->assertEquals($expected, $result);

        foreach ($posts['2012']['01'] as $post) {
            $this->assertInstanceOf(Post::class, $post);
        }
    }

    /**
     * testGetLatest
     *
     * @return void
     */
    public function testGetLatest()
    {
        $result = $this->Blog->getLatest();
        $this->assertInstanceOf(Post::class, $result);
        $this->assertSame('most-recent', $result->slug());
    }
}