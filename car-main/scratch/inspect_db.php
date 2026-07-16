<?php
$db = new PDO('sqlite:database/syarah.db');
$res = $db->query("SELECT sql FROM sqlite_master WHERE type='table'");
foreach ($res as $row) {
    echo $row['sql'] . "\n\n";
}
