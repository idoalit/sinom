<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 9/10/21 4:15 PM
 * @File name           : FormBootstrap4.php
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

class FormBootstrap4 extends Form
{
    protected $method;
    protected $action;
    protected $name;
    protected $enctype;

    public function __construct($method, $action, $name = 'sinomForm')
    {
        $this->method = $method;
        $this->action = $action;
        $this->name = $name;
    }

    function text($label, $name, $value, $placeholder = '', $required = false)
    {
        $required = $required ? 'required' : '';
        $this->inputs[] = <<<HTML
<div class="form-group row">
    <label for="input-{$name}" class="col-sm-2 col-form-label">{$label}</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="input-{$name}" name="{$name}" placeholder="{$placeholder}" {$required}>
    </div>
</div>
HTML;
    }

    function date($label, $name, $value, $placeholder = '', $required = false) {
        $required = $required ? 'required' : '';
        $this->inputs[] = <<<HTML
<div class="form-group row">
    <label for="input-{$name}" class="col-sm-2 col-form-label">{$label}</label>
    <div class="col-sm-10">
        <input type="date" class="form-control" id="input-{$name}" name="{$name}" placeholder="{$placeholder}" {$required}>
    </div>
</div>
HTML;
    }

    function email($label, $name, $value, $placeholder = '', $required = false) {
        $required = $required ? 'required' : '';
        $this->inputs[] = <<<HTML
<div class="form-group row">
    <label for="input-{$name}" class="col-sm-2 col-form-label">{$label}</label>
    <div class="col-sm-10">
        <input type="email" class="form-control" id="input-{$name}" name="{$name}" placeholder="{$placeholder}" {$required}>
    </div>
</div>
HTML;
    }

    function password($label, $name, $value, $placeholder = '', $required = false) {
        $required = $required ? 'required' : '';
        $this->inputs[] = <<<HTML
<div class="form-group row">
    <label for="input-{$name}" class="col-sm-2 col-form-label">{$label}</label>
    <div class="col-sm-10">
        <input type="password" class="form-control" id="input-{$name}" name="{$name}" placeholder="{$placeholder}" {$required}>
    </div>
</div>
HTML;
    }

    function textarea($label, $name, $value, $rows = 1, $required = false)
    {
        $required = $required ? 'required' : '';
        $this->inputs[] = <<<HTML
<div class="form-group row">
    <label for="input-{$name}" class="col-sm-2 col-form-label">{$label}</label>
    <div class="col-sm-10">
    <textarea class="form-control" id="input-{$name}" name="{$name}" rows="{$rows}" {$required}>{$value}</textarea>
    </div>
</div>
HTML;

    }

    function file($label, $name, $multiple = false, $accepts = [], $required = false) {
        $required = $required ? 'required' : '';
        $this->enctype = 'multipart/form-data';
        $multiple_str = '';
        if($multiple) {
            $name = str_replace(['[',']'], '', $name);
            $name = trim($name) . '[]';
            $multiple_str = 'multiple';
        }
        $accept_ext = implode(',', $accepts);
        $accept_str = !empty($accepts) ? 'accept="'.$accept_ext.'"' : '';
        $this->inputs[] = <<<HTML
        <div class="form-group row">
            <label for="input-{$name}" class="col-sm-2 col-form-label">{$label}</label>
            <div class="col-sm-10">
            <input class="form-control" type="file" id="formFile" name="{$name}" {$multiple_str} {$accept_str} {$required}/>
            </div>
        </div>
HTML;

    }

    function fileList($label, $urlPopup, $urlList) {}

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

    function radio($label, $name, $options, $default, $required = false) {
        $radio_str = '';
        foreach($options as $index => $option) {
            $checked = $default === $option[0] ? 'checked' : '';
            $required = $required && $index < 1 ? 'required' : '';
            $radio_str .= <<<HTML
<div class="form-check">
    <input class="form-check-input" type="radio" name="{$name}" id="{$name}-{$index}" value="{$option[0]}" {$checked} {$required} />
    <label class="form-check-label" for="{$name}-{$index}">
        {$option[1]}
    </label>
</div>
HTML;
        }

        $this->inputs[] = <<<HTML
<fieldset class="form-group row">
    <legend class="col-form-label col-sm-2 float-sm-left pt-0">{$label}</legend>
    <div class="col-sm-10">
      {$radio_str}
    </div>
  </fieldset>
HTML;

    }

    function build($print = false): string
    {
        $enctype = !is_null($this->enctype) ? 'enctype="'.$this->enctype.'"' : '';
        $str = <<<HTML
<form action="{$this->action}" method="{$this->method}" {$enctype} name="{$this->name}">
HTML;
        $str .= CSRF::getHiddenInputString($this->name);
        $str .= implode('', $this->inputs);
        $str .= '<button type="submit" class="btn btn-success">Submit</button>';
        $str .= '</form>';

        if ($print) echo $str;
        return $str;
    }
}