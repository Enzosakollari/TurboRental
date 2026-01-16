<?php
function db_config(): array
{
    return [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'name' => getenv('DB_NAME') ?: 'car_rental',
        'port' => (int)(getenv('DB_PORT') ?: 3307),
    ];
}

function db_connect(): mysqli
{
    $config = db_config();
    return new mysqli(
        $config['host'],
        $config['user'],
        $config['pass'],
        $config['name'],
        $config['port']
    );
}
?>
