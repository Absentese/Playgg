<?php

/**
 * Экспорт database/database.sqlite в SQL-дамп (запуск: php database/dumps/export-sqlite.php).
 */

$dbPath = dirname(__DIR__).'/database.sqlite';

if (! is_file($dbPath)) {
    fwrite(STDERR, "Файл не найден: {$dbPath}\n");
    exit(1);
}

$outPath = __DIR__.'/playgg-'.date('Y-m-d').'.sql';

$pdo = new PDO('sqlite:'.$dbPath, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$tables = $pdo->query(
    "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
)->fetchAll(PDO::FETCH_COLUMN);

$fh = fopen($outPath, 'wb');
fwrite($fh, "-- playgg SQLite dump\n-- Generated: ".date('c')."\n\n");
fwrite($fh, "PRAGMA foreign_keys=OFF;\nBEGIN TRANSACTION;\n\n");

foreach ($tables as $table) {
    $create = $pdo->query(
        "SELECT sql FROM sqlite_master WHERE type='table' AND name=".$pdo->quote($table)
    )->fetchColumn();
    fwrite($fh, $create.";\n\n");

    $rows = $pdo->query("SELECT * FROM \"{$table}\"");
    while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
        $columns = array_map(fn ($c) => '"'.$c.'"', array_keys($row));
        $values = array_map(function ($v) use ($pdo) {
            if ($v === null) {
                return 'NULL';
            }

            return $pdo->quote((string) $v);
        }, array_values($row));

        fwrite($fh, 'INSERT INTO "'.$table.'" ('.implode(', ', $columns).') VALUES ('.implode(', ', $values).");\n");
    }

    fwrite($fh, "\n");
}

fwrite($fh, "COMMIT;\n");
fclose($fh);

echo "Dump written: {$outPath}\n";
