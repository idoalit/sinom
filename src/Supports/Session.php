<?php
/*
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : Sun Jun 27 2021 11:30:12
 * @File name           : Session.php
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

class Session
{
    public function __get($name)
    {
        // chack in default session
        if(isset($_SESSION[$name])) return $_SESSION[$name];
        // check in session flash
        if(isset($_SESSION['flash'])) {
            $session = $_SESSION['flash'][$name] ?? null;
            unset($_SESSION['flash'][$name]);
            return $session;
        }
        // return default
        return null;
    }

    public function __set($name, $value)
    {
        return $_SESSION[$name] = $value;
    }

    public function flash($name, $value = null) {
        if(!isset($_SESSION['flash'])) $_SESSION['flash'] = [];
        return $_SESSION['flash'][$name] = $value;
    }
}
