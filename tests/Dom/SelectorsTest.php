<?php

namespace DQTests\Dom;

use DQ\DomQuery;
use DQTests\TestCaseBase;

class SelectorsTest extends TestCaseBase
{

    public function testFindNthChildSelector()
    {
        $dom = new DomQuery('<ul>
            <li>list item 1</li>
            <li id="item2">list item 2</li>
            <li id="item3" _="x" i- i2-="">list item 3</li>
            <li>list item 4</li>
            <li class="item-item">list item 5</li>
            <li class="item item6" rel="x">list item 6</li>
            <li class="item"></li>
            <meta http-equiv="Content-Type" content="text/html">
        </ul>');

        $text3 = $dom->find('li:nth-child(3)')->text;
        $text5 = $dom->find('li:eq(4)')->text;
        $text6 = $dom->find('li:eq(-2)')->text;
        $text5_1 = $dom->find('li:nth-child(-3)')->text;

        $this->assertEquals('list item 3', $text3);
        $this->assertEquals('list item 5', $text5);
        $this->assertEquals('list item 5', $text5_1);
        $this->assertEquals('list item 6', $text6);

    }

    /*
     * Test select by tagname selector
     */
    public function testElementTagnameSelector()
    {
        $dom = new DomQuery('<a>1</a><b>2</b><i>3</i>');
        $this->assertEquals('2', $dom->find('b')->text());
    }

    /*
     * Test select by tagname selector with xml
     */
    public function testElementTagnameSelectorWithXml()
    {
        $dom = new DomQuery('<?xml version="1.0" encoding="UTF-8"?><root>'
            .'<link>Hi</link><b:link>Hi2</b:link></root>');
        $this->assertEquals('Hi', $dom->find('link')->text());
        $this->assertEquals(1, $dom->find('link')->length);
    }

    /*
    * Test select by tagname selector with xml name space
    */
    public function testElementTagnameSelectorWithXmlNameSpace()
    {
        $dom = new DomQuery('<?xml version="1.0" encoding="UTF-8"?>'
            .'<root xmlns:h="http://www.w3.org/TR/html4/" xmlns:f="https://www.w3schools.com/furniture">'
            .'<f:link>Hi</f:link><b:link>Hi2</b:link><h:link>Hi3</h:link></root>');
        $this->assertEquals('Hi', $dom->find('f\\:link')->text());
        $this->assertEquals(1, $dom->find('f\\:link')->length);
        $this->assertEquals('Hi3', $dom->find('h\\:link')->text());
    }

    /*
     * Test wildcard / all selector
     */
    public function testWildcardSelector()
    {
        $dom = new DomQuery('<a>1</a><b>2</b>');
        $this->assertEquals(2, $dom->find('*')->length);
    }

    /*
     * Test children selector
     */
    public function testSelectChildrenSelector()
    {
        $dom = new DomQuery('<div><a>1</a><b>2</b></div><a>3</a>');
        $this->assertEquals('1', $dom->find('div > a')->text());
        $this->assertEquals(1, $dom->find('div > a')->length);
    }

    /*
     * Test id selector
     */
    public function testIdSelector()
    {
        $dom = new DomQuery('<div><a>1</a><b>2</b></div><a id="here">3</a>');
        $this->assertEquals('3', $dom->find('#here')->text());
        $this->assertEquals(1, $dom->find('#here')->length);
    }

    /*
     * Test emoji as id selector
     */
    public function testEmojiIdSelector()
    {
        $dom = new DomQuery('<div><a>1</a><b id="🐝">2</b></div><a >3</a>');
        $this->assertEquals('2', $dom->find('#🐝')->text());
        $this->assertEquals(1, $dom->find('#🐝')->length);
    }

    /*
     * Test id selector with meta character
     */
    public function testIdSelectorWithMetaCharacter()
    {
        $dom = new DomQuery('<div><a>1</a><b>2</b></div><a id="here.here">3</a>');
        $this->assertEquals('3', $dom->find('#here\\.here')->text());
    }

    /*
     * Test descendant selector
     */
    public function testDescendantSelector()
    {
        $dom = new DomQuery('<div><a>1</a><b>2</b></div><a id="here">3</a><p><a>4</a></p>');
        $this->assertEquals('2', $dom->find('div > b')->text());
        $this->assertEquals(1, $dom->find('div > b')->length);
        $this->assertEquals(0, $dom->find('a > b')->length);
        $this->assertEquals(1, $dom->find('> a')->length);
        $this->assertEquals(2, $dom->find('* > a')->length);
    }

    /*
     * Test next adjacent selector
     */
    public function testNextAdjacentSelector()
    {
        $dom = new DomQuery('<a>x</a><b>a</b><a>1</a><a>2</a><a>3</a>');
        $this->assertEquals(1, $dom->find('b + a')->length);
        $this->assertEquals('1', $dom->find('b + a')->text());
        $this->assertEquals(1, $dom->find('a + b')->length);
        $this->assertEquals('2', $dom->find('a + a')->text());
        $this->assertEquals(2, $dom->find('a + a')->length);
    }

    /*
     * Test next adjacent selector using class
     */
    public function testNextAdjacentSelectorUsingClass()
    {
        $dom = new DomQuery('<a class="app">x</a><b>a</b><a class="app">1</a><a class="app">2</a><a class="app">3</a>');
        $this->assertEquals('2', $dom->find('.app + a.app')->text());
        $this->assertEquals('2', $dom->find('a + .app')->text());
        $this->assertEquals('3', $dom->find('a + .app')->last()->text());
        $this->assertEquals(2, $dom->find('.app + .app')->length);
        $this->assertEquals(2, $dom->find('a.app + a.app')->length);
    }

    /*
     * Test next siblings selector
     */
    public function testNextSiblingsSelector()
    {
        $dom = new DomQuery('<b>a</b><a>1</a><a>2</a><a>3</a>');
        $this->assertEquals(3, $dom->find('b ~ a')->length);
        $this->assertEquals('3', $dom->find('b ~ a')->last()->text());
        $this->assertEquals(0, $dom->find('a ~ b')->length);
        $this->assertEquals(2, $dom->find('a ~ a')->length);
    }

    /*
     * Test multiple selectors
     */
    public function testMultipleSelectors()
    {
        $dom = new DomQuery('<div><a>1</a><b>2</b></div><a id="here">3</a><p><a>4</a></p>');
        $this->assertEquals(2, $dom->find('#here, div > b')->length);
    }

    /*
     * Test class selector
     */
    public function testClassSelector()
    {
        $dom = new DomQuery('<div><a class="monkey moon">1</a><b>2</b></div><a class="monkey">3</a>');
        $this->assertEquals(2, $dom->find('a.monkey')->length);
        $this->assertEquals(1, $dom->find('.moon')->length);
        $this->assertEquals(1, $dom->find('a.moon')->length);
    }

    /*
     * Test class selector with uppercase
     */
    public function testClassSelectorWithUppercase()
    {
        $dom = new DomQuery('<div><a class="monkey">1</a><b>2</b></div><a class="Monkey">3</a>');
        $this->assertEquals('3', $dom->find('.Monkey')->text());
        $this->assertEquals(1, $dom->find('.Monkey')->length);
        $this->assertEquals(1, $dom->find('a.Monkey')->length);
    }

    /*
     * Test class selector with underscore
     */
    public function testClassSelectorWithUnderscore()
    {
        $dom = new DomQuery('<div><a class="monkey_moon">1</a><b>2</b></div><a class="monkey-moon">3</a>');
        $this->assertEquals('1', $dom->find('.monkey_moon')->text());
        $this->assertEquals('3', $dom->find('.monkey-moon')->text());
    }

    /*
     * Test emoji as class selector
     */
    public function testEmojiClassSelector()
    {
        $dom = new DomQuery('<div><a>1</a><b class="🐝">2</b></div><a>3</a>');
        $this->assertEquals('2', $dom->find('.🐝')->text());
        $this->assertEquals(1, $dom->find('b.🐝')->length);
    }

    /*
     * Test not filter selector
     */
    public function testNotFilterSelector()
    {
        $dom = new DomQuery('<a>1</a><a class="monkey">2</a><a id="some-monkey">3</a>');

        $this->assertEquals(2, $dom->find('a:not(.monkey)')->length);
        $this->assertEquals(2, $dom->find('a:not([id])')->length);
        $this->assertEquals(1, $dom->find('a:not([id],[class])')->length);
        $this->assertEquals(2, $dom->find('a:not(#some-monkey)')->length);
        $this->assertEquals(1, $dom->find('a:not(#some-monkey, .monkey)')->length);
        $this->assertEquals(1, $dom->find('a:not(.monkey,#some-monkey)')->length);
        $this->assertEquals(3, $dom->find('a:not(b)')->length);
    }

    /*
     * Test has filter selector
     */
    public function testHasFilterSelector()
    {
        $dom = new DomQuery('<ul>
            <li id="item2">list item 1<a></a></li>
            <li id="item2">list item 2</li>
            <li id="item3"><b></b>list item 3</li>
            <li >list item 4</li>
            <li class="item-item"><span id="anx">x</span></li>
            <li class="item item6">list item 6</li>
        </ul>');

        $this->assertEquals('item2', $dom->find('li:has(a)')->id);
        $this->assertEquals(1, $dom->find('li:has(b)')->length);
        $this->assertEquals('item-item', $dom->find('li:has(span#anx)')->class);
        $this->assertEquals('item-item', $dom->find('li:has(*#anx)')->class);
        $this->assertEquals(1, $dom->find('ul:has(li.item)')->length);
        $this->assertEquals(1, $dom->find('ul:has(span)')->length);
        $this->assertEquals(1, $dom->find('ul:has(li span)')->length);
        $this->assertEquals(0, $dom->find('ul:has(> span)')->length);
    }

    /*
     * Test contains selector
     */
    public function testContainsSelector()
    {
        $dom = new DomQuery('<a>no</a><a id="ok">yes it is!</a><a>no</a>');
        $this->assertEquals('ok', $dom->find('a:contains(yes)')->attr('id'));
    }

    /*
     * Test attribute selector
     */
    public function testAttributeSelector()
    {
        $dom = new DomQuery('<ul>
            <li>list item 1</li>
            <li id="item2">list item 2</li>
            <li id="item3" _="x" i- i2-="">list item 3</li>
            <li>list item 4</li>
            <li class="item-item">list item 5</li>
            <li class="item item6" rel="x">list item 6</li>
            <li class="item"></li>
            <meta http-equiv="Content-Type" content="text/html">
        </ul>');

        $this->assertEquals(2, $dom->find('li[id]')->length);
        $this->assertEquals(1, $dom->find('li[_]')->length);
        $this->assertEquals(1, $dom->find('li[_=x]')->length);
        $this->assertEquals(1, $dom->find('li[i-]')->length);
        $this->assertEquals(1, $dom->find('li[i2-]')->length);
        $this->assertEquals(2, $dom->find('li[id^=\'item\']')->length);
        $this->assertEquals(2, $dom->find('li[id*=\'tem\']')->length);
        $this->assertEquals('list item 3', $dom->find('li[id$=\'tem3\']')->text());
        $this->assertEquals(0, $dom->find('li[id$=\'item\']')->length);
        $this->assertEquals(0, $dom->find('li[id|=\'item\']')->length);
        $this->assertEquals('list item 3', $dom->find('li[id=\'item3\']')->text());
        $this->assertEquals('list item 6', $dom->find('li[class~=\'item6\']')->text());
        $this->assertEquals('list item 6', $dom->find('li[class][rel]')->text());
        $this->assertEquals(2, $dom->find('li[class~=\'item\']')->length);
        $this->assertEquals(1, $dom->find('li[class~=\'item\'][class~=\'item6\']')->length);
        $this->assertEquals(0, $dom->find('li[class~=\'item\'][id~=\'item\']')->length);
        $this->assertEquals(3, $dom->find('li[class*=\'item\']')->length);
        $this->assertEquals(2, $dom->find('li[class|=\'item\']')->length);
        $this->assertEquals('text/html', (string) $dom->find('meta[http-equiv^="Content"]')->attr('content'));
    }

    /*
     * Test malformed attribute selector
     */
    public function testMalformedAttributeSelector()
    {
        $this->expectException(\Exception::class);
        $dom = new DomQuery('<div></div>');
        $dom->find('#id[-]');
    }

    /*
     * Test odd even pseudo selector
     * @note :even and :odd use 0-based indexing, so even (0, 2, 4) becomes item (1, 3, 5)
     */
    public function testOddEvenPseudoSelector()
    {
        $dom = new DomQuery('<ul>
            <li>list item 1</li>
            <li>list item 2</li>
            <li>list item 3</li>
            <li>list item 4</li>
            <li>list item 5</li>
            <li>list item 6</li>
        </ul>');

        $this->assertEquals(3, $dom->find('li')->filter(':even')->length); // 1 3 5
        $this->assertEquals('list item 5', $dom->find('li')->filter(':even')->last()->text());

        $this->assertEquals(3, $dom->find('li')->filter(':odd')->length); // 2 4 6
        $this->assertEquals('list item 6', $dom->find('li')->filter(':odd')->last()->text());
    }

    /*
     * Test first child pseudo selector
     */
    public function testFirstChildPseudoSelector()
    {
        $dom = new DomQuery('<div>
        <div id="list-a">
            <span>a</span>
            <li>nope</li>
        </div>
        <ul id="list-b">
            <li>item 1</li>
            <li>item 2</li>
            <li>item 3</li>
        </ul></div>');

        $this->assertEquals(0, $dom->find('ul:first-child')->length);
        $this->assertEquals(1, $dom->find('div > div:first-child')->length);
        $this->assertEquals('item 1', $dom->find('li:first-child')->text());
    }

    /*
     * Test last child pseudo selector
     */
    public function testLastChildPseudoSelector()
    {
        $dom = new DomQuery('<section>
        <div id="list-a">
            <li>nope</li>
            <span>a</span>
        </div>
        <ul id="list-b">
            <li>item 1</li>
            <li>item 2</li>
            <li>item 3</li>
        </ul></section>');

        $this->assertEquals(0, $dom->find('div:last-child')->length);
        $this->assertEquals('item 3', $dom->find('li:last-child')->text());
        $this->assertTrue($dom->find('ul')->is(':last-child'));
    }

    /*
     * Test only child pseudo selector
     */
    public function testOnlyChildPseudoSelector()
    {
        $dom = new DomQuery('<section>
        <ul id="list-b">
            <li>nope</li>
            <span>nope</span>
        </ul>
        <ul id="list-b">
            <li>yep</li>
        </ul></section>');

        $this->assertEquals(0, $dom->find('span:only-child')->length);
        $this->assertEquals(0, $dom->find('ul:only-child')->length);
        $this->assertTrue($dom->is(':only-child'));
        $this->assertEquals('yep', $dom->find('li:only-child')->text());
    }

    /*
     * Test parent pseudo selector
     */
    public function testParentPseudoSelector()
    {
        $dom = new DomQuery('<section>
        <ul id="list-b">
            <li>nope</li>
            <li><span>yep</span></li>
            <li>nope</li>
        </ul></section>');

        $this->assertEquals('yep', $dom->find('li:parent')->text());
        $this->assertEquals(1, $dom->find('li:parent')->length);
        $this->assertEquals(2, $dom->find('li:not(:parent)')->length);
    }

    /*
     * Test empty pseudo selector
     */
    public function testEmptyPseudoSelector()
    {
        $dom = new DomQuery('<ul id="list-b">
            <li id="nope">nope</li>
            <li id="yep"></li>
            <li id="nope"><span></span></li>
        </ul>');

        $this->assertEquals('yep', $dom->find('li:empty')->attr('id'));
        $this->assertEquals(1, $dom->find('li:empty')->length);
    }

    /*
     * Test empty pseudo selector
     */
    public function testNotEmptyPseudoSelector()
    {
        $dom = new DomQuery('<ul id="list-b">
            <li id="nope"></li>
            <li id="yep">hi</li>
            <li id="yep2"><span></span></li>
        </ul>');

        $this->assertEquals('yep', $dom->find('li:not-empty')->attr('id'));
        $this->assertEquals('yep2', $dom->find('li:not-empty')->last()->attr('id'));
        $this->assertEquals(2, $dom->find('li:not-empty')->length);
    }

    /*
     * Test header pseudo selector
     */
    public function testHeaderPseudoSelector()
    {
        $dom = new DomQuery('<div>
            <span>nope</span>
            <h1>yep</h1>
            <h6>yep2</h6>
        </div>');

        $this->assertEquals('yep', $dom->find(':header')->text());
        $this->assertEquals('yep2', $dom->find(':header')->last()->text());
        $this->assertEquals('nope', $dom->find('div > *:not(:header)')->text());
    }

    /*
     * Test first and last pseudo selector
     */
    public function testFirstAndLastPseudoSelector()
    {
        $dom = new DomQuery('<div>
            <span>yep</span>
            <h1>nope</h1>
            <h6>yep2</h6>
        </div>');

        $this->assertEquals('yep', $dom->find('div > *:first')->text());
        $this->assertEquals('yep2', $dom->find('div > *:last')->text());
    }

    /*
     * Test root pseudo selector
     */
    public function testRootPseudoSelector()
    {
        $dom = new DomQuery('<div id="yep">
            <span>nope</span>
            <h1>nope</h1>
            <h6>nope</h6>
        </div>');

        $this->assertEquals('yep', $dom->find(':root')->attr('id'));
        $this->assertEquals(1, $dom->find(':root')->length);
    }

}
