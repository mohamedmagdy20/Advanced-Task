# 📝 Advanced Task Management System (Laravel 10)

A task management system built with Laravel 10 and Sanctum. It supports authenticated users, email notifications, queued jobs, and a clean service-based architecture.

---

## 📖 Project Overview

This Laravel application enables users to manage their personal tasks through a RESTful API. It includes secure user authentication, email notifications for new tasks, and ownership-based access control. The logic is decoupled using a service layer and background jobs for scalability and maintainability.

---

##  Api Collection Link
- https://documenter.getpostman.com/view/23571272/2sB2x8EWEU#430a7f8e-b4d9-4298-82b6-d5b0282469c1


## ✨ Features

### ✅ Task Management (CRUD)
- Create, read, update, and soft delete tasks
- Each task includes title, description, due date, priority , and status
- Task statuses: `Pending`, `In Progress`, `Completed`, `Overdue`
- Task Priority : `Low` , `High` , `Medium`
- Retriving Data With Filter for Status , priority , Start Data , End Date
- Handle Validation in Status flow 

### ✅ Api Error Handler
- Handle All Error Response in one Class Handler `App\Exception\Handler.php`

### ✅ Email Notifications via Queued Jobs
- Tasks trigger notification emails through `SendNewTaskJob` , `SendTaskReminderJob`
- Uses Laravel Queues for asynchronous processing
- Efficient with large task sets using `chunk(100)`
- SendNewTaskJob : for Notified User for UpComing Tasks
- SendTaskReminderJob : for Notified User for Due Date Task before 24 hour

### ✅ Cron Job with Email Notifications 
- 2 Command running per Hour to Trigger Changes in Database
- `php artisan task:check-notifications` : for UpComing Tasks
- `php artisan task:check-due-date-notification` for Due Date Task before 24 hour


### ✅ User Authentication
- Secure login via Laravel Sanctum
- Tasks are user-specific and protected by ownership rules

### ✅ Service-Oriented Architecture
- Business logic separated into `TaskNotificationService`
- Keeps controller, command, and job code clean

### ✅ Authorization Logic
- Only task owners can delete their tasks
- Unauthorized actions return `403 Forbidden`

### ✅ Artisan Command Integration
- Custom command `task:check-notifications` dispatches notification job
- Can be run via scheduler (cron job) for automation

### ✅ Developer Tools
- Ready for Laravel Sail or native environment
- Pre-configured with Pint and PHPUnit
- Logs sent emails and exceptions for debugging

---

## 🧠 Design Decisions

### 🔧 Job Queue for Emails
- Sending emails via background jobs improves performance and user experience
- The job processes tasks in batches using `chunk()` to prevent memory issues

### 🔧 Service Layer Abstraction
- `TaskNotificationService` handles logic for getting new tasks and marking them as sent
- Promotes the Single Responsibility Principle (SRP)
- Easier testing and reuse across multiple components

### 🔧 Authorization Inside Service
- Access control (user owns task) is checked directly in service method
- Keeps logic centralized and avoids duplication across controllers/jobs

### 🔧 Command Separation
- Artisan command triggers jobs without direct coupling
- Enhances maintainability and allows automation via scheduler

### 🔧 Logging and Error Handling
- Email delivery success/failure is logged using Laravel’s `Log` facade
- Exceptions are caught and rethrown in jobs for monitoring

---

## ⚙️ Setup Instructions

```bash
# Clone the repo
git clone https://github.com/mohamedmagdy20/Advanced-Task.git
cd Advanced-Task

# Install PHP dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Set up database
php artisan migrate
php artisan db:seed

# Run queue worker
php artisan queue:work

# Run schedule
php artisan schedule:run

