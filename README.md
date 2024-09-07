
# Eloquent Wrapper for WordPress by Emneslab

This library package allows you to use Laravel's [Eloquent ORM](https://laravel.com/docs/11.x/eloquent) seamlessly within WordPress, extending the capabilities of WordPress' native database handling.

## Package Installation

To install this package, update your `composer.json` file as follows:

```json
{
    "require": {
        "emneslab/wp-eloquent": "dev-main"
    }
}
```

Then run the command:

```sh
$ composer install
```

## Usage Examples

### Basic Usage

```php
$db = \Emneslab\ORM\Database\Connection::instance();

var_dump($db->table('users')->find(1));
var_dump($db->select('SELECT * FROM wp_users WHERE id = ?', [1]));
var_dump($db->table('users')->where('user_login', 'john')->first());

// OR with DB facade
use Emneslab\ORM\Support\Facades\DB;

var_dump(DB::table('users')->find(1));
var_dump(DB::select('SELECT * FROM wp_users WHERE id = ?', [1]));
var_dump(DB::table('users')->where('user_login', 'john')->first());
```

### Working with WordPress Models

You can work with WordPress models just like Eloquent models:

```php
use Emneslab\ORM\WP\Post;

// Fetch all posts
var_dump(Post::all());

// Filter by post type and status
var_dump(Post::type('page')->get()->toArray()); // Fetch all pages
var_dump(Post::status('publish')->get()->toArray()); // Fetch all published posts
var_dump(Post::type('page')->status('publish')->get()->toArray()); // Fetch all published pages
```

### Using Custom Tables

You can define models for custom WordPress tables, making use of Eloquent features:

```php
<?php
namespace CustomNamespace;

use Emneslab\ORM\Database\Eloquent\Model;

class CustomTableModel extends Model {
    protected $table = 'custom_table_name';
    protected $fillable = ['column1', 'column2', 'column3'];
    public $timestamps = false;
    protected $primaryKey = 'ID';
    protected $guarded = ['ID'];

    public function getTable()
    {
        if (isset($this->table)) {
            return $this->getConnection()->getTablePrefix() . $this->table;
        }

        return parent::getTable();
    }
}
```

### Schema Management

You can use Laravel's Schema builder to create or modify tables:

```php
use Illuminate\Database\Schema\Blueprint;
use Emneslab\ORM\Support\Facades\Schema;

// Dropping a table
Schema::drop('wp_test_database_table');

// Creating a new table
Schema::create('test_database_table', function (Blueprint $table) {
    $table->id();
});
```

## How It Works

- Eloquent is used as a query builder.
- Queries are executed using WordPress' native [WPDB](http://codex.wordpress.org/Class_Reference/wpdb) class.
- This setup ensures compatibility with WordPress debugging tools like `debug-bar` or `query-monitor`.
- It doesn't create any additional MySQL connections, making it lightweight and efficient.

## Minimum Requirements

- PHP >= 7.2
- WordPress >= 6.2

## Author

[Emneslab](https://lab.emnes.co)

This package is inspired by [Tareq Hasan's wp-eloquent](https://github.com/tareq1988/wp-eloquent) and extends the functionality with additional support and features for WordPress developers looking to leverage Eloquent ORM.