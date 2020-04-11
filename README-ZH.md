# DomQuery 

[English](README.md) | [中文说明](README-ZH.md)

DomQuery是一个PHP库，使您可以轻松地遍历和修改DOM（HTML / XML）。 作为库，它的目标是提供对 [PHP DOMDocument 类](http://php.net/manual/en/book.dom.php) 的 "仿jQuery" 的访问方式

## 安装

安装最新版本

```bash
composer require edwinhuish/domquery
```

## 基本使用方式

### 读取 attributes 和 properties：

``` php
$dom = new DomQuery('<div><h1 class="title">Hello</h1></div>');

echo $dom->find('h1')->text(); // output: Hello
echo $dom->find('div')->prop('outerHTML'); // output: <div><h1 class="title">Hello</h1></div>
echo $dom->find('div')->html(); // output: <h1 class="title">Hello</h1>
echo $dom->find('div > h1')->class; // output: title
echo $dom->find('div > h1')->attr('class'); // output: title
echo $dom->find('div > h1')->prop('tagName'); // output: h1
echo $dom->find('div')->children('h1')->prop('tagName'); // output: h1
echo (string) $dom->find('div > h1'); // output: <h1 class="title">Hello</h1>
echo count($dom->find('div, h1')); // output: 2
```

### 遍历节点 （结果集）：

``` php
$dom = new DomQuery('<a>1</a> <a>2</a> <a>3</a>');
$links = $dom->children('a');

// foreach 遍历
$texts = [];
foreach($links as $key => $dq) { // $dq 是 DomQuery 对象
    $texts[] = $dq->text();
}
print_r($texts); // array('1','2','3')

// map 遍历
$result = $links->map(function(DomQuery $dq, int $idx){
    return $dq->text();
});
// map 函数返回 Collection 对象
print_r($result->toArray()); // array('1','2','3')

// each 遍历, 与 Collection 的 each 函数类似， 返回 false 则中断循环。
$links->each(function(DomQuery $dq, int $idx){
    if($idx === 1){
        return false;
    }
    $dq->text('changed');
});
print_r($links->texts()); // array('changed', '2', '3')

echo $links->text(); // output 1，仅返回第一个子节点的text，如需要返回多个，请使用 texts() 或者 foreach, each, map 函数
echo $links[0]->text(); // output 1
echo $links->last()->text(); // output 3
echo $links->first()->next()->text(); // output 2
echo $links->last()->prev()->text(); // output 2
echo $links->get(0)->textContent; // output 1
echo $links->get(-1)->textContent; // output 3
```

### 工厂模式 （创建实例）：

```php
DomQuery::create('<a title="hello"></a>')->attr('title') // hello
```

## Jquery 可用函数

#### 遍历 > 树遍历

- `.find( selector )`
- `.children( [selector] )`
- `.parent( [selector] )`
- `.closest( [selector] )`
- `.next( [selector] )`
- `.prev( [selector] )`
- `.nextAll( [selector] )`
- `.prevAll( [selector] )`
- `.siblings( [selector] )`

#### 遍历 > 杂项

- `.contents()` 获取所有子节点，包含空格或者其他文本亦视为子节点
- `.add( selector, [context] )` 如果context为空，则从现有document中查找对应selector加入集合，否则从context中查找对应selector加入集合

 #### Traversing > Filtering

- `.is( selector )`
- `.filter ( selector )` 筛选符合selector的节点, selector 可以是 callable
- `.not( selector )` 将selector对应的节点从集合中去除
- `.has( selector )` 子节点内含有对应selector的节点集合
- `.first( [selector] )`
- `.last( [selector] )`
- `.slice( [offset] [, length])` 类似 [PHP 的 array_slice](http://php.net/manual/zh/function.array-slice.php), 非 js/jquery
- `.eq( index )`
- `.map( callable(elm,i) )`

<sub>\* __[selector]__ 可以是 css selector 或者 DomQuery|DOMNodeList|DOMNode 的对象实例</sub>

 #### 操作 > DOM 插入 & 删除

- `.text( [text] )`
- `.html( [html_string] )`
- `.append( [content],... )`
- `.prepend( [content],... )`
- `.after( [content],... )`
- `.before( [content],... )`
- `.appendTo( [target] )`
- `.prependTo( [target] )`
- `.replaceWith( [content] )`
- `.wrap( [content] )`
- `.wrapAll( [content] )`
- `.wrapInner( [content] )`
- `.remove( [selector] )`
- `.unwrap()`
- `.first()`
- `.last()`
- `.gt( int $index )`
- `.lt( int $index )`

<sub>\* __[content]__ 可以是 html 或者 DomQuery|DOMNodeList|DOMNode 对象实例</sub>

 #### 属性 | 操作

- `.attr( name [, val] )`
- `.prop( name [, val] )`
- `.css( name [, val] )`
- `.removeAttr( name )`
- `.addClass( name )`
- `.hasClass( name )`
- `.toggleClass ( name )`
- `.removeClass( [name] )`

<sub>\* addClass, removeClass, toggleClass 和 removeAttr 可以传入 数组 或者空格间隔的 __names__</sub>

 #### 其他 > DOM元素方法 | 遍历 | 存储

- `.get( index )`
- `.each ( callable(elm,i) )`
- `.data ( key [, val] )`
- `.removeData ( [name] )`
- `.index ( [selector] )`
- `.toArray()`
- `.clone()`

## 支持的 selectors

- `.class`
- `#foo`
- `parent > child`
- `foo, bar`  多个 selectors
- `prev + next` 例如：`div + p` 选择紧接在 `<div>` 元素之后的所有 `<p>` 元素。
- `prev ~ siblings` 例如：`p ~ ul` 选择前面有 `<p>` 元素的每个 `<ul>` 元素。
- `*` 所有节点
- `[name="foo"]` 属性名称为 foo
- `[name*="foo"]` 例如：`a[src*="abc"]` 选择其 `src` 属性中包含 `abc` 子串的每个 `<a>` 元素。
- `[name~="foo"]` 例如：`[title~=flower]` 选择 `title` 属性包含单词 "flower" 的所有元素。
- `[name^="foo"]` 例如：`a[src^="https"]` 选择其 `src` 属性值以 "https" 开头的每个 `<a>` 元素。
- `[name$="foo"]` 例如：`a[src$=".pdf"]` 选择其 `src` 属性以 ".pdf" 结尾的所有 `<a>` 元素。	
- `[name|="foo"]` 例如：`[lang|=en]` 选择 lang 属性值以 "en" 开头的所有元素。

### 伪类

- `:empty`
- `:even`
- `:odd`
- `:first-child`
- `:last-child`
- `:only-child`
- `:parent` 必须至少有一个子元素
- `:first`
- `:last`
- `:header` 选择 h1, h2, h3 等.
- `:not(foo)` 选择非 selector foo 的元素集合
- `:has(foo)` 至少有一个子节点匹配 foo selector 的节点
- `:contains(foo)` 含有 foo text 的节点
- `:root` 文档的根元素。
- `:nth-child(n)`
- `:nth-child(even)`
- `:nth-child(odd)`
- `:nth-child(3n+8)`
- `:nth-child(2n+1)`
- `:nth-child(n+4)` 等同于 `:gt(2)`
- `:nth-child(-n+4)` 等同于 `:lt(4)`
- `:nth-child(3)`
- `:nth-child(-2)`
- `:nth-child(4n)`
- `:eq(0)`
- `:eq(-1)`
- `:lt(3)`
- `:gt(2)`

## 其他 （非 jQuery 函数）

- `findOrFail( selector )` 查找选择元素，如果找不到则抛出异常
- `loadContent(content, encoding='UTF-8')`  加载 html/xml 内容
- `xpath(xpath_query)` 使用xpath查找当前匹配元素集中每个元素的后代
- `getOuterHtml()` 得到集合所有元素的html (和 `(string) $dom`,  `$elm->prop('outerHTML')` 一致)

## XML 支持

- 如果找到 [XML 声明](https://wiki.jikexueyuan.com/project/xml/declaration.html)，XML 内容会自动加载为 [XML](http://php.net/manual/zh/domdocument.loadxml.php) （属性 `xml_mode` 将会设置为 `true` ）
- 同时会使保存（渲染）如 [XML](http://php.net/manual/zh/domdocument.savexml.php)。您可以将 `xml_mode` 设为 `false` 来避免这个状况。
- 为防止插入 `XML` 声明的内容，您可以将 `xml_mode` 设为 `false`，再用 `loadContent($content)` 函数进行加载。
- Namespaces 会自动注册。 （无须[手动注册](http://php.net/manual/zh/domxpath.registernamespace.php)）

在选择器中转义元字符以查找具有名称空间的元素：

```php
$dom->find('namespace\\:h1')->text();
```

## 关于

### 环境要求

- PHP 7.0 或者以上
- libxml 扩展 （默认开启）

### Fork from

- https://github.com/Rct567/DomQuery
