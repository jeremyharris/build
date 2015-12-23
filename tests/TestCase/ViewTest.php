<?php
namespace JeremyHarris\Build\Test\TestCase;

use JeremyHarris\Build\View;

/**
 * View test
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->View = new View(TEST_APP . DS . 'views' . DS . 'html.php');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->View);
        parent::tearDown();
    }

    /**
     * testInvalidViewFile
     *
     * @expectedException \Exception
     */
    public function testInvalidViewFile()
    {
        new View('invalid file name');
    }

    /**
     * testSetAndGet
     *
     * @return void
     */
    public function testSetAndGet()
    {
        $this->View->set('var', 'test');
        $result = $this->View->get('var');
        $expected = 'test';
        $this->assertEquals($expected, $result);
    }

    /**
     * testGetException
     *
     * @expectedException \OutOfBoundsException
     */
    public function testGetException()
    {
        $this->View->get('missing');
    }

    /**
     * testRender
     *
     * @return void
     */
    public function testRender()
    {
        $this->View->set('test', 'Span!');
        $result = $this->View->render();
        $expected = '<span>Span!</span>';
        $this->assertEquals($expected, $result);
    }

    /**
     * testRenderMarkdownView
     *
     * @return void
     */
    public function testRenderMarkdownView()
    {
        $view = new View(TEST_APP . DS . 'views' . DS . 'markdown.md');
        $result = $view->render();

        $h1 = '/<h1>(.+)<\/h1>/';
        $p = '/<p>(.+)<code>(.+)<\/code><\/p>/';
        $ul = '/<ul>(.*)<\/ul>/s';
        $li = '/<li>(.+)<\/li>/';

        $this->assertRegExp($h1, $result);
        $this->assertRegExp($p, $result);
        $this->assertRegExp($ul, $result);
        $this->assertRegExp($li, $result);
    }

    /**
     * testIsMarkdown
     *
     * @return void
     */
    public function testIsMarkdown()
    {
        $this->assertFalse($this->View->isMarkdown());

        $mdView = new View(TEST_APP . DS . 'views' . DS . 'markdown.md');
        $this->assertTrue($mdView->isMarkdown());
    }

    /**
     * testGetTitle
     *
     * @return void
     */
    public function testGetTitle()
    {
        $view = new View(TEST_APP . DS . 'views' . DS . 'markdown.md');

        $result = $view->getTitle();
        $this->assertEquals('Markdown', $result);

        $view = new View(TEST_APP . DS . 'views' . DS . '2013' . DS . '05' . DS . 'my-may-post.md');

        $result = $view->getTitle();
        $this->assertEquals('My May Post', $result);
    }

    /**
     * testGetViewTitle
     *
     * @return void
     */
    public function testGetViewTitle()
    {
        $view = new View(TEST_APP . DS . 'views' . DS . 'html.php');

        $result = $view->getTitle();
        $this->assertEquals('View Title', $result);
    }

    /**
     * testGetPost
     *
     * @return void
     */
    public function testGetPost()
    {
        $view = new View(TEST_APP . DS . 'views' . DS . 'html.php');

        $result = $view->getPost();
        $this->assertFalse($result);

        $view = new View(TEST_APP . DS . 'views' . DS . '2013' . DS . '05' . DS . 'my-may-post.md');

        $result = $view->getPost();
        $this->assertInstanceOf('\\JeremyHarris\\Build\\Blog\\Post', $result);
    }

}
