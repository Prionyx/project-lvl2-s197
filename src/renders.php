<?php

namespace Diff\Renders;

use Illuminate;

function getRender($ast, $format)
{
    switch ($format) {
        case 'pretty':
            return prettyRender($ast);
        case 'plain':
            return plainRender($ast);
        case 'json':
            return jsonRender($ast);
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
                case 'nested':
                    $newValue = $iter($item['children'], $level + 1);
                    return "{$spaces}    {$item['key']}: {\n{$newValue}\n{$spaces}    }";
                case 'unchanged':
                    return "{$spaces}    {$item['key']}: {$item['value']}";
                case 'changed':
                    return "{$spaces}  + {$item['key']}: {$item['value'][0]}\n{$spaces}  - {$item['key']}: {$item['value'][1]}";
                case 'added':
                    if (is_array($item['value'])) {
                        $key1 = $item['key'];
                        $key2 = array_keys($item['value'])[0];
                        $value = $item['value'][$key2];
                        return "{$spaces}  + {$key1}: {\n{$spaces}        {$key2}: {$value}\n    {$spaces}}";
                    }
                    return "{$spaces}  + {$item['key']}: {$item['value']}";
                case 'removed':
                    if (is_array($item['value'])) {
                        if (is_array($item['value'])) {
                            $key1 = $item['key'];
                            $key2 = array_keys($item['value'])[0];
                            $value = $item['value'][$key2];
                            return "{$spaces}  - {$key1}: {\n{$spaces}        {$key2}: {$value}\n    {$spaces}}";
                        }
                    }
                    return "{$spaces}  - {$item['key']}: {$item['value']}";
            }
        }, $ast);

        return (implode(PHP_EOL, $report));
    };

    return "{\n{$iter($ast, 1)}\n}\n";
}

function plainRender($ast)
{
    $iter = function ($ast, $parent) use (&$iter) {
        $report = array_map(function ($item) use ($iter, $parent) {
            switch ($item['type']) {
                case 'nested':
                    $newValue = $iter($item['children'], $item['key'] . ".");
                    return "{$parent}{$newValue}";
                case 'changed':
                    return "Property '{$parent}{$item['key']}' was changed. From '{$item['value'][1]}' to '{$item['value'][0]}'\n";
                case 'added':
                    if (is_array($item['value'])) {
                        return "Property '{$parent}{$item['key']}' was added with value: 'complex value'\n";
                    }
                    return "Property '{$parent}{$item['key']}' was added with value: '{$item['value']}'\n";
                case 'removed':
                    return "Property '{$parent}{$item['key']}' was removed\n";
            }
        }, $ast);

        return (implode('', $report));
    };

    return $iter($ast, '');
}

function jsonRender($ast)
{
    $iter = function ($ast) use (&$iter) {
        $report = array_map(function ($item) use ($iter) {
            switch ($item['type']) {
                case 'nested':
                    return [$item['key'] => $iter($item['children'])];
                case 'unchanged':
                    return [$item['key'] => $item['value']];
                case 'changed':
                    return [$item['key'] => $item['value'][0],$item['key'] => $item['value'][1]];
                case 'added':
                    return [$item['key'] => $item['value']];
                case 'removed':
                    return [$item['key'] => $item['value']];
            }
        }, $ast);

        return $report;
    };

    return json_encode($iter($ast)) . "\n";
}
