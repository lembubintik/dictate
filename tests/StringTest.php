<?php

use Mockery as m;
use Dictate\String\String;

class StringTest extends PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * Test the Str::length method.
     */
    public function testStringLengthIsCorrect()
    {
        $app = $this->getApplication();
        $str = new String($app);
        $this->assertEquals(6, $str->length('Taylor'));
        $this->assertEquals(5, $str->length('ラドクリフ'));
    }

    /**
     * Test the Str::lower method.
     */
    public function testStringCanBeConvertedToLowercase()
    {
        $app = $this->getApplication();
        $str = new String($app);
        $this->assertEquals('taylor', $str->lower('TAYLOR'));
        $this->assertEquals('άχιστη', $str->lower('ΆΧΙΣΤΗ'));
    }

    /**
     * Test the Str::upper method.
     */
    public function testStringCanBeConvertedToUppercase()
    {
        $app = $this->getApplication();
        $str = new String($app);
        $this->assertEquals('TAYLOR', $str->upper('taylor'));
        $this->assertEquals('ΆΧΙΣΤΗ', $str->upper('άχιστη'));
    }

    /**
     * Test the Str::title method.
     */
    public function testStringCanBeConvertedToTitleCase()
    {
        $app = $this->getApplication();
        $str = new String($app);
        $this->assertEquals('Taylor', $str->title('taylor'));
        $this->assertEquals('Άχιστη', $str->title('άχιστη'));
    }

    /**
     * Test the Str::limit method.
     */
    public function testStringCanBeLimitedByCharacters()
    {
        $app = $this->getApplication();
        $str = new String($app);
        $this->assertEquals('Tay...', $str->limit('Taylor', 3));
        $this->assertEquals('Taylor', $str->limit('Taylor', 6));
        $this->assertEquals('Tay___', $str->limit('Taylor', 3, '___'));
    }

    /**
     * Test the Str::words method.
     */
    public function testStringCanBeLimitedByWords()
    {
        $app = $this->getApplication();
        $str = new String($app);
        $this->assertEquals('Taylor...', $str->words('Taylor Otwell', 1));
        $this->assertEquals('Taylor___', $str->words('Taylor Otwell', 1, '___'));
        $this->assertEquals('Taylor Otwell', $str->words('Taylor Otwell', 3));
    }

    /**
     * Test the Str::plural and Str::singular methods.
     */
    public function testStringsCanBeSingularOrPlural()
    {
        $app = $this->getApplication();
        $app['config']->shouldReceive('get')->with('string::strings')->andReturn(array(
            'irregular' => array(),
            'singular' => array(),
            'uncountable' => array(
                'traffic'
            ),
            'plural' => array(
                '/s$/i' => "s",
                '/$/' => "s"
            ),
            'singular' => array(
                '/s$/i' => ""
            )
        ));
        $str = new String($app);
        $this->assertEquals('user', $str->singular('users'));
        $this->assertEquals('users', $str->plural('user'));
        $this->assertEquals('User', $str->singular('Users'));
        $this->assertEquals('Users', $str->plural('User'));
        $this->assertEquals('user', $str->plural('user', 1));
        $this->assertEquals('users', $str->plural('user', 2));
        $this->assertEquals('chassis', $str->plural('chassis', 2));
        $this->assertEquals('traffic', $str->plural('traffic', 2));
    }

    /**
     * Test the Str::slug method.
     */
    public function testStringsCanBeSlugged()
    {
        $app = $this->getApplication();
        $app['config']->shouldReceive('get')->with('string::ascii')->andReturn(array());
        $str = new String($app);
        $this->assertEquals('my-new-post', $str->slug('My nEw post!!!'));
        $this->assertEquals('my_new_post', $str->slug('My nEw post!!!', '_'));
    }

    /**
     * Test the Str::classify method.
     */
    public function testStringsCanBeClassified()
    {
        $app = $this->getApplication();
        $str = new String($app);
        $this->assertEquals('Something_Else', $str->classify('something.else'));
        $this->assertEquals('Something_Else', $str->classify('something_else'));
    }

    /**
     * Test the Str::random method.
     */
    public function testRandomStringsCanBeGenerated()
    {
        $app = $this->getApplication();
        $str = new String($app);
        $this->assertEquals(40, strlen($str->random(40)));
    }

    protected function getApplication()
    {
        $app = new Illuminate\Container\Container;
        $app['config'] = m::mock('stdClass');
        $app['config']->shouldReceive('get')->with('string::encoding')->andReturn('UTF8');

        return $app;
    }

}