<?php

namespace Diff\AST;

use Illuminate;

function getNode($key, $content1, $content2, $nodeTypes)
{
    $types = collect($nodeTypes);
    $action = $types->first(function ($item) use ($key, $content1, $content2) {
        return $item['check']($key, $content1, $content2);
    });
    $node = $action['action']($key, $content1, $content2);
    return $node;
}

function getAST($content1, $content2)
{
    $nodeTypes = [
      [
        "type" => 'children',
        "check" => function ($key, $content1, $content2) {
            //return ((in_array($key, array_keys($content1)) && in_array($key, array_keys($content2))) && (is_array($content1[$key]) || is_array($content2[$key])));
            return ((in_array($key, array_keys($content1)) && is_array($content1[$key])) || (in_array($key, array_keys($content2)) && is_array($content2[$key])));
        },
        "action" => function ($key, $content1, $content2) {
            $arg1 = in_array($key, array_keys($content1)) ? $content1[$key] : [];
            $arg2 = in_array($key, array_keys($content2)) ? $content2[$key] : [];

            return ["type" => 'children', "key" => $key, "value" => getAST($arg1, $arg2)];
            //return ["type" => 'children', "key" => $key, "value" => $value];
            //return ["type" => 'children', "key" => $key, "value" => getAST($content1[$key], $content2[$key])];
        }
      ],
      [
        "type" => 'changed',
        "check" => function ($key, $content1, $content2) {
            return (in_array($key, array_keys($content1)) && in_array($key, array_keys($content2)) && $content1[$key] !== $content2[$key]);
        },
        "action" => function ($key, $content1, $content2) {
            return ["type" => 'changed', 'key' => $key, 'value' => [$content2[$key], $content1[$key]]];
            //return [["type" => 'removed', 'key' => $key, 'value' => $content1[$key]], ["type" => 'added', 'key' => $key, 'value' => $content2[$key]]];
        }
      ],
      [
        "type" => 'unchanged',
        "check" => function ($key, $content1, $content2) {
            return (in_array($key, array_keys($content1)) && in_array($key, array_keys($content2)) && $content1[$key] === $content2[$key]);
        },
        "action" => function ($key, $content1, $content2) {
            return ["type" => 'unchanged', 'key' => $key, 'value' => $content1[$key]];
        }
      ],
      [
        "type" => 'added',
        "check" => function ($key, $content1, $content2) {
            return (!in_array($key, array_keys($content1)) && in_array($key, array_keys($content2)));
        },
        "action" => function ($key, $content1, $content2) {
            return ["type" => 'added', 'key' => $key, 'value' => $content2[$key]];
        }
      ],
      [
        "type" => 'removed',
        "check" => function ($key, $content1, $content2) {
            return (in_array($key, array_keys($content1)) && !in_array($key, array_keys($content2)));
        },
        "action" => function ($key, $content1, $content2) {
            return ["type" => 'removed', 'key' => $key, 'value' => $content1[$key]];
        }
      ]
    ];

    $newKeys = array_unique(array_merge(array_keys($content1), array_keys($content2)));
    $result = array_reduce($newKeys, function ($acc, $key) use ($content1, $content2, $nodeTypes) {
        $acc[] = getNode($key, $content1, $content2, $nodeTypes);
        return $acc;
    }, []);

    //var_dump($result);
    return $result;
}
