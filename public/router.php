<?php
// PHP built-in server router: serve existing static files directly, route everything else through Laravel.
// SCRIPT_FILENAME is the absolute path the CLI server resolved for the request.
if (isset($_SERVER['SCRIPT_FILENAME']) && is_file($_SERVER['SCRIPT_FILENAME'])) {
    return false;
}
require __DIR__ . '/index.php';
