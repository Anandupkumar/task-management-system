# AI-Assisted Task Management System

A production-ready Task Management System built with Laravel 12, featuring an AI-assisted summary generation, Repository and Service layer patterns, and comprehensive Role-Based Access Control (RBAC).

## Key Features

- **Clean Architecture:** Strict adherence to the Repository and Service layer patterns.
- **AI Integration:** Automatically generates task summaries and priority suggestions using OpenAI/Gemini (with a built-in mock fallback for testing).
- **Role-Based Access Control (RBAC):** Admin (full access) and User (assigned tasks only).
- **REST API:** Fully featured API endpoints with `TaskResource` transformations.
- **Asynchronous Processing:** AI summaries are generated via queued jobs (`GenerateTaskAISummaryJob`).

---

## Installation & Setup

1. **Clone the repository and install dependencies:**
   ```bash
   composer install
   npm install && npm run build
   ```

2. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Ensure you have a MySQL database created (e.g., `task_manager`). Update the `.env` file with your database credentials.

   Optional AI configuration (leave empty to use the built-in mock):
   ```env
   AI_API_KEY=your-api-key
   AI_API_URL=https://your-ai-provider-endpoint
   ```

3. **Migrate and Seed Database:**
   ```bash
   php artisan migrate --seed
   ```
   This will create an Admin user, a Regular User, and 10 sample tasks.

4. **Run Queue Worker** (required for AI summaries after task creation):
   ```bash
   php artisan queue:work --tries=3
   ```

   AI summaries are generated asynchronously when tasks are **created** (not for tasks that already existed before the worker ran). If you seeded the database before starting the worker, backfill missing summaries:
   ```bash
   php artisan tasks:generate-ai-summaries
   ```

5. **Start Development Server:**
   ```bash
   php artisan serve
   ```

---

## Test Credentials

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@example.com` | `password` |
| **User** | `user@example.com` | `password` |

---

## Architecture Overview

- **Repository Pattern (`app/Repositories`):** Abstracts data access. `TaskRepository` handles queries and enforces user-scoping (users only see their assigned tasks).
- **Service Layer (`app/Services`):** Business logic is decoupled from controllers. `TaskService` handles transactions and orchestrates jobs.
- **Policies (`app/Policies/TaskPolicy.php`):** Ensures authorization rules are strictly applied.
- **Form Requests (`app/Http/Requests`):** Validates all incoming data payload securely.

---

## AI Prompt Strategy

The system utilizes a queued job to request task analysis from an AI service. The prompt strategy focuses on structured JSON output for predictable parsing.

**Prompt Template:**
```text
You are a task analysis assistant. Analyze the task and respond ONLY with valid JSON.

Task Title: {title}
Description: {description}
Priority: {priority}
Due Date: {due_date}

Respond with:
{
  "ai_summary": "A concise 1-2 sentence summary",
  "ai_priority": "low|medium|high"
}
```

*Note: If no AI key is configured (`config('services.ai.key')`), the `AIService` automatically falls back to a mocked response for seamless local testing.*

---

## REST API Documentation

API endpoints are protected by standard session-based auth (`auth` middleware) for this implementation.

| Method | Endpoint | Description | Status Codes |
|---|---|---|---|
| `GET` | `/api/tasks` | List paginated tasks (scoped by user role) | `200` |
| `POST` | `/api/tasks` | Create a new task (Admin only) | `201`, `403`, `422` |
| `PATCH` | `/api/tasks/{task}/status` | Update task status (Admin or Assigned User) | `200`, `403`, `422` |
| `GET` | `/api/tasks/{task}/ai-summary` | Retrieve AI summary from DB | `200`, `403`, `404` |
