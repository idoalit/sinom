<?php
/*
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : Sun Jun 27 2021 09:41:18
 * @File name           : Text.php
 *
 * The MIT License (MIT)
 * Copyright (c) 2021 Waris Agung Widodo
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Idoalit\Sinom\Supports;

class Text
{
    /**
     * Change camel case string into lowercase with separator
     *
     * @param $text
     * @param string $separator
     * @return string
     */
    public static function unCamelCase($text, string $separator = '_'): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $text, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        return implode($separator, $ret);
    }
}
