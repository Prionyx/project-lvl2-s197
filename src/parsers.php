<?php

namespace Diff\Parsers;

function getParser($content, $format)
{
    switch ($format) {
        case 'json':
            return jsonParser($content);
    }
}

function jsonParser($content)
{
    return json_decode($content, true);
}
