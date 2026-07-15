$lines = Get-Content 'c:\xampp\htdocs\سيارة\assets\css\style.css'
$part1 = $lines[0..714]
$part2 = @(
    '.compare-row-title { font-weight: 700; background-color: var(--beige); justify-content: flex-start; padding-inline-start: 1.5rem; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-muted); }',
    '',
    '.text-center { text-align: center; }',
    '.text-muted  { color: var(--text-muted); }',
    ''
)
$part3 = $lines[915..($lines.Length-1)]
$newLines = $part1 + $part2 + $part3
$newLines | Set-Content 'c:\xampp\htdocs\سيارة\assets\css\style.css' -Encoding UTF8
