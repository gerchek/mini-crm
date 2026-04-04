# Mini CRM

Mini-CRM for collecting and processing customer feedback tickets via an embeddable widget.

## Tech Stack

- PHP 8.4 + Laravel 12 (^12.0)
- MySQL 8.0
- Docker + Docker Compose
- spatie/laravel-permission (roles)
- spatie/laravel-medialibrary (file attachments)

## Quick Start

```bash
# Clone the repository
git clone <repo-url> mini-crm
cd mini-crm

# Start containers
docker compose up -d

# Run migrations and seed data
docker compose exec app php artisan migrate --seed

# Create storage symlink
docker compose exec app php artisan storage:link
```

The application will be available at: **http://localhost:8080**

## Test Credentials

| Role    | Email               | Password  |
|---------|---------------------|-----------|
| Manager | manager@example.com | password  |
| Admin   | admin@example.com   | password  |

Seeder also creates 5 customers with 2-5 tickets each.

## API Endpoints

### Create Ticket

```
POST /api/tickets
Content-Type: multipart/form-data
```

| Field     | Type     | Required | Description              |
|-----------|----------|----------|--------------------------|
| name      | string   | yes      | Customer name            |
| phone     | string   | yes      | Phone in E.164 format    |
| email     | string   | yes      | Customer email           |
| subject   | string   | yes      | Ticket subject           |
| body      | string   | yes      | Ticket message           |
| files[]   | file[]   | no       | Attachments (max 5 files, 10MB each) |

**Rate limit:** 1 ticket per day per email/phone.

Example:
```bash
curl -X POST http://localhost:8080/api/tickets \
  -H "Accept: application/json" \
  -F "name=John Doe" \
  -F "phone=+12025551234" \
  -F "email=john@example.com" \
  -F "subject=Question" \
  -F "body=Hello, I have a question."
```

### Ticket Statistics

```
GET /api/tickets/statistics
```

Response:
```json
{
  "today": 5,
  "week": 12,
  "month": 34
}
```

## Widget (Embeddable Form)

Available at: **http://localhost:8080/widget**

Embed on any website:
```html
<iframe src="http://localhost:8080/widget" width="520" height="700" frameborder="0"></iframe>
```

## Admin Panel

Available at: **http://localhost:8080/admin/tickets** (login required)

Features:
- View tickets list with pagination
- Filter by status, email, phone, date range
- View ticket details with attachments
- Change ticket status (new / in progress / processed)

## Running Tests

```bash
docker compose exec app php artisan test
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/TicketController.php      # API endpoints
│   │   ├── Admin/TicketController.php    # Admin panel
│   │   ├── Auth/LoginController.php      # Authentication
│   │   └── WidgetController.php          # Widget page
│   ├── Requests/StoreTicketRequest.php   # Validation
│   └── Resources/                        # API Resources
├── Models/
│   ├── User.php
│   ├── Customer.php
│   └── Ticket.php
└── Services/TicketService.php            # Business logic
```
