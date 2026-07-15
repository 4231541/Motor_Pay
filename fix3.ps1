$files = @('index.php', 'brands.php', 'cars.php', 'notifications.php', 'offers.php', 'requests.php', 'settings.php', 'users.php');
foreach ($file in $files) {
    $path = 'c:\xampp\htdocs\سيارة\admin\' + $file;
    $content = Get-Content $path -Raw -Encoding UTF8;
    $content = $content -replace 'href="\.\./assets/css/style\.css\?v=3"', 'href="../assets/css/style.css?v=4"';
    [IO.File]::WriteAllText($path, $content);
}
