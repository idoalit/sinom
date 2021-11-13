<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 13/11/2021 15:54
 * @File name           : DebugQueryTest.php
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

class DebugQueryTest extends \PHPUnit\Framework\TestCase
{
    public function testDebugQueryReplacement()
    {
        $connection = new \PDO('mysql:host=localhost;dbname=slims_v9', 'root', 'root');
        $biblio = new Biblio($connection);
        $biblio->title = 'This is title';
        $biblio->author = 'Author of Title';
        $biblio->insert();

        $this->assertEquals("insert into `biblio` (`title`, `author`) values ('This is title', 'Author of Title')", $biblio->debugQuery());
    }
}

class Biblio extends \Idoalit\Sinom\Database\Model
{
    protected $table = 'biblio';
    protected $primary_key = 'biblio_id';
}