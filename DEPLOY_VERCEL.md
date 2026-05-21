# Деплой playgg (gamevault) на Vercel

Laravel на Vercel работает через **serverless PHP** и **внешнюю базу данных**. SQLite на Vercel **не подходит**.

## 1. База данных (обязательно)

Создайте бесплатную PostgreSQL:

1. [Neon](https://neon.tech) → New Project → скопируйте **Connection string**
2. Или в Vercel: **Storage → Create Database → Postgres**

Строка вида:

```text
postgresql://user:pass@host/dbname?sslmode=require
```

## 2. Подключение GitHub к Vercel

1. [vercel.com](https://vercel.com) → **Add New → Project**
2. Импортируйте репозиторий **gamevault**
3. **Framework Preset:** Other
4. **Root Directory:** `.` (корень)
5. Build и Output уже заданы в `vercel.json` — не меняйте вручную, если не уверены

## 3. Переменные окружения (Settings → Environment Variables)

Добавьте для **Production** (и Preview при необходимости):

| Переменная | Значение |
|------------|----------|
| `APP_NAME` | playgg |
| `APP_ENV` | production |
| `APP_KEY` | `php artisan key:generate --show` (локально) |
| `APP_DEBUG` | false |
| `APP_URL` | `https://ваш-проект.vercel.app` (после первого деплоя) |
| `POSTGRES_URL` | строка подключения Neon / Vercel Postgres |
| `DB_CONNECTION` | pgsql |
| `SESSION_DRIVER` | cookie |
| `SESSION_SECURE_COOKIE` | true |
| `CACHE_STORE` | array |
| `QUEUE_CONNECTION` | sync |
| `LOG_CHANNEL` | stderr |
| `FILESYSTEM_DISK` | local |

Опционально AI-чат:

| `OPENAI_API_KEY` | ваш ключ |
| `GEMINI_API_KEY` | или Groq |

Опционально миграции при каждом деплое (осторожно):

| `VERCEL_RUN_MIGRATIONS` | 1 |

## 4. Первый запуск миграций

**Один раз** с вашего ПК (подключение к облачной БД):

```bash
# В .env локально временно:
# DB_CONNECTION=pgsql
# DB_URL=postgresql://...

php artisan migrate --seed --force
```

Либо включите `VERCEL_RUN_MIGRATIONS=1` на один деплой, затем уберите.

## 5. Deploy

Нажмите **Deploy**. После успеха откройте URL проекта.

Демо-вход (после seed):

- `demo@playgg.ru` / `password`
- `admin@playgg.ru` / `password`

## 6. Обновление APP_URL

После деплоя скопируйте точный домен (`https://gamevault-xxx.vercel.app`) в `APP_URL` и сделайте **Redeploy**.

## 7. Ограничения на Vercel

- Загруженные **аватары** в `public/images/avatars` не сохраняются между вызовами — для продакшена нужен S3
- Таймаут функции до 30 с (`vercel.json`)
- Локальная SQLite в git не используется на сервере

## 8. Ошибки

| Симптом | Решение |
|---------|---------|
| 500 на всех страницах | Проверьте `APP_KEY`, `POSTGRES_URL`, логи в Vercel → Deployments → Functions |
| Нет стилей | Убедитесь, что `public/css` в репозитории; Redeploy |
| Сессия сбрасывается | `SESSION_DRIVER=cookie`, `APP_URL` совпадает с доменом |
| Build: composer | Vercel должен видеть `composer.json` в корне |

## 9. Повторный деплой

Пуш в ветку `main` на GitHub → Vercel деплоит автоматически.
