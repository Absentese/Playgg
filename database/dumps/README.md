# Дампы базы данных

| Файл | Описание |
|------|----------|
| `playgg-YYYY-MM-DD.sql` | Полный дамп локальной SQLite (схема + данные) |

## Восстановление локально (SQLite)

```bash
rm -f database/database.sqlite
touch database/database.sqlite
php -r "
\$pdo = new PDO('sqlite:database/database.sqlite');
\$sql = file_get_contents('database/dumps/playgg-2026-05-21.sql');
\$pdo->exec(\$sql);
"
php artisan migrate --force
```

Или через `sqlite3` (если установлен):

```bash
sqlite3 database/database.sqlite < database/dumps/playgg-2026-05-21.sql
```

## Railway (PostgreSQL)

Дамп SQLite **не импортируется** в Postgres напрямую. На Railway используйте миграции и сидер:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Либо переменную `RAILWAY_RUN_SEED=1` при деплое (см. `DEPLOY_RAILWAY.md`).

## Обновить дамп

```bash
php database/dumps/export-sqlite.php
```
