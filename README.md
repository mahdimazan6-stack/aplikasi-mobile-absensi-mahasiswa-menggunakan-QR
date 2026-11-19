# absensi_mahasiswa

Flutter application for student attendance.

## Project structure

The project now contains both applications:

- `lib/` - Flutter student attendance application
- `android/`, `ios/`, `web/`, `linux/`, `macos/`, `windows/` - Flutter platform targets
- `backend/` - Laravel attendance backend source

## Connecting to the API

### Starting the Laravel backend

Create a MySQL database named `absensi_mahasiswa` in MySQL/phpMyAdmin, then
configure the connection values in `backend/.env` if your local MySQL host,
username, or password differs from the defaults in `.env.example`.

From the project root:

```sh
cd backend
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan admin:create
php artisan serve --host=0.0.0.0 --port=8000
```

`php artisan admin:create` securely prompts for an administrator name, email,
and password. The seeder adds local demo student/lecturer accounts and sample
courses; do not use those demo credentials in production.

The Android emulator uses `http://10.0.2.2:8000/api` by default. Start the app
without an `API_BASE_URL` override:

```sh
flutter run
```

If the computer's LAN IP changes, set `API_BASE_URL` to its current address.
For the Android Studio emulator, `10.0.2.2` can also be used to reach the host:

```sh
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api
```

For a physical device, use the computer's LAN IP and make sure both devices
are on the same network.

## Local demo accounts

After running `php artisan db:seed`, these accounts are available for local
development:

| Role | NIM | Password |
| --- | --- | --- |
| Mahasiswa | `3312511074` | `123456` |
| Dosen | `3312511138` | `123456` |

Do not use these demo credentials in production.
