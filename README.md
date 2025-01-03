# Cloud Bill Master Database Model
Cloud Bill Master Singleton Database Model is a PHP-based project that implements a robust, object-oriented database management system for handling complex transactions and data manipulation tasks. Built on top of MySQL, this singleton model aims to provide a high-performance, flexible, and secure way to interact with databases in PHP applications, specifically designed to streamline billing and cloud data management systems.

# Key Features
* <b>Object-Oriented Structure</b>: Built with PHP OOP principles, ensuring code reusability, scalability, and maintainability.</br>
* <b>Custom Database and Model Classes</b>: Uses a custom Database class for managing database connections, queries, and transactions, and a Model class to represent data entities in the application.</br>
* <b>Secure Transactions</b>: Implements ACID-compliant transactions for consistent and reliable data handling.</br>
* <b>Dynamic Query Builder</b>: Supports dynamic query generation with a range of options for filters, sorting, and pagination, making it easy to create complex queries without directly writing SQL.</br>
* <b>Error Handling</b>: Comprehensive error handling and logging for tracking and debugging issues efficiently.</br>
* <b>Scalable Architecture</b>: Designed with scalability in mind, suitable for all type of PHP applications.</br>
* <b>Easy Integration</b>: Integrates seamlessly with other PHP-based applications and frameworks, allowing flexible deployment in diverse environments.</br>

## Technologies Used
* <b>PHP (Object-Oriented)</b>: Core programming language, providing OOP features for structure and maintainability.</br>
* <b>MySQL</b>: Relational database management system used for data storage, with optimized queries for faster performance.</br>
* <b>PDO (PHP Data Objects)</b>: Utilized for secure database access with prepared statements to prevent SQL injection.</br>

## Installation
Use Without Composer:
Download the source code and copy it in your application directory. For manual installation, please remove the require vendor autoload file and use the db_model.php file from the repository.
```php
require_once('path/cbm-model/db_model.php');
```
Install with composer:
```bash
composer require cb-master/cb-model
```

Configure your database settings in your PHP application page top section.
```php
use CBM\Model\Model;

// Require Autoload File
require_once("./vendor/autoload.php");

// Config DB Model
Model::config([
    'driver'    =>  'mysql',
    'host'      =>  'localhost',
    'name'      =>  'database_name',
    'user'      =>  'database_user_here',
    'password'  =>  'database_password_here'
]);

// Add 'port' Key if you are not using the default port.
/**
 * Model::config([
 *  'driver'    =>  'mysql',
 *  'host'      =>  'localhost',
 *  'port'      =>  12345,
 *  'name'      =>  'database_name',
 *  'user'      =>  'database_user_here',
 *  'password'  =>  'database_password_here',
 * ])
 * */

```
By default fetching data as object. But you can fetch data as array.
 ```php
// Config DB Model
Model::config([
    'driver'    =>  'mysql',
    'host'      =>  'localhost',
    'name'      =>  'database_name',
    'user'      =>  'database_user_here',
    'password'  =>  'database_password_here',,
    'object'    =>  false
]);
```
You can ignore the driver key if you are using the mysql driver. Mysql is the default driver in this system.
 ```php
// Config DB Model
Model::config([
    'host'      =>  'localhost',
    'name'      =>  'database_name',
    'user'      =>  'database_user_here',
    'password'  =>  'database_password_here',
]);
```
## Usage
This project provides a base for any PHP application needing a reliable and efficient database model, especially useful for billing and cloud services. For detailed usage examples, please see the given method implementation below.

### Get Data From Table
```php
// Get All Data With Select *
Model::table('table_name')->select()->get();

// Get All Data With Selected Columns
Model::table('table_name')->select('column_1, column_2, column_3, .....')->get();

// Get Single Data
Model::table('table_name')->select()->single();

```

### Get Limited Data From Table (Default is 20)
Additional method to use is limit()
```php
// Get Data for Default Limit 20
Model::table('table_name')->select()->limit()->get();

// Custom Limit Set
Model::table('table_name')->select()->limit(40)->get();
```
