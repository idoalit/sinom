# Sinom

`Sinom` merupakan ORM (Object Relational Mapping) sederhana.

Install dengan composer
```
composer require idoalit/sinom
```
## Cara Penggunaan
- Buat sebuah `class` model yang meng-extends `\Idoalit\Sinom\Database\Model`
- `Sinom` mensyaratkan koneksi `\PDO` untuk koneksi ke database. 
  Koneksi ini dapat ditambahkan dalam `constructor` atau menggunakan method `setConnection`
  atau dengan membuat sebuah class skeleton yang memiliki static method `getInstance` untuk mengambil 
  instance dari `\PDO`.
- Contoh:

```
# Ini contoh class Biblio model

class Biblio extends \Idoalit\Sinom\Database\Model 
{
  protected $connection_class = MyPDOConnection::class;
  protected $table = 'Biblio';
  protected $primary_key = 'biblio_id';
}
```
- Penggunaan model
```
# Menambah data ke database
$biblio = new Biblio;
$biblio->title = 'Judul buku saya';
$biblio->insert();

# Mengambil data bibliografi dengan biblio ID
$biblio = Biblio::find($biblio_id);

# Mengambil data bibliografi dengan kata kunci
$biblio = Biblio::where('title', '=', 'judul buku saya')->first();

# Ambil data dengan object
$title = $biblio->title; // judul dari database

# Mengupdate data ke database
$biblio->title = 'Judul buku yang baru';
$biblio->update();

# menghapus data bibliografi dari database
$biblio->delete();
```