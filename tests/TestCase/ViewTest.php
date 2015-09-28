<?php
namespace JeremyHarris\Build\Test\TestCase;

use JeremyHarris\Build\View;
use JeremyHarris\Build\Template\Engine;

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
        $this->engine = new Engine(TEST_APP . DS . 'views');
        $this->engine->addFolder('layouts', TEST_APP . DS . 'layouts');
        $this->View = new View(TEST_APP . DS . 'views' . DS . 'html.php', $this->engine);
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
        new View('invalid file name', $this->engine);
    }

    /**
     * testRender
     *
     * @return void
     */
    public function testRender()
    {
        $result = $this->View->render('layouts::default.php', [
            'test' => 'Span!'
        ]);

        $expected = '<span>Span!</span>';
        $this->assertContains($expected, $result);

        $expected = '<title>View Title</title>';
        $this->assertContains($expected, $result);
    }

    /**
     * testRenderMarkdownView
     *
     * @return void
     */
    public function testRenderMarkdownView()
    {
        $view = new View(TEST_APP . DS . 'views' . DS . 'markdown.md', $this->engine);
        $result = $view->render('layouts::default.php');

        $title = '<title>Markdown</title>';
        $h1 = '<h1>Hello</h1>';
        $p = '<p>This is some <code>markdown</code></p>';
        $ul = '<ul><li>View rendering should automatically parse this</li></ul>';

        $this->assertContains($title, $result);
        $this->assertContains($h1, $result);
        $this->assertContains($p, $result);
        $this->assertContains($ul, $result);
    }

    /**
     * testIsMarkdown
     *
     * @return void
     */
    public function testIsMarkdown()
    {
        $this->assertFalse($this->View->isMarkdown());

        $mdView = new View(TEST_APP . DS . 'views' . DS . 'markdown.md', $this->engine);
        $this->assertTrue($mdView->isMarkdown());
    }

    /**
     * testGetTitle
     *
     * @return void
     */
    public function testGetTitle()
    {
        $view = new View(TEST_APP . DS . 'views' . DS . 'markdown.md', $this->engine);

        $result = $view->getTitle();
        $this->assertEquals('Markdown', $result);

        $view = new View(TEST_APP . DS . 'views' . DS . '2013' . DS . '05' . DS . 'my-may-post.md', $this->engine);

        $result = $view->getTitle();
        $this->assertEquals('My May Post', $result);
    }

}
