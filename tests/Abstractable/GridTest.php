<?php namespace Orchestra\Html\Abstractable\TestCase;

use Illuminate\Container\Container;
use Orchestra\Html\Abstractable\Grid;

class GridTest extends \PHPUnit_Framework_TestCase
{
    public function testMetaData()
    {
        $app  = new Container;
        $stub = new GridStub($app);

        $refl = new \ReflectionObject($stub);
        $meta = $refl->getProperty('meta');
        $meta->setAccessible(true);

        $this->assertEquals(array(), $meta->getValue($stub));

        $stub->set('foo.bar', 'foobar');
        $stub->set('foo.hello', 'world');

        $this->assertEquals(array('foo' => array('bar' => 'foobar', 'hello' => 'world')), $meta->getValue($stub));
        $this->assertEquals('foobar', $stub->get('foo.bar'));
        $this->assertNull($stub->get('foobar'));

        $stub->forget('foo.bar');


        $this->assertEquals(array('foo' => array('hello' => 'world')), $meta->getValue($stub));
        $this->assertNull($stub->get('foo.bar'));
    }
}

class GridStub extends Grid
{
    /**
     * Load grid configuration.
     *
     * @return void
     */
    protected function initiate()
    {

    }
}
