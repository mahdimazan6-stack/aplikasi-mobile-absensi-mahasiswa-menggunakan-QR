import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/app_widgets.dart';
import '../../../shared/data/dummy_data.dart';
import '../../../shared/pages/jadwal_page.dart';
import '../../auth/presentation/login_page.dart';
import '../rekap_kehadiran/daftar_kehadiran_page.dart';
import '../sesi_absensi/buat_qr_page.dart';

class DosenDashboardPage extends StatelessWidget {
  const DosenDashboardPage({super.key});

  void _go(BuildContext context, Widget page) =>
      Navigator.push(context, MaterialPageRoute(builder: (_) => page));

  @override
  Widget build(BuildContext context) {
    final today = DummyData.courses.first;
    return Scaffold(
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(20, 20, 20, 30),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const DashboardHeader(name: DummyData.lecturerName, info: 'Dosen Pengampu'),
            const SizedBox(height: 25),
            const SectionTitle('Kelas Hari Ini'),
            AppCard(
              child: Row(children: [
                const IconBox(icon: Icons.school),
                const SizedBox(width: 13),
                Expanded(
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(today.name,
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                    const SizedBox(height: 5),
                    Text(today.time,
                        style: const TextStyle(color: Colors.grey, fontSize: 12)),
                    const SizedBox(height: 3),
                    Text('Ruang ${today.room}',
                        style: const TextStyle(color: Colors.grey, fontSize: 11)),
                  ]),
                ),
              ]),
            ),
            const SizedBox(height: 16),
            PrimaryButton(
              label: 'BUAT QR ABSENSI',
              icon: Icons.qr_code_2,
              onPressed: () => _go(context, BuatQrPage(course: today)),
            ),
            const SizedBox(height: 25),
            const SectionTitle('Menu'),
            Row(children: [
              Expanded(
                child: MenuCard(
                  icon: Icons.fact_check_outlined,
                  title: 'Rekap',
                  subtitle: 'Absensi',
                  color: AppColors.primary,
                  onTap: () => _go(context, const DaftarKehadiranPage()),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: MenuCard(
                  icon: Icons.calendar_month,
                  title: 'Jadwal',
                  subtitle: 'Kuliah',
                  color: AppColors.purple,
                  onTap: () => _go(context, const JadwalPage()),
                ),
              ),
            ]),
            const SizedBox(height: 25),
            LogoutButton(
              onPressed: () => Navigator.pushReplacement(
                context,
                MaterialPageRoute(builder: (_) => const LoginPage()),
              ),
            ),
          ]),
        ),
      ),
    );
  }
}
