<?php
$required = [
    'fileinfo', 'gd', 'intl', 'mbstring', 'pdo_mysql',
    'xml', 'zip', 'bcmath', 'ctype', 'json', 'tokenizer', 'openssl',
];

echo '<h1>PHP Extension Check</h1>';
echo '<table border="1" cellpadding="8" style="border-collapse:collapse">';
echo '<tr><th>Extension</th><th>Status</th></tr>';

$allOk = true;
foreach ($required as $ext) {
    $loaded = extension_loaded($ext);
    if (!$loaded) $allOk = false;
    $color = $loaded ? 'green' : 'red';
    echo "<tr><td>$ext</td><td style='color:$color;font-weight:bold'>" . ($loaded ? '✓ OK' : '✗ MISSING') . '</td></tr>';
}
echo '</table>';

echo '<h2>PHP Version: ' . PHP_VERSION . '</h2>';
if ($allOk) echo '<p style="color:green;font-weight:bold">All required extensions are loaded.</p>';
else echo '<p style="color:red;font-weight:bold">Some extensions are missing. Install them via cPanel → Select PHP Version.</p>';

echo '<hr><h2>phpinfo()</h2>';
phpinfo();
