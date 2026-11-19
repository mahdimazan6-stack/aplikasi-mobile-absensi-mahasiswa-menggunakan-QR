<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Kehadiran;
use Carbon\Carbon;

class AutoAlpaKaryawan extends Command
{
    // Ini nama perintah yang bakal kita ketik di terminal
    protected $signature = 'absen:alpa';

    // Deskripsi perintahnya
    protected $description = 'Otomatis menandai alpa karyawan yang tidak absen hari ini';

    public function handle()
    {
        $today = Carbon::today();

        // Ambil SEMUA akun yang role-nya karyawan/user
        $karyawans = User::where('role', 'user')->get();

        $count = 0;

        foreach ($karyawans as $karyawan) {
            // Cek apakah karyawan ini udah ada datanya di hari ini
            $sudahAbsen = Kehadiran::where('user_id', $karyawan->id)
                ->whereDate('tanggal', $today)
                ->exists();

            // Kalau belum absen sama sekali, kita bikinin data "alpa"
            if (!$sudahAbsen) {
                Kehadiran::create([
                    'user_id' => $karyawan->id,
                    'tanggal' => $today,
                    'check_in' => null,
                    'check_out' => null,
                    'status' => 'alpa'
                ]);
                $count++;
            }
        }

        // Ini cuma pesan balasan di terminal biar kita tau sukses/nggak
        $this->info("Berhasil menandai $count karyawan sebagai Alpa untuk hari ini.");
    }
}