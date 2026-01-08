# Simple E-commerce Shopping Cart

A simple e-commerce shopping cart system built with Laravel, React, and Tailwind CSS.

## Features

- Browse products with stock information
- Add products to cart (authenticated users only)
- Update cart item quantities
- Remove items from cart
- Clear entire cart
- Checkout to create orders
- Low stock email notifications (via Laravel Jobs/Queues)
- Daily sales report emails (scheduled job)

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: React + Inertia.js
- **Styling**: Tailwind CSS + Shadcn UI
- **Database**: SQLite (default)

## Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm

### Installation

1. Clone the repository:

```bash
git clone <repository-url>
cd task-simple-cart-edward
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install Node.js dependencies:

```bash
npm install
```

4. Copy environment file:

```bash
cp .env.example .env
```

5. Generate application key:

```bash
php artisan key:generate
```

6. Configure environment variables (optional):
   Edit `.env` and set:

```env
LOW_STOCK_THRESHOLD=10
ADMIN_EMAIL=admin@example.com
```

7. Run migrations and seed database:

```bash
php artisan migrate
php artisan db:seed
```

This will create:

- 60 sample products
- A test user (email: `comgeek71@gmail.com`, password: `password`)

## Running the Application

Start the development server:

```bash
composer run dev
```

This command runs the following processes concurrently:

- Laravel development server (`php artisan serve`)
- Vite development server (`npm run dev`)
- Queue worker (`php artisan queue:listen`) - for processing email notifications
- Scheduler (`php artisan schedule:work`) - for daily sales reports

Visit the application:

- Open http://localhost:8000 in your browser

## Testing

Run the test suite:

```bash
php artisan test
```

## Additional Commands

Generate daily sales report manually:

```bash
php artisan sales:generate-daily-report
```

## Default Test User

- **Email**: comgeek71@gmail.com
- **Password**: password
