<?php

namespace Diff;

function genDiff($reportFormat, $file1, $file2)
{
    $content1 = Parsers\getParser(getContent($file1), getFormat($file1));
    $content2 = Parsers\getParser(getContent($file2), getFormat($file2));
    $ast = AST\getAST($content1, $content2);

    return Renders\getRender($ast, $reportFormat);
}

function getFormat($path)
{
    return pathinfo($path, PATHINFO_EXTENSION);
}

function getContent($file)
{
    return file_get_contents($file);
}
