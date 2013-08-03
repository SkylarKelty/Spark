Spark
=====

High-performance Templating Engine designed to work with common WYSIWYG HTML editors.

Requires:
  - PHP 5.3 (or above)

Spark takes a HTML page and tokenises all tags within a specified namespace (e.g. <Spark*>) it then runs through user-specified callbacks for each token and replaces the token with the result.

The result of the project is a very simple template parser that can be hooked up to a more fully-featured templating engine.

### Usage
```
$spark = new Spark\Core\Spark();
print $spark->run('<html><body><SparkVersion /></body></html>');
```