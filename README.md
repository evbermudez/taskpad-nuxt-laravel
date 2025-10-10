# TaskPad – Laravel 10 API + Nuxt 3 UI (Monorepo)

Simple, fast task manager built in one repo:
- **Backend** → Laravel 10 (Sail + Sanctum, Repository Pattern, Policies, Form Requests, JsonResources, Pest)
- **Frontend** → Nuxt 3 (TypeScript, Tailwind CSS, Pinia, Nuxt UI, Lucide Icons)

## Project Structure

```
/backend   → Laravel API
/frontend  → Nuxt 3 app
```

## Services Used

- **MySQL (via Sail)** – required  
- **Redis** – not required (no queues/caching needed)

## Quick Start

### Backend (Laravel)
```bash
cd backend
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```
Env highlights:
```
APP_URL=http://localhost
SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=localhost:3000
```
Test account:
```
Email: matt@example.com
Password: password
```

### Frontend (Nuxt)
```bash
cd frontend
pnpm install
pnpm dev
```
Then open http://localhost:3000

## API (Auth)

| Method | Endpoint | Description |
|:--|:--|:--|
| POST | /api/login | Log in user → `{ user }` |
| GET  | /api/me    | Get current authenticated user |
| POST | /api/logout| Logout user → `{ message }` |

## API (Tasks)

| Method | Endpoint | Description |
|:--|:--|:--|
| GET  | /api/tasks?date=YYYY-MM-DD&sort=position\|priority\|created_at&dir=asc\|desc | List tasks by date |
| POST | /api/tasks | Create new task `{ statement, task_date, priority? }` |
| PATCH| /api/tasks/{id} | Update a task `{ statement?, task_date?, priority?, is_done? }` |
| POST | /api/tasks/{id}/toggle | Toggle task completion |
| DELETE | /api/tasks/{id} | Delete a task |
| POST | /api/tasks/reorder | Reorder tasks `{ orders:[{id,position}] }` |
| GET  | /api/search?q=keyword | Search tasks across all dates |

## Notes

- Drag-drop order is saved in `position`
- Priority values:
  - `1 = High`
  - `2 = Medium`
  - `3 = Low`
-	Uses Laravel Sanctum for SPA authentication
-	Fully type-safe API client with $fetch and Pinia
