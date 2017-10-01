<?php

namespace AppBundle\Services;


class MarkdownTransformer
{
    public function parse($str)
    {
        return strtoupper($str);
    }
}