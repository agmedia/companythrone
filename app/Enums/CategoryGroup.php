<?php

namespace App\Enums;

enum CategoryGroup: string
{
    case Companies = 'tvrtke';
    case Blog      = 'blog';
    case Pages     = 'pages';
    case Footer    = 'footer';

    public function isClassic(): bool
    {
        return in_array($this, [self::Companies, self::Blog], true);
    }

    public function isPageGroup(): bool
    {
        return $this === self::Pages;
    }
}
