<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 9/10/21 4:36 PM
 * @File name           : Form.php
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

abstract class Form
{
    protected array $inputs = [];

    abstract function build();

    abstract function text($label, $name, $value);

    abstract function textarea($label, $name, $value);

    abstract function file($label, $name, $multiple = false, $accepts = []);

    abstract function fileList($label, $urlPopup, $urlList);

    abstract function datalist($label, $urlPopup, $urlList);
}
