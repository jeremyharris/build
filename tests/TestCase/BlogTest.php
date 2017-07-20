<?php
namespace JeremyHarris\Build\Test\TestCase;

use JeremyHarris\Build\Blog;
use JeremyHarris\Build\Blog\Post;
use SplFileObject;

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
        $moreRecentFile = $this->getMockBuilder(SplFileObject::class)
            ->setMethods(['getMTime', 'getBasename'])
            ->setConstructorArgs([
                TEST_APP . DS . 'views' . DS . '2013' . DS . '05' . DS . 'most-recent.md'
            ])
            ->getMock();

        $moreRecentFile
            ->expects($this->any())
            ->method('getBasename')
            ->will($this->returnValue('more-recent'));

        $moreRecentFile
            ->expects($this->any())
            ->method('getMTime')
            ->will($this->returnValue(strtotime('+2 days')));

        $this->Blog->addPost($moreRecentFile);
        $result = $this->Blog->getLatest();
        $this->assertInstanceOf(Post::class, $result);
        $this->assertSame($moreRecentFile, $result->source());
        $this->assertSame('more-recent', $result->slug());
    }
}