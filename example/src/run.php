<?php
$ob = ob_get_clean();
global $spark;
// Render out
print $spark->run($ob);