# EclipsePHP World Plugin Workbench

<div style="text-align: center">

[![PHP Version](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x-6366F1?logo=filament&logoColor=white)](https://filamentphp.com)

</div>

## About

This workbench provides a complete Laravel application environment for developing and testing the **EclipsePHP World Plugin**. It includes a full Laravel application skeleton with all necessary configurations for plugin development and testing.

### Features

- 🚀 **Complete Laravel Environment** - Full application skeleton for testing
- 🔧 **Plugin Development** - Pre-configured for EclipsePHP World Plugin development
- 🧪 **Testing Ready** - Includes Pest testing framework and all necessary tools
- 📦 **Dependency Management** - Composer and npm dependencies pre-configured
- 🐳 **Lando Integration** - Docker-based development environment

## Quick Start

### Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js and npm
- Lando

### Installation

1. **Navigate to the world plugin directory:**
   ```bash
   cd packages/eclipsephp-world-plugin
   ```

2. **Enter the workbench:**
   ```bash
   cd workbench
   ```

3. **Install dependencies:**
   ```bash
   composer install
   ```

4. **Return to plugin directory:**
   ```bash
   cd ../
   ```

5. **Build the plugin:**
   ```bash
   lando rebuild -y
   ```

## Development Workflow

1. Make changes to the plugin code in the parent directory
2. Use the workbench to test your changes
3. Run `lando rebuild -y` to rebuild the plugin after changes
4. Test the changes in the workbench environment

## Available Commands

| Command | Description |
|---------|-------------|
| `composer install` | Install PHP dependencies |
| `lando rebuild -y` | Build the plugin (run from parent directory) |
| `php artisan serve` | Start the development server |
| `npm install` | Install frontend dependencies |
| `npm run dev` | Build frontend assets |
| `vendor/bin/pest` | Run tests |
| `vendor/bin/pint` | Format code |

## Environment Setup

The workbench uses the same environment configuration as the main application:

1. Copy `.env.example` to `.env` if it doesn't exist
2. Configure your database settings
3. Generate application key: `php artisan key:generate`

## Testing

Run the test suite using Pest:

```bash
vendor/bin/pest
```

## Code Quality

Format your code using Laravel Pint:

```bash
vendor/bin/pint
```

---

**Built with ❤️ by [DataLinx](https://www.datalinx.si/)** 