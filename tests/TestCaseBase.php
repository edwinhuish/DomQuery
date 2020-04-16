<?php

namespace DQ\Tests;

use PHPUnit\Framework\TestCase;

class TestCaseBase extends TestCase
{
    public function getSnippet($name)
    {
        return file_get_contents(__DIR__.'/assets/'.$name.'.html');
    }
}
