# Деплой playgg на Railway

Laravel на Railway работает с **PostgreSQL** (SQLite на сервере не подходит). Сборка через **Railpack**; миграции — в pre-deploy.

## 1. Подготовка репозитория

Убедитесь, что в GitHub есть актуальная ветка `main` с файлами:

- `railway.toml`
- `railway/init-app.sh`
- `nixpacks.toml`
- `.env.railway.example`

## 2. Проект на Railway

1. [railway.com](https://railway.com) → **New Project** → **Deploy from GitHub repo**
2. Выберите репозиторий **Playgg** (gamevault)
3. **Add PostgreSQL** на canvas (правый клик → Database → PostgreSQL)

## 3. Переменные окружения

В сервисе приложения → **Variables** → **Raw Editor** — вставьте из `.env.railway.example` и задайте:

| Переменная | Значение |
|------------|----------|
| `APP_KEY` | `php artisan key:generate --show` (локально) |
| `APP_URL` | `https://${{RAILWAY_PUBLIC_DOMAIN}}` |
| `DATABASE_URL` | `${{Postgres.DATABASE_URL}}` |
| `DB_CONNECTION` | `pgsql` |

Остальные — как в `.env.railway.example`.

## 4. Pre-deploy

В **Settings → Deploy** поле **Pre-Deploy Command** уже задано в `railway.toml`:

```bash
chmod +x railway/init-app.sh && railway/init-app.sh
```

Скрипт выполняет `migrate --force` и кэширование config/route/view.

## 5. Первое наполнение БД

После успешного деплоя один раз добавьте переменную:

```env
RAILWAY_RUN_SEED=1
```

Сделайте **Redeploy**, дождитесь завершения, затем **удалите** `RAILWAY_RUN_SEED` и снова redeploy.

Сидер скачивает обложки со Steam — первый деплой может занять несколько минут.

Либо из CLI:

```bash
railway run php artisan db:seed --force
```

## 6. Публичный домен

**Settings → Networking → Generate Domain** → скопируйте URL в `APP_URL` → **Redeploy**.

## 7. Демо-аккаунты (после seed)

| Роль | Email | Пароль |
|------|-------|--------|
| Админ | admin@playgg.ru | password |
| Пользователь | demo@playgg.ru | password |

## 8. Логи и отладка

- Логи: **Deployments → View logs** или `railway logs`
- Healthcheck: `GET /up`
- Локально проверка production-настроек: скопируйте `.env.railway.example` в `.env` и укажите Postgres URL

## 9. Обновление сайта

Пуш в `main` → Railway деплоит автоматически (если включён GitHub deploy).

## Ограничения

- Файлы в `public/images/products/` хранятся на диске контейнера; при пересоздании сервиса загруженные вручную фото могут пропасть — для продакшена позже стоит S3/R2.
- Очередь (`QUEUE_CONNECTION=sync`) — фоновые job не используются.
