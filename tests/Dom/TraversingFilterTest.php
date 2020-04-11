<?php

namespace DQTests\Dom;

use DQ\DomQuery;
use DQTests\TestCaseBase;
use Tightenco\Collect\Support\Collection;

class TraversingFilterTest extends TestCaseBase
{
    /*
     * Test is
     */
    public function testIs()
    {
        $dom = new DomQuery('<a>hai</a> <a></a> <a id="mmm"></a> <a class="x"></a> <a class="xpp"></a>');
        $this->assertTrue($dom[2]->is('#mmm'));
        $this->assertTrue($dom[2]->next()->is('.x'));
        $this->assertTrue($dom[0]->is($dom->xpathQuery('//a')));
        $this->assertTrue($dom[0]->is($dom[0]));
        $this->assertTrue($dom[0]->is(function ($node) {
            return $node->tagName == 'a';
        }));
        $this->assertFalse($dom[0]->is($dom[1]));
        $this->assertFalse($dom[0]->is($dom->find('a:last-child')));
    }

    /*
     *
     */
    public function testHas()
    {
        $dom = new DomQuery('<a>hai</a> <a></a> <a id="mmm"></a> <a class="x"><span id="here"></span></a> <a class="xpp"></a>');

        $this->assertEquals('<a class="x"><span id="here"></span></a>', (string) $dom->find('a')->has('#here'));
        $this->assertEquals('<a class="x"><span id="here"></span></a>', (string) $dom->find('a')->has($dom->find('#here')));
    }

    /*
     * Test filter on selection result
     */
    public function testFilter()
    {
        $dom = new DomQuery('<a>hai</a> <a></a> <a id="mmm"></a> <a class="x"></a> <a class="xpp"></a>');
        $selection = $dom->find('a');
        $this->assertEquals(5, $selection->length);
        $this->assertEquals(5, $selection->filter('a')->length);
        $this->assertEquals(5, $selection->filter(function ($node) {
            return $node->tagName == 'a';
        })->length);
        $this->assertEquals(1, $selection->filter('#mmm')->length);
        $this->assertEquals(1, $selection->filter($dom->getDOMDocument()->getElementById('mmm'))->length);
        $this->assertEquals(1, $selection->filter('a')->filter('.xpp')->length);
        $this->assertEquals(3, $selection->filter('a[class], #mmm')->length);
        $this->assertEquals(3, $selection->filter(':even')->length);
        $this->assertEquals('<a class="xpp"></a>', (string) $selection->filter($dom->find('a.xpp')));
    }

    /*
     * Test not filter on selection result
     */
    public function testNot()
    {
        $dom = new DomQuery('<a>hai</a> <a></a> <a id="mmm"></a> <a class="x"></a> <a class="xpp"></a>');
        $selection = $dom->find('a');
        $this->assertEquals(5, $selection->length);
        $this->assertEquals(5, $selection->not('p')->length);
        $this->assertEquals(0, $selection->not('a')->length);
        $this->assertEquals(4, $selection->not('#mmm')->length);
        $this->assertEquals(3, $selection->not('#mmm')->not('.xpp')->length);
        $this->assertEquals(2, $selection->not('a[class], #mmm')->length);
        $this->assertEquals(2, $selection->not(':even')->length);
        $this->assertEquals(2, $selection->not(function ($node) {
            return $node->hasAttributes();
        })->length);
        $this->assertEquals(4, $selection->not($dom->getDOMDocument()->getElementById('mmm'))->length);
        $inner = (string) $selection->not($dom->find('a:first-child, a:last-child'));
        $this->assertEquals('<a></a><a id="mmm"></a><a class="x"></a>', $inner);
    }

    /*
     * Test first last, with and without filter selector
     */
    public function testFirstLast()
    {
        $dom = new DomQuery('<a>1</a> <a>2</a> <a>3</a>');
        $links = $dom->children('a');

        $this->assertEquals(3, $links->length);

        $this->assertEquals(null, $links->first()->next('p')->text());
        $this->assertEquals(null, $links->last()->prev('p')->text());

        $this->assertEquals('2', $links->first()->next('a')->text());
        $this->assertEquals('2', $links->last()->prev('a')->text());

        $this->assertEquals(0, $links->first('p')->length);
        $this->assertEquals(0, $links->last('p')->length);

        $this->assertEquals(1, $links->first('a')->length);
        $this->assertEquals(1, $links->last('a')->length);
    }

    /*
     * Test slice
     */
    public function testSlice()
    {
        $dom = new DomQuery('<a>1</a><a>2</a><a>3</a><a>4</a><a>5</a><a>6</a>');
        $this->assertEquals('<a>1</a><a>2</a>', (string) $dom->find('a')->slice(0, 2));
        $this->assertEquals('<a>3</a><a>4</a><a>5</a><a>6</a>', (string) $dom->find('a')->slice(2));
        $this->assertEquals('<a>6</a>', (string) $dom->find('a')->slice(-1));
        $this->assertEquals('<a>5</a>', (string) $dom->find('a')->slice(-2, -1));
    }

    /*
     * Test eq
     */
    public function testEq()
    {
        $dom = new DomQuery('<a>1</a><a>2</a><a>3</a><a>4</a><a>5</a><a>6</a>');
        $this->assertEquals('<a>1</a>', (string) $dom->find('a')->eq(0));
        $this->assertEquals('<a>2</a>', (string) $dom->find('a')->eq(1));
        $this->assertEquals('<a>6</a>', (string) $dom->find('a')->eq(-1));
        $this->assertEquals('<a>5</a>', (string) $dom->find('a')->eq(-2));
    }

    /*
     * Test map (with returning string and array)
     */
    public function testMap()
    {
        $dom = new DomQuery('<p> <a>1</a> <a>2,3</a> <a>4</a> <span></span> </p>');

        $map = $dom->find('p>*')->map(function (DomQuery $dq) {
            $text = $dq->text();
            if ($text !== '') {
                if (strpos($text, ',') !== false) {
                    return explode(',', $text);
                } else {
                    return $text;
                }
            }
            return null;
        });

        $this->assertInstanceOf(Collection::class, $map);
        $this->assertEquals(['1', ['2', '3'], '4'], $map->toArray());
    }

    /**
     * Test foreach
     */
    public function testForeach()
    {
        $dom = new DomQuery('<p> <a>1</a> <a>2,3</a> <a>4</a> <span></span> </p>');

        $text = [];
        foreach ($dom->find('a') as $key => $node) {
            /* @var \DQ\DomQuery $node */
            $text[] = $node->text();

            $this->assertInstanceOf(DomQuery::class, $node);
        }

        $this->assertCount(3, $text);
        $this->assertEquals(['1', '2,3', '4'], $text);

    }

    public function testEach()
    {
        $dom = new DomQuery('<p> <a>1</a> <a>2,3</a> <a>4</a> <span></span> </p>');

        $dom->find('a')->each(function (DomQuery $query, int $index) {

            $this->assertInstanceOf(DomQuery::class, $query);

            if ($index === 1) {
                return false;
            }

            $query->text('new text');

        });

        $texts = $dom->find('a')->texts();

        $this->assertEquals(['new text', '2,3', '4'], $texts);

    }
}
