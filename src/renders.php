<?php

namespace Diff\Renders;

function getRender($file, $format)
{
    switch ($format) {
        case 'pretty':
            return prettyRender($file);
    }
}

function prettyRender($ast)
{
    $report = array_map(function ($item) {
        switch ($item['type']) {
            case 'children':
                $newValue = prettyRender($item['value']);
                return "    {$item['key']}: {$newValue}";
            case 'unchanged':
                return "    {$item['key']}: {$item['value']}";
            case 'changed':
                return "  + {$item['key']}: {$item['value'][0]}\n  - {$item['key']}: {$item['value'][1]}";
            case 'added':
                return "  + {$item['key']}: {$item['value']}";
            case 'removed':
                return "  - {$item['key']}: {$item['value']}";
        }
    }, $ast);

    return ("{\n" . implode(PHP_EOL, $report) . "\n}\n");
}
