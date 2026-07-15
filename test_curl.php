<?php
$ch = curl_init("http://localhost:8012/سيارة/admin/cars.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
file_put_contents("c:/xampp/htdocs/سيارة/test_output.txt", substr($result, 0, 500));
echo "Done";
