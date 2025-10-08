# TaskPad – Laravel 10 API + Nuxt 3 UI (Monorepo)

This is a simple task manager built in **one** repo:
- **Backend**: Laravel 10 (Sail + Sanctum)
- **Frontend**: Nuxt 3 (TypeScript + Tailwind + Pinia + Lucide)

---


## 🧱 Project Structure
/backend   → Laravel API (Sail + Sanctum)
/frontend  → Nuxt 3 app (Tailwind + Pinia)

## 🧰 Services Used

- **MySQL (via Laravel Sail)** – ✅ *Required*  
- **Redis** – ❌ *Not required for this project*  
  > This app doesn’t use queues, cache, or Redis-backed sessions.  
  > Sanctum authentication works fine using cookies and session driver defaults.  
  > If you ever add queues or caching later, you can enable Redis then.

---

## 🏗️ Getting Started