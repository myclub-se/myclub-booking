# AGENTS

Repo root (for agents): `myclub-booking/` (this directory).

## Project overview

**WordPress plugin** (GPL v2) that integrates MyClub's bookable-resource system into WordPress sites. It fetches bookable items and time slots from the MyClub backend API and exposes them through a Gutenberg block, shortcode, and WordPress REST API. Users can view availability and submit bookings directly from the WordPress frontend.

- **Plugin name:** MyClub Booking
- **Text domain:** `myclub-booking`
- **PHP namespace:** `MyClub\MyClubBooking\` → `src/`
- **Shared library:** `myclub/common-lib` (installed to `lib/`)
- **Requires:** WordPress ≥ 6.4, PHP ≥ 7.4

## Key paths

| Path | Purpose |
|---|---|
| `myclub-booking.php` | Plugin entry point — defines constant, registers lifecycle hooks, calls `Services::register_services()` |
| `src/Services.php` | Service registry — instantiates and wires all service classes |
| `src/Api/RestApi.php` | Extends `BaseRestApi`; sets API key option name to `myclub_booking_api_key` |
| `src/Services/Admin.php` | Admin settings page, API key field, validation |
| `src/Services/Api.php` | Registers WordPress REST API endpoints |
| `src/Services/Blocks.php` | Registers the calendar Gutenberg block, enqueues FullCalendar |
| `src/Services/Base.php` | Base service — exposes `plugin_path` and `plugin_url` |
| `src/Activation.php` | Activation / deactivation / uninstall hooks |
| `blocks/src/calendar/` | Block source (React edit component, `render.php`, `block.json`) |
| `blocks/build/calendar/` | Compiled block assets |
| `templates/admin/` | PHP templates for admin settings UI |
| `lib/` | Composer dependencies (including `myclub/common-lib`) |

## REST API endpoints (`myclub/v1`)

| Method | Route | Auth | Description |
|---|---|---|---|
| GET | `/options` | `manage_options` | Plugin settings |
| GET | `/bookables` | `edit_posts` | All bookable items |
| GET | `/bookables/{id}/slots` | public | Available slots for a bookable (`?start_date=&end_date=`) |
| GET | `/bookables/{id}/slots/{slot_id}` | public | Single slot detail |
| POST | `/bookables/{id}/slots/{slot_id}/book` | public | Submit a booking (`email`, `first_name`, `last_name`, `start_time`, `end_time`) |

## Gutenberg block

**Block name:** `myclub-booking/calendar`
**Category:** `myclub`
**Attributes:** `bookable_id` (string), `post_id` (string)
**Render:** server-side via `blocks/src/calendar/render.php`
**Frontend:** FullCalendar v5.11.5 (daygrid + timegrid + list views)

The shortcode equivalent is `[myclub-booking-calendar]` with the same attributes.

## Architecture patterns

- **Service registry pattern** — `Services::register_services()` instantiates all service classes on `plugins_loaded`.
- **Extends shared library** — `RestApi` extends `MyClub\Common\Api\BaseRestApi`; sets `$apiKeyOptionName = 'myclub_booking_api_key'`.
- **No background sync** — booking data is fetched on demand (unlike myclub-groups which caches via cron).
- **Admin settings** use the standard WordPress Settings API with a single options group (`myclub_booking_settings_tab1`).

## Common commands

```bash
# PHP dependencies
composer install          # installs myclub/common-lib to lib/

# JS / block build
npm install
npm start                 # webpack dev server — watches blocks/src/ → blocks/build/
npm run build             # production build

# Linting
npm run lint:js
npm run lint:css
npm run format

# Package plugin as zip
npm run plugin-zip
```

## Admin settings

Single settings page with three tabs:
1. **General** — API key input (validated against the MyClub API on save)
2. **Gutenberg Blocks** — documentation for the calendar block
3. **Shortcodes** — documentation for `[myclub-booking-calendar]`

WordPress option key: `myclub_booking_api_key`
