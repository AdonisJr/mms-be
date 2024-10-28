
# General Services Monitoring and Management System (GSMMS)

The **General Services Monitoring and Management System (GSMMS)** is a Laravel-based application designed to streamline the management and monitoring of General Services within a school department. The app allows faculty members to request services, track preventive maintenance for equipment, manage inventory, and facilitate task updates from utility workers.

## Features

- **Service Request Management**: Faculty can submit service requests (e.g., repairs, cleaning) and track their status.
- **Preventive Maintenance**: Regular maintenance schedules and tasks assigned to personnel to ensure equipment remains in optimal condition.
- **Inventory Management**: CRUD functionality for tracking and managing general services equipment inventory.
- **User Roles**: Different access levels for administrators, personnel, and staff/faculty.
- **Task Management**: Personnel update task statuses and upload proof of completion.

## Requirements

- **PHP 8.1+**
- **Composer**
- **Laravel 11**
- **MariaDB/MySQL**
- **Node.js and npm** (for frontend dependencies)
- **Git** (for version control)
- **.env** file (set up with your database and app details)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/gsmms.git
cd gsmms-be
```

### 2. Install Dependencies

- **Install PHP dependencies**:

  ```bash
  composer install
  ```

- **Install JavaScript dependencies**:

  ```bash
  npm install
  ```

### 3. Environment Setup

- Duplicate the `.env.example` file and rename it as `.env`:

  ```bash
  cp .env.example .env
  ```

- **Configure the `.env` file** with your database details and any other environment variables (e.g., `APP_NAME`, `APP_URL`, `DB_CONNECTION`, `DB_DATABASE`, etc.).

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Database Setup

- **Create a new database** for the app in your preferred database manager (e.g., MariaDB/MySQL).
- **Run migrations** to set up the database tables:

  ```bash
  php artisan migrate
  ```

- **Seed the database** with initial data (if applicable):

  ```bash
  php artisan db:seed
  ```

### 6. Run the Development Server

To start the local development server, run:

```bash
php artisan serve
```

### 7. Running Additional Services

- **Compile assets** for development (hot reloading) or production:

  ```bash
  npm run dev   # for development
  npm run build # for production
  ```

The app should now be accessible at `http://localhost:8000`.

## Usage

1. **Login/Register**: Different user roles (Admin, Staff, Personnel) can log in and access role-specific features.
2. **Service Requests**: Staff members can submit requests, which are then managed by admins and personnel.
3. **Preventive Maintenance**: Regular equipment maintenance tasks are tracked, assigned, and completed.
4. **Inventory Management**: CRUD functionality allows the tracking of equipment details, including maintenance schedules and conditions.
5. **Task Updates**: Utility workers can update task statuses and upload proof of completion.

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request for review.

## License

This project is open-source and available under the [MIT License](LICENSE).

## Contact

For support or inquiries, please contact [your-email@example.com].

---

Thank you for using GSMMS!
