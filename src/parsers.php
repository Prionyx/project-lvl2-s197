<?php

namespace Diff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($content, $format)
{
    switch ($format) {
        case 'json':
            return jsonParser($content);
        case 'yml':
            return yamlParser($content);
    }
}

function jsonParser($content)
{
    return json_decode($content, true);
}

function yamlParser($content)
{
    return Yaml::parse($content);
}
