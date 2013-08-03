<?php require_once("src/loader.php"); ?>

<!DOCTYPE html>
<html>
    <head>
        <SparkHeader>Speed Test | Spark</SparkHeader>
    </head>
    <body>
        <div class="container">
            <h1>Speed Test</h1>
            <div class="tests">
                <?php
                for ($i = 0; $i <= 1000; $i++) {
                    print '<SparkPass></SparkPass>';
                }
                ?>
                <p>Parsed 1000 snippets in <STIME> seconds using <KBDATA>kb of memory</p>
            </div>
        </div>
        <div class="version">
            <p>Rendered with <SparkVersion /></p>
        </div>
    </body>
</html>

<?php
$page = ob_get_clean();

$k = memory_get_usage();
$t = microtime(true);

global $spark;
$out = $spark->run($page);

$out = str_replace("<STIME>", round((microtime(true) - $t), 3), $out);
$out = str_replace("<KBDATA>", round((memory_get_peak_usage() - $k) / 1024), $out);

print $out;
?>