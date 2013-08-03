<?php
// Render the page
global $spark;
print $spark->run(ob_get_clean());