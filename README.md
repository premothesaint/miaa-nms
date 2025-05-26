# MIAA Local Management System

A web-based system for managing local telephone numbers and user administration for MIAA (Manila International Airport Authority). The system helps track and manage internal phone extensions and contact numbers within the organization.

## Features

- User Authentication and Authorization
- Admin and User Role Management
- Local Telephone Number Management
- User Approval System
- Activity Tracking
- Office-wise Local Number Directory

## System Requirements

- PHP (Web Server)
- MySQL Database
- Modern Web Browser

## Project Structure

```
├── admin_producer/      # Admin-specific functionalities
├── CSS/                 # Stylesheet files
├── database/            # Database configuration
├── images/              # System images and logos
├── user/                # User-specific functionalities
└── various PHP files    # Core system functionalities
```

## Key Files

- `index.php` - Main entry point
- `login.php` - Authentication handler
- `dashboard.php` - Main dashboard
- `manage_locals.php` - Local personnel management
- `manage_users.php` - User management interface
- `database/db.php` - Database connection configuration

## Setup Instructions

1. Set up a PHP-enabled web server
2. Import the database schema from `database.sql/miaa_locals.sql`
3. Configure database connection in `database/db.php`
4. Place the project files in your web server's root directory
5. Access the system through your web browser

## Default Admin Account

Use these credentials for initial system access:
- Username: `admin`
- Password: `misd@rea5`


## User Roles

### Admin
- Create/manage user accounts
- Approve new user registrations
- Manage local telephone numbers and extensions
- View system-wide activities

### Regular User
- View and manage assigned local telephone numbers
- Update personal information
- View office-specific local number directory

## Security Features

- Password-protected access
- Role-based access control
- User activity logging
- Account status management

## Contributing

For any system modifications or improvements, please coordinate with the system administrator.

## Support

For technical support or system-related inquiries, please contact Mark John I. Guillermo (markjohnguillermo47@gmail.com)