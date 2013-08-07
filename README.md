Spark [![Build Status](https://travis-ci.org/SkylarKelty/Spark.png?branch=master)](https://travis-ci.org/SkylarKelty/Spark)
=====
*The 300 line template parser*

A high-performance Template Parser designed to work inside a full templating engine or alongside an application.
Whilst Spark can be used on its own it includes a very limited set of features and is really only designed to work alongside a larger system.

Requires:
  - PHP 5.3 (or above)

Spark takes a HTML page and tokenises all tags within a specified namespace (e.g. <Spark*>) it then runs through user-specified callbacks for each token and replaces the token with the result.

The result of this project is a very simple template parser that can be hooked up to a more fully-featured templating engine.

### Usage
```
$spark = new Spark\Core\Spark();
print $spark->run('<html><body><SparkVersion /></body></html>');
```

### Adding a tag
```
$spark = new Spark\Core\Spark();

$spark->addTag("Example", function($html, $inner) {
    return "Hello World!";
});

print $spark->run('<html><body><SparkExample /></body></html>');
```