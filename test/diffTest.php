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

        $test = \Diff\genDiff('test/fixtures/rebefore.json', 'test/fixtures/reafter.json');
        //var_dump($test);
        $result = file_get_contents('test/fixtures/rediff.json');
        $this->assertEquals($test, $result);

        $test = \Diff\genDiff('test/fixtures/rebefore.json', 'test/fixtures/reafter.json', 'plain');
        //var_dump($test);
        $result = file_get_contents('test/fixtures/diff.plain');
        $this->assertEquals($test, $result);

        $test = \Diff\genDiff('test/fixtures/rebefore.json', 'test/fixtures/reafter.json', 'json');
        //var_dump($test);
        $result = file_get_contents('test/fixtures/jsonReport.json');
        $this->assertEquals($test, $result);
    }
}
