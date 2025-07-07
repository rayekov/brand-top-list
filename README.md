# Brand Top List API

This is a RESTful API for managing brand toplists with geolocation-based configuration, built with Symfony 7 and featuring an admin panel.

## � Development Setup (Without Docker)

### Prerequisites
- PHP 8.3+
- Composer
- MySQL 8.0+
- Symfony CLI (optional but recommended)

### 1. Clone & Install
```bash
git clone <repository-url>
cd brand-top-list
composer install
```

### 2. Environment Configuration
```bash
# Copy environment file
cp .env .env.local

# Configure database in .env.local
DATABASE_URL="mysql://YOUR_USER:YOUR_PASS@127.0.0.1:3306/brand_toplist?serverVersion=8.0&charset=utf8mb4"
```

### 3. Database Setup
```bash
# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Load demo data
php bin/console doctrine:fixtures:load
```

### 4. Generate JWT Keys
```bash
php bin/console lexik:jwt:generate-keypair
```

### 5. Start Development Server
```bash
# Using Symfony CLI (recommended)
symfony server:start

# Or using PHP built-in server
php -S localhost:8000 -t public/
```

### 6. Access the Application
- **Frontend**: http://localhost:8000
- **Admin UI**: http://localhost:8000/admin.html
- **API Documentation**: http://localhost:8000/api/doc

### 7. Admin Authentication
- **Login Endpoint**: `POST http://localhost:8000/api/auth/login`
- **Username**: `admin`
- **Password**: `admin123`

## 🐳🚀 Quick Start (Docker) - Recommended

### Prerequisites
- Docker & Docker Compose installed
- Git

### 1. Clone & Start
```bash
git clone <repository-url>
cd brand-top-list
docker-compose up --build
```

**That's it!** The application will automatically:
- ✅ Set up the database and run migrations
- ✅ Load demo data (brands, countries, toplists)
- ✅ Generate JWT authentication keys
- ✅ Start all services

### 2. Access the Application
- **Frontend**: http://localhost:8011
- **Admin UI**: http://localhost:8011/admin.html
- **API Documentation**: http://localhost:8011/api/doc

### 3. Admin Authentication
- **Login Endpoint**: `POST http://localhost:8011/api/auth/login`
- **Username**: `admin`
- **Password**: `admin123`
- **Example**:
  ```bash
  curl -X POST http://localhost:8011/api/auth/login \
    -H 'Content-Type: application/json' \
    -d '{"username":"admin","password":"admin123"}'
  ```

## 📋 Features

### 🌍 Geolocation-Based Toplists
- Automatic country detection via CF-IPCountry header
- Fallback to default country (Cameroon)
- Country-specific brand rankings

### 🔐 Admin Interface
- Complete CRUD operations for brands, countries, and toplists
- JWT-based authentication
- Image upload support for brand logos
- Real-time filtering and search

### 📊 API Endpoints
- **Public**: Brand and country listings, geolocation-based toplists
- **Admin**: Full CRUD operations with authentication
- **Swagger Documentation**: Interactive API testing

## 🛠️ Technology Stack

- **Backend**: Symfony 7, PHP 8.3
- **Database**: MySQL 8.0
- **Authentication**: JWT (LexikJWTAuthenticationBundle)
- **API Documentation**: Swagger/OpenAPI (NelmioApiDocBundle)
- **Frontend**: Vanilla JavaScript, HTML5, CSS3
- **Containerization**: Docker & Docker Compose

## 📁 Project Structure

```
├── src/
│   ├── Controller/Api/     # API Controllers
│   ├── Entity/            # Doctrine Entities
│   ├── Service/           # Business Logic
│   └── DataFixtures/      # Demo Data
├── public/
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript
│   ├── *.html            # Frontend Pages
│   └── index.php         # Symfony Entry Point
├── config/               # Symfony Configuration
├── docker/               # Docker Configuration
└── migrations/           # Database Migrations
```

## 🔧 Development

### Local Development (without Docker)
```bash
# Install dependencies
composer install

# Setup database
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

# Generate JWT keys
php bin/console lexik:jwt:generate-keypair

# Start server
symfony server:start
```

### API Testing
1. **Get JWT Token**:
   ```bash
   curl -X POST http://localhost:8011/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"username":"admin","password":"admin123"}'
   ```

2. **Use Token**:
   ```bash
   curl -X GET http://localhost:8011/api/admin/brands \
     -H "Authorization: Bearer YOUR_TOKEN_HERE"
   ```

## 🌐 Frontend Features

### Public Interface
- **Responsive Design**: Mobile-first approach
- **Country Selection**: Manual country selection with auto-detection
- **Brand Display**: Clean card-based layout with ratings
- **Real-time Updates**: Dynamic content loading

### Admin Interface
- **Dashboard**: Overview of all entities
- **Brand Management**: Create, edit, delete brands with image upload
- **Country Management**: Manage available countries
- **Toplist Management**: Configure country-specific rankings
- **Filtering**: Real-time search and filtering capabilities

## 🔒 Security

- **JWT Authentication**: Secure token-based authentication
- **CORS Configuration**: Properly configured for API access
- **Input Validation**: Comprehensive validation on all inputs
- **SQL Injection Protection**: Doctrine ORM with prepared statements

## 📖 API Documentation

Visit http://localhost:8011/api/doc for interactive API documentation with:
- Complete endpoint listing
- Request/response examples
- Authentication testing
- Live API calls

## 🎯 Demo Data

The application includes demo data with:
- **5+ Countries**: Including Cameroon ❤️, France, USA, Nigeria, etc.
- **10+ Brands**: Sample brands
- **Sample Toplists**: Pre-configured rankings per country

## 🐛 Troubleshooting

### Common Issues

1. **Port 8011 already in use**:
   ```bash
   docker-compose down
   # Change port in compose.yaml if needed
   ```

2. **Database connection issues**:
   ```bash
   docker-compose logs db
   docker-compose restart db
   ```

3. **JWT Authentication errors**:
   ```bash
   # JWT keys are generated automatically, but if issues persist:
   docker-compose exec app php bin/console lexik:jwt:generate-keypair --skip-if-exists
   ```

4. **Permission issues**:
   ```bash
   docker-compose exec app chown -R www-data:www-data /var/www/html/var
   ```

### Logs
```bash
# Application logs
docker-compose logs app

# Database logs
docker-compose logs db

# Follow logs
docker-compose logs -f
```

## �📞 Support

For questions or issues, please check:
1. API Documentation: http://localhost:8011/api/doc
2. Application logs: `docker-compose logs app`
3. Database status: `docker-compose ps`

---
