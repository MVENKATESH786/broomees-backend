
```markdown
# Broomees Backend Assignment - User Reputation System

A secure, scalable REST API built with **Laravel 11** and **PHP 8.2+**. This system manages users, mutual relationships, and computes reputation scores while enforcing strict rate limiting, concurrency safety, and data integrity.

## 🚀 Features

* **Reputation Algorithm:** Dynamically calculated score based on friends, shared hobbies, and account age.
* **Concurrency Safety:** Implemented **Optimistic Locking** (versioning) to prevent race conditions during user updates.
* **Data Integrity:** DB-level transactions ensure mutual relationships (User A adding User B automatically links B to A).
* **Security:** Token-based authentication (hashed storage) and Rate Limiting (Throttling).
* **Architecture:** Service-Repository Pattern for clean separation of concerns.

## 🛠 Tech Stack

* **Framework:** Laravel 11
* **Language:** PHP 8.2+
* **Database:** MySQL
* **Testing:** PHPUnit

---

## ⚙️ Setup Instructions

### 1. Clone & Install
```bash
git clone https://github.com/MVENKATESH786/broomees-backend.git
cd broomees-backend
composer install

```

### 2. Environment Configuration

Copy the example environment file and configure your database:

```bash
cp .env.example .env

```

Open `.env` and update your database credentials:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=broomees
DB_USERNAME=root
DB_PASSWORD=

```

### 3. Initialize Application

Run the following commands to generate the key and set up the database:

```bash
php artisan key:generate
php artisan migrate:fresh

```

### 4. Run the Server

```bash
php artisan serve

```

The API will be available at `http://127.0.0.1:8000`.

---

## 📚 API Documentation

**Base URL:** `http://127.0.0.1:8000/api`

### 1. Authentication

* **Endpoint:** `POST /auth/token`
* **Response:** `{ "token": "your-access-token" }`
* **Usage:** Include this token in the header of all subsequent requests:
`Authorization: Bearer <your-token>`

### 2. Users

| Method | Endpoint | Description | Body Parameters |
| --- | --- | --- | --- |
| **GET** | `/users` | List all users | N/A |
| **POST** | `/users` | Create a User | `{ "username": "alice", "age": 25 }` |
| **GET** | `/users/{id}` | Get Details | N/A |
| **PUT** | `/users/{id}` | Update User | `{ "age": 26, "version": 1 }` |
| **DELETE** | `/users/{id}` | Delete User | N/A |

### 3. Relationships & Hobbies

| Method | Endpoint | Description | Body Parameters |
| --- | --- | --- | --- |
| **POST** | `/users/{id}/relationships` | Add Friend (Mutual) | `{ "friend_id": "uuid" }` |
| **DELETE** | `/users/{id}/relationships` | Remove Friend | `{ "friend_id": "uuid" }` |
| **POST** | `/users/{id}/hobbies` | Add Hobby | `{ "name": "Chess" }` |

### 4. System Metrics

* **Endpoint:** `GET /metrics/reputation`
* **Description:** Returns the system-wide average reputation score.

---

## 🧪 Testing

### Automated Tests

Run the PHPUnit test suite to verify concurrency logic, rate limiting, and score calculation:

```bash
php artisan test

```

### Manual Testing Guide (Curl)

**1. Get Token**

```bash
curl -X POST [http://127.0.0.1:8000/api/auth/token](http://127.0.0.1:8000/api/auth/token)

```

**2. Create User**

```bash
curl -X POST [http://127.0.0.1:8000/api/users](http://127.0.0.1:8000/api/users) \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"username": "Alice", "age": 25}'

```

**3. Test Optimistic Locking (Conflict)**
Attempt to update a user with an incorrect `version`:

```bash
curl -X PUT [http://127.0.0.1:8000/api/users/](http://127.0.0.1:8000/api/users/)<UUID> \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"age": 26, "version": 0}'

```

*Expected Result: 409 Conflict*

---

## 📂 Project Structure

* `app/Http/Controllers`: API Controllers (User, Relationship, Hobby, Auth).
* `app/Services`: Business logic (ReputationService, UserService).
* `app/Repositories`: Database interactions and Transaction management.
* `app/Models`: Eloquent models with UUIDs and relationships.
* `tests/Feature`: Automated tests for concurrency and logic.

```
