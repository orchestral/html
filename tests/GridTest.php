<?php

namespace Orchestra\Html\Tests;

use Illuminate\Container\Container;
use Orchestra\Html\Grid;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    /** @test */
    public function it_can_manipulate_meta_data()
    {
        $app = new Container();
        $stub = new GridStub($app, []);

        $refl = new \ReflectionObject($stub);
        $meta = $refl->getProperty('meta');
        $meta->setAccessible(true);

        $this->assertEquals([], $meta->getValue($stub));

        $stub->set('foo.bar', 'foobar');
        $stub->set('foo.hello', 'world');

        $this->assertEquals(['foo' => ['bar' => 'foobar', 'hello' => 'world']], $meta->getValue($stub));
        $this->assertEquals('foobar', $stub->get('foo.bar'));
        $this->assertNull($stub->get('foobar'));

        $stub->forget('foo.bar');

        $this->assertEquals(['foo' => ['hello' => 'world']], $meta->getValue($stub));
        $this->assertNull($stub->get('foo.bar'));
    }
}

class GridStub extends Grid
{
    public function find(string $name)
    {
        //
    }
}
