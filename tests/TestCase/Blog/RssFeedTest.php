<?php
namespace JeremyHarris\Build\Test\TestCase\Blog;

use JeremyHarris\Build\Blog\RssFeed;

/**
 * RssFeed test
 */
class RssFeedTest extends \PHPUnit_Framework_TestCase
{

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->Rss = new RssFeed();
        $this->Rss
            ->setTitle('Title')
            ->setDescription('My blog & stuff')
            ->setLink('http://example.com/');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown() {
        unset($this->Rss);
        parent::tearDown();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidRss()
    {
        $this->Rss->setTitle(null);
        $this->Rss->build(TEST_APP . DS . 'views');
    }

    /**
     * tests rss
     *
     * @return void
     */
    public function testRss()
    {
        $xml = $this->Rss->build(TEST_APP . DS . 'views');
        $this->assertXmlStringEqualsXmlFile(FIXTURES . DS . 'rss.xml', $xml->asXml());
    }
}