<?php
$files = ['c:/xampp/htdocs/سيارة/includes/db.php', 'c:/xampp/htdocs/سيارة/includes/functions.php'];
foreach($files as $f) {
    $c = file_get_contents($f);
    echo basename($f) . ': ' . bin2hex(substr($c, 0, 3)) . '... len: ' . strlen($c) . ' ends with: ' . bin2hex(substr($c, -5)) . "\n";
}
