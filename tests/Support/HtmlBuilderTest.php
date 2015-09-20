<?php namespace Orchestra\Html\Support\TestCase;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Routing\RouteCollection;
use Orchestra\Html\Support\HtmlBuilder;

class HtmlBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Orchestra\Html\Support\HtmlBuilder
     */
    protected $html;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $generator = new UrlGenerator(new RouteCollection(), Request::create('/foo', 'GET'));
        $this->html = new HtmlBuilder($generator);
    }

    public function testDl()
    {
        $list = [
            'foo' => 'bar',
            'bing' => 'baz',
        ];

        $attributes = ['class' => 'example'];

        $result = $this->html->dl($list, $attributes);

        $this->assertEquals('<dl class="example"><dt>foo</dt><dd>bar</dd><dt>bing</dt><dd>baz</dd></dl>', $result);
    }

    public function testMeta()
    {
        $result = $this->html->meta('description', 'Lorem ipsum dolor sit amet.');

        $this->assertEquals('<meta name="description" content="Lorem ipsum dolor sit amet.">'.PHP_EOL, $result);
    }

    public function testMetaOpenGraph()
    {
        $result = $this->html->meta(null, 'website', ['property' => 'og:type']);

        $this->assertEquals('<meta content="website" property="og:type">'.PHP_EOL, $result);
    }
}
