<?php

namespace Diff\test;

use \PHPUnit\Framework\TestCase;

class DiffTest extends TestCase
{
    public function testGetValue()
    {
        $test = \Diff\genDiff('test/fixtures/before.json', 'test/fixtures/after.json');
        //var_dump($test);
        $result = file_get_contents('test/fixtures/diff.json');
        $this->assertEquals($test, $result);

        $test = \Diff\genDiff('test/fixtures/before.yml', 'test/fixtures/after.yml');
        //var_dump($test);
        $result = file_get_contents('test/fixtures/diff.yml');
        $this->assertEquals($test, $result);
    }
}
