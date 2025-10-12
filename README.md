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
| GET  | /api/tasks?date=YYYY-MM-DD&sort=position\|created_at&dir=asc\|desc | List tasks by date |
| POST | /api/tasks | Create new task `{ statement, task_date }` |
| PATCH| /api/tasks/{id} | Update a task `{ statement?, task_date?, is_done? }` |
| POST | /api/tasks/{id}/toggle | Toggle task completion |
| DELETE | /api/tasks/{id} | Delete a task |
| POST | /api/tasks/reorder | Reorder tasks `{ orders:[{id,position}] }` |
| GET  | /api/search?q=keyword | Search tasks across all dates |

## Backend Success Metrics

We expect the API to adhere to Laravel’s conventions, enforce consistency, and remain testable:

### Resourceful Endpoints & Controllers

All CRUD operations follow Laravel’s apiResource convention.
Additional endpoints (toggle, reorder, search) remain RESTful and consistent.

### Strict Input Validation

All user input validated through Form Requests or $request->validate() for custom endpoints.
Protects against malformed or malicious payloads.

### Consistent JSON Formatting

All responses use TaskResource (or collections) for unified output.

### Fine-Grained Authorization

TaskPolicy enforces ownership and access control via $this->authorize() calls in the controller.

### Automated Testing

Pest test suite covers:

- CRUD, reorder, toggle, search, and validation failure cases.
Run tests:

```
sail exec laravel.test ./vendor/bin/pest --filter=TaskApiTest
```

### Code Style Compliance (PSR-12)

The backend is PSR-12 compliant using Laravel Pint.

Check:

```
sail php ./vendor/bin/pint --preset=psr12 --test
```

Auto-fix:

```
sail php ./vendor/bin/pint --preset=psr12
```

## Frontend Success Metrics

We expect the Nuxt 3 interface to be type-safe, visually consistent, and maintain a clear component hierarchy.

### TypeScript First

All components, composables, and stores are written in TypeScript for strong type safety and better developer experience.

Run type checks:

```
pnpm typecheck
```

### Utility-First CSS

The UI uses Tailwind CSS, ensuring consistent spacing, colors, and typography with no custom CSS clutter.

### Modular Component Structure

The project is organized for clarity and reusability:

```
/components   → UI and layout building blocks
/composables  → Reusable logic (e.g. useApi)
/layouts      → Page layouts (default, etc.)
/stores       → Pinia stores for shared state
/pages        → Route-based views
```
Each component uses `<script setup lang="ts">` and follows Vue’s style guide conventions.

### State Management

Global state is handled with Pinia, replacing event buses or prop drilling.

| Store | Description |
|:--|:--|
auth.ts | Handles login, logout, and session restore |
tasks.ts | Manages fetching, reordering, toggling, and deleting tasks |
search.ts | Keeps global search query in sync |

### Linting & Formatting

Code style consistency is enforced with ESLint.

Run lint:
```
pnpm lint
```

Auto-fix:
```
pnpm lint --fix
```

### Build & Dev Commands

Start development:

```
pnpm dev
```

## Notes

- Drag-drop order is saved in `position`
- Drag-drop task reordering is disabled when a search query is active.
-	Uses Laravel Sanctum for SPA authentication
- Global search is fully reactive via the search store.
- Dark mode persists between sessions using VueUse composables.
