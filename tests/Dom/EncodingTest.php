<?php

namespace DQ\Tests\Dom;

use DQ\DomQuery;
use DQ\Tests\TestCaseBase;

class EncodingTest extends TestCaseBase
{
    /*
     * Test get attribute value
     */
    public function testGB2312()
    {
        $html = '<p>这个是一个测试！</p>';
        $html = mb_convert_encoding($html, 'GB2312', 'UTF-8');

        DomQuery::$autoEncoding = true;

        $dom = DomQuery::create($html);

        $this->assertEquals('<p>这个是一个测试！</p>', $dom->getOuterHtml());

    }

}
