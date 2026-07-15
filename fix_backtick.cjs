const fs = require('fs');
const files = [
    'users.php',
    'settings.php',
    'requests.php',
    'offers.php',
    'notifications.php',
    'cars.php',
    'brands.php'
];

for (const f of files) {
    const path = `c:/xampp/htdocs/سيارة/admin/${f}`;
    let content = fs.readFileSync(path, 'utf8');
    content = content.replace(/`n    /g, '\n    ');
    // Also let's update v=3 to v=4 so the user gets the CSS flexbox update!
    content = content.replace(/style\.css\?v=3/g, 'style.css?v=4');
    fs.writeFileSync(path, content, 'utf8');
    console.log(`Fixed ${f}`);
}
