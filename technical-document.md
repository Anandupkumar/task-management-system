# Technical Implementation Document

## Project Overview

Build a production-ready AI-assisted Task Management System using Laravel with clean architecture principles.

This project MUST strictly follow:
- Repository Pattern
- Service Layer Architecture
- Policies & Authorization
- REST API standards
- AI Integration separation
- Clean folder structure
- Responsive UI implementation

---

# Mandatory Tech Stack

- Laravel 10+
- PHP 8.2+
- MySQL
- Blade + Tailwind CSS (Preferred)
- Laravel Breeze Authentication
- Chart.js
- REST APIs
- OpenAI / Gemini / Claude integration (real or mocked)

Recommended:
- Blade + Tailwind instead of Vue/Inertia for faster delivery and cleaner implementation.

---

# Critical Rules (DO NOT VIOLATE)

## Strictly Forbidden
- NO direct Eloquent calls inside Controllers
- NO business logic inside Controllers
- NO AI logic inside Controllers
- NO inline CSS
- NO skipping Repository Layer
- NO skipping Policies

Violating these will likely cause rejection.

---

# Required Folder Structure

```txt
app/
├── Http/
│   ├── Controllers/
│   │   ├── TaskController.php
│   │   ├── TaskApiController.php
│   │   ├── DashboardController.php
│   │
│   ├── Requests/
│   │   ├── StoreTaskRequest.php
│   │   ├── UpdateTaskRequest.php
│   │   ├── UpdateTaskStatusRequest.php
│   │
│   ├── Resources/
│   │   ├── TaskResource.php
│
├── Models/
│   ├── Task.php
│   ├── User.php
│
├── Repositories/
│   ├── Contracts/
│   │   ├── TaskRepositoryInterface.php
│   │
│   ├── Eloquent/
│   │   ├── TaskRepository.php
│
├── Services/
│   ├── TaskService.php
│   ├── AIService.php
│
├── Policies/
│   ├── TaskPolicy.php
│
├── Jobs/
│   ├── GenerateTaskAISummaryJob.php
│
├── Enums/
│   ├── TaskPriority.php
│   ├── TaskStatus.php
│
├── Providers/
│   ├── RepositoryServiceProvider.php
```

---

# Authentication & Roles

Use:
- Laravel Breeze

Roles:
- Admin
- User

Behavior:
- Admin → Full access to all tasks
- User → Only assigned tasks access

---

# Database Design

## users table

Add role field.

```php
$table->enum('role', ['admin', 'user'])
      ->default('user');
```

---

## tasks table

Fields:

| Field | Type |
|---|---|
| title | string |
| description | text |
| priority | enum(low, medium, high) |
| status | enum(pending, in_progress, completed) |
| due_date | date |
| assigned_to | foreign key user_id |
| ai_summary | text |
| ai_priority | enum(low, medium, high) |

---

# Repository Pattern (MANDATORY)

## TaskRepositoryInterface

```php
interface TaskRepositoryInterface
{
    public function all(array $filters = []);

    public function find(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);
}
```

---

# Service Layer (MANDATORY)

## TaskService Responsibilities

Must handle:
- Business logic
- Transactions
- Repository calls
- AI orchestration
- Queue dispatching

Controllers should call ONLY the service layer.

---

# AI Integration (STRICT)

## AIService Responsibilities

Must handle:
- Prompt creation
- API communication
- Response parsing
- Error handling
- Mock fallback

AI must NEVER be called directly from Controllers.

---

# Policies & Security

## TaskPolicy

Must implement:
- view
- update
- delete

Rules:
- Admin → all access
- User → only assigned tasks

---

# Form Requests (MANDATORY)

Required:
- StoreTaskRequest
- UpdateTaskRequest
- UpdateTaskStatusRequest

Validation must be implemented using Form Requests only.

---

# API Endpoints

| Method | Endpoint |
|---|---|
| GET | /api/tasks |
| POST | /api/tasks |
| PATCH | /api/tasks/{id}/status |
| GET | /api/tasks/{id}/ai-summary |

Requirements:
- API Resources
- Proper HTTP status codes
- JSON responses
- Validation
- Authorization

---

# Dashboard & Analytics

Dashboard must include:
- Total tasks
- Completed tasks
- Pending tasks
- High-priority tasks
- Chart.js chart

---

# UI Requirements

Required Pages:
1. Task List Page
2. Task Create/Edit Page
3. Task Detail + AI Summary Page

Requirements:
- Pixel-close implementation
- Fully responsive
- Proper spacing
- Proper typography hierarchy
- Tailwind CSS
- No inline CSS

---

# Bonus Features

Recommended:
- Queued AI Job
- Repository caching
- Feature tests
- Docker support
- Clean commits

---

# README Requirements

README MUST include:
- Installation steps
- Architecture explanation
- Repository pattern explanation
- Service layer explanation
- AI flow explanation
- AI prompt documentation
- API documentation
- Test credentials

---

# Auto-Reject Conditions

The following may cause rejection:
- No Repository Layer
- Direct Model usage in Controllers
- No AI integration
- No Policies
- Poor folder structure
- Fat Controllers
- Inline CSS
- Missing validation
- Missing authorization

---

# Recommended Final Stack

Use:
- Laravel 11
- Blade
- Tailwind
- Breeze
- Repository Pattern
- Service Layer
- Queue Job for AI
- Chart.js
- MySQL

This combination is optimal for:
- Fast delivery
- Clean architecture
- Senior-level code quality
- Maintainability
