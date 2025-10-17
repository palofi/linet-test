# Rentline Test API


## 🚀 Requirements

- **PHP 8.4**
- **Composer**
- **SQLite** (for database)

## 📦 Installation

### 1. Clone repository and navigate to directory
```bash
cd rentline-test
```

### 2. Install dependencies
```bash
composer install
```

### 3. Set up environment file
```bash
cp .env .env.local
```

### 4. Create database and run migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Import test data
```bash
php bin/console app:import-orders
```

## 🏃‍♂️ Running the project

### Start development server
```bash
php -S localhost:8080 -t public
```

Application will be available at: `http://localhost:8080`





