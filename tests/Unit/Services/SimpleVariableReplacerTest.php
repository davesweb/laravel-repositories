<?php

namespace Davesweb\Repositories\Tests\Unit\Services;

use Davesweb\Repositories\Services\SimpleVariableReplacer;
use Davesweb\Repositories\Tests\TestCase;

class SimpleVariableReplacerTest extends TestCase
{
    /**
     * @var SimpleVariableReplacer
     */
    private $service;

    /**
     * @var string
     */
    private $text = 'This is a {cool} string {with} {some} variables.';

    public function setUp()
    {
        parent::setUp();

        $this->service = new SimpleVariableReplacer();
    }

    public function test_variables_get_replaced()
    {
        $variables = [
            'cool' => 'hot',
            'with' => 'replacement with spaces',
            'some' => 'all',
        ];

        $expected = 'This is a hot string replacement with spaces all variables.';

        $actual = $this->service->replace($this->text, $variables);

        $this->assertEquals($expected, $actual);
    }

    public function test_non_defined_variables_do_nothing()
    {
        $variables = [
            'cool'  => 'hot',
            'with'  => 'replacement with spaces',
            'some'  => 'all',
            'other' => 'should not matter',
            'more'  => 'should not matter either',
        ];

        $expected = 'This is a hot string replacement with spaces all variables.';

        $actual = $this->service->replace($this->text, $variables);

        $this->assertEquals($expected, $actual);
    }

    public function test_non_provided_variables_do_not_get_replaced()
    {
        $variables = [
            'with' => 'replacement with spaces',
        ];

        $expected = 'This is a {cool} string replacement with spaces {some} variables.';

        $actual = $this->service->replace($this->text, $variables);

        $this->assertEquals($expected, $actual);
    }
}
