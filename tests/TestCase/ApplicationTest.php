<?php
namespace JeremyHarris\Build\Test\TestCase;

use JeremyHarris\Build\Application;

/**
 * Application test
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * testSlugToTitle
     *
     * @return void
     */
    public function testSlugToTitle()
    {
        $result = Application::slugToTitle('my-slug-name');
        $expected = 'My Slug Name';
        $this->assertEquals($expected, $result);
    }

}
