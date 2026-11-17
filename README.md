# absensi_mahasiswa

Flutter application for student attendance.

## Connecting to the API

The default API URL is `http://192.168.1.9:8000/api`. If using Laravel, start
the API server so it accepts emulator connections:

```sh
php artisan serve --host=0.0.0.0 --port=8000
```

Then run the app without an `API_BASE_URL` override:

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

Create the backend tables and demo data from the Laravel backend directory with:

```sh
php artisan migrate
php artisan db:seed --class="Database\Seeders\DatabaseSeeder"
```

For local development only:

| Role | NIM | Password |
| --- | --- | --- |
| Mahasiswa | `3312511074` | `123456` |
| Dosen | `3312511138` | `123456` |

Do not use these demo credentials in production.
