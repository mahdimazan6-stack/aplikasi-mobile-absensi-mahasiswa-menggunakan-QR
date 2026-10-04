import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/app_widgets.dart';
import '../../../shared/data/dummy_data.dart';
import '../../../shared/pages/jadwal_page.dart';
import '../../auth/presentation/login_page.dart';
import '../riwayat_absensi/riwayat_page.dart';
import '../scan_qr/scan_qr_page.dart';

class MahasiswaDashboardPage extends StatelessWidget {
  const MahasiswaDashboardPage({super.key});

  void _go(BuildContext context, Widget page) =>
      Navigator.push(context, MaterialPageRoute(builder: (_) => page));

  @override
  Widget build(BuildContext context) {
    final today = DummyData.courses.first;
    return Scaffold(
      body: SafeArea(
        child: ListenableBuilder(
          listenable: AppState.instance,
          builder: (context, _) {
            final sudah = AppState.instance.sudahAbsenHariIni;
            return SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(20, 20, 20, 30),
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                const DashboardHeader(
                  name: DummyData.studentName,
                  info: 'NIM ${DummyData.studentNim}',
                ),
                const SizedBox(height: 25),
                const SectionTitle('Absensi Hari Ini'),
                AppCard(
                  padding: const EdgeInsets.all(18),
                  radius: 22,
                  child: Column(children: [
                    IconBox(
                      icon: sudah ? Icons.check_circle_outline : Icons.qr_code_scanner,
                      color: sudah ? AppColors.success : AppColors.primary,
                      size: 65,
                      iconSize: 38,
                    ),
                    const SizedBox(height: 12),
                    Text(sudah ? 'Absensi berhasil' : 'Belum melakukan absensi',
                        style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 5),
                    Text(
                      sudah
                          ? 'Kehadiran Anda sudah tercatat'
                          : 'Scan QR Code untuk melakukan absensi',
                      style: const TextStyle(color: Colors.grey, fontSize: 12),
                    ),
                    const SizedBox(height: 15),
                    PrimaryButton(
                      label: 'SCAN QR CODE',
                      icon: Icons.qr_code_scanner,
                      height: 48,
                      onPressed: () => _go(context, const ScanQrPage()),
                    ),
                  ]),
                ),
                const SizedBox(height: 25),
                const SectionTitle('Menu'),
                Row(children: [
                  Expanded(
                    child: MenuCard(
                      icon: Icons.calendar_month,
                      title: 'Jadwal',
                      subtitle: 'Kuliah',
                      color: AppColors.primary,
                      onTap: () => _go(context, const JadwalPage()),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: MenuCard(
                      icon: Icons.history,
                      title: 'Riwayat',
                      subtitle: 'Absensi',
                      color: AppColors.purple,
                      onTap: () => _go(context, const RiwayatPage()),
                    ),
                  ),
                ]),
                const SizedBox(height: 25),
                const SectionTitle('Jadwal Hari Ini'),
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
                    sudah
                        ? const StatusChip(label: 'Hadir', color: AppColors.success)
                        : const StatusChip(
                            label: 'Belum',
                            color: AppColors.warnText,
                            background: AppColors.warnBg,
                          ),
                  ]),
                ),
                const SizedBox(height: 25),
                LogoutButton(
                  onPressed: () => Navigator.pushReplacement(
                    context,
                    MaterialPageRoute(builder: (_) => const LoginPage()),
                  ),
                ),
              ]),
            );
          },
        ),
      ),
    );
  }
}
