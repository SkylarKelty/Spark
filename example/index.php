<?php
$t = microtime(true);
require_once("src/loader.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <SparkHeader>Home | Spark</SparkHeader>
    </head>
    <body>
        <div class="container">
            <h1>Welcome to Spark!</h1>
            <p>This folder is a demonstration of how Spark can be used...</p>
        </div>
        <div class="version">
            <p>Rendered with <SparkVersion /></p>
        </div>
    </body>
</html>

<?php
require_once("src/run.php");
print "\n" . '<!-- Page loaded in ' . round((microtime(true) - $t), 3) . ' seconds -->';
?>