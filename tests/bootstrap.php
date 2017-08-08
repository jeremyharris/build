<?php

require 'vendor/autoload.php';

define('DS', DIRECTORY_SEPARATOR);
define('TEST_APP', __DIR__ . DS . 'test_site');
define('FIXTURES', __DIR__ . DS . 'Fixtures');
define('TEST_BUILD', sys_get_temp_dir());

$mtimes = [
    '2013' . DS . '05' . DS . 'most-recent.md' => 'Mon, 20 May 2013 15:32:58 +0000',
    '2013' . DS . '05' . DS . 'my-may-post.md' => 'Sat, 18 May 2013 17:20:00 +0000',
    '2012' . DS . '01' . DS . 'post-markdown.md' => 'Fri, 20 Jan 2012 01:50:09 +0000',
    '2012' . DS . '01' . DS . 'post.html' => 'Mon, 16 Jan 2012 15:30:42 +0000',
    '2012' . DS . '02' . DS . 'another-post.md' => 'Thu, 16 Feb 2012 15:30:42 +0000',
];
// Reset modified times for all test_app views so they are predictable in tests
foreach ($mtimes as $postFilePath => $mtime) {
    touch(TEST_APP . DS . 'views' . DS . $postFilePath, strtotime($mtime));
}