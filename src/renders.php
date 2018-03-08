<?php

namespace Diff\Renders;

function getRender($ast, $format)
{
    switch ($format) {
        case 'pretty':
            return prettyRender($ast);
        case 'plain':
            return plainRender($ast);
    }
}

function prettyRender($ast)
{
    $spacesCount = function ($level) {
        $a = '';
        for ($i = 1; $i < $level; $i++) {
            $a = $a . '    ';
        }
        return $a;
    };
    $iter = function ($ast, $level) use (&$iter, $spacesCount) {
        $spaces = $spacesCount($level);
        $report = array_map(function ($item) use ($iter, $level, $spaces) {
            switch ($item['type']) {
                case 'children':
                    $newValue = $iter($item['value'], $level + 1);
                    return "{$spaces}    {$item['key']}: {\n{$newValue}\n{$spaces}    }";
                case 'childrenAdd':
                    $key = array_keys($item['value'])[0];
                    return "{$spaces}  + {$item['key']}: {\n{$spaces}        {$key}: {$item['value'][$key]}\n{$spaces}    }";
                case 'childrenRm':
                    $key = array_keys($item['value'])[0];
                    return "{$spaces}  - {$item['key']}: {\n{$spaces}        {$key}: {$item['value'][$key]}\n{$spaces}    }";
                case 'unchanged':
                    return "{$spaces}    {$item['key']}: {$item['value']}";
                case 'changed':
                    return "{$spaces}  + {$item['key']}: {$item['value'][0]}\n{$spaces}  - {$item['key']}: {$item['value'][1]}";
                case 'added':
                    return "{$spaces}  + {$item['key']}: {$item['value']}";
                case 'removed':
                    return "{$spaces}  - {$item['key']}: {$item['value']}";
            }
        }, $ast);

        return (implode(PHP_EOL, $report));
    };

    return "{\n{$iter($ast, 1)}\n}\n";
}
