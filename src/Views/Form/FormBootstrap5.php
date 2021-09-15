<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 9/10/21 4:15 PM
 * @File name           : FormBootstrap5.php
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

namespace Idoalit\Sinom\Views\Form;

use Volnix\CSRF\CSRF;

class FormBootstrap5 extends Form
{
    protected $method;
    protected $action;
    protected $entype;

    public function __construct($method, $action, $enctype = null)
    {
        $this->method = $method;
        $this->action = $action;
        $this->entype = $enctype;
    }

    function text($label, $name, $value, $placeholder = '')
    {
        $this->inputs[] = <<<HTML
<div class="mb-3">
  <label for="input-{$name}" class="form-label">{$label}</label>
  <input type="text" class="form-control" id="input-{$name}" name="{$name}" placeholder="{$placeholder}">
</div>
HTML;
    }

    function textarea($label, $name, $value, $rows = 1)
    {
        $this->inputs[] = <<<HTML
<div class="mb-3">
  <label for="input-{$name}" class="form-label">{$label}</label>
  <textarea class="form-control" id="input-{$name}" name="{$name}" rows="{$rows}">{$value}</textarea>
</div>
HTML;

    }

    function datalist($label, $urlPopup, $urlList)
    {
        $id_iframe = 'datalist--' . strtolower(str_replace(' ', '-', $label));
        $this->inputs[] = <<<HTML
<div class="mb-3">
    <label class="form-label">{$label}</label>
    <div>
        <a href="{$urlPopup}" class="btn btn-info text-dark modalIframe" data-iframe="{$id_iframe}" title="Tambah {$label}">Tambah {$label}</a>
    </div>
    <div class="mt-2 rounded overflow-hidden ratio auto-height">
        <iframe id="{$id_iframe}" src="{$urlList}" title="Datalist {$label}" allowfullscreen onload="resizeIframe(this)"></iframe>
    </div>
</div>
HTML;

    }

    function build($print = false): string
    {
        $str = <<<HTML
<form action="{$this->action}" method="{$this->method}">
HTML;
        $str .= CSRF::getHiddenInputString();
        $str .= implode('', $this->inputs);
        $str .= '<button type="submit" class="btn btn-success">Submit</button>';
        $str .= '</form>';

        if ($print) echo $str;
        return $str;
    }
}