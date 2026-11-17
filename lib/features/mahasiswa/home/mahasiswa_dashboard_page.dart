import 'package:flutter/material.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/app_widgets.dart';
import '../../../services/api_service.dart';
import '../../../shared/models/models.dart';
import '../../../shared/pages/jadwal_page.dart';
import '../../auth/presentation/login_page.dart';
import '../riwayat_absensi/riwayat_page.dart';
import '../scan_qr/scan_qr_page.dart';

class MahasiswaDashboardPage extends StatefulWidget {
  const MahasiswaDashboardPage({super.key});

  @override
  State<MahasiswaDashboardPage> createState() => _MahasiswaDashboardPageState();
}

class _MahasiswaDashboardPageState extends State<MahasiswaDashboardPage> {
  late Future<_DashboardData> _dashboard;

  @override
  void initState() {
    super.initState();
    _loadDashboard();
  }

  void _loadDashboard() {
    _dashboard = _fetchDashboard();
  }

  Future<_DashboardData> _fetchDashboard() async {
    final values = await Future.wait<Object>([
      ApiService.getMe(),
      ApiService.getCourses(),
      ApiService.getHistory(),
    ]);

    return _DashboardData(
      user: values[0] as Map<String, dynamic>,
      courses: values[1] as List<Course>,
      history: values[2] as List<AttendanceRecord>,
    );
  }

  void _go(BuildContext context, Widget page) {
    Navigator.push(context, MaterialPageRoute(builder: (_) => page)).then((_) {
      if (mounted) setState(_loadDashboard);
    });
  }

  Future<void> _logout() async {
    try {
      await ApiService.logout();
      if (!mounted) return;
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const LoginPage()),
      );
    } catch (error) {
      if (!mounted) return;
      ScaffoldMessenger.of(context)
          .showSnackBar(SnackBar(content: Text('Gagal logout: $error')));
    }
  }

  bool _sudahAbsenHariIni(List<AttendanceRecord> history) {
    final now = DateTime.now();
    return history.any((record) {
      final date = record.date.toLocal();
      return date.year == now.year &&
          date.month == now.month &&
          date.day == now.day;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: FutureBuilder<_DashboardData>(
          future: _dashboard,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator());
            }
            if (snapshot.hasError) {
              return Center(
                child: Padding(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text('Gagal memuat dashboard: ${snapshot.error}'),
                      const SizedBox(height: 12),
                      OutlinedButton(
                        onPressed: () => setState(_loadDashboard),
                        child: const Text('Coba lagi'),
                      ),
                    ],
                  ),
                ),
              );
            }

            final data = snapshot.data!;
            final name = data.user['name']?.toString() ?? 'Mahasiswa';
            final nim = data.user['nim']?.toString() ?? '-';
            final todayCourses = data.courses
                .where((course) => course.isToday)
                .toList();
            final today = todayCourses.isEmpty ? null : todayCourses.first;
            final sudah = _sudahAbsenHariIni(data.history);

            return RefreshIndicator(
              onRefresh: () async => setState(_loadDashboard),
              child: ListView(
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 30),
                children: [
                  DashboardHeader(name: name, info: 'NIM $nim'),
                  const SizedBox(height: 25),
                  const SectionTitle('Absensi Hari Ini'),
                  AppCard(
                    padding: const EdgeInsets.all(18),
                    radius: 22,
                    child: Column(
                      children: [
                        IconBox(
                          icon: sudah
                              ? Icons.check_circle_outline
                              : Icons.qr_code_scanner,
                          color: sudah ? AppColors.success : AppColors.primary,
                          size: 65,
                          iconSize: 38,
                        ),
                        const SizedBox(height: 12),
                        Text(
                          sudah
                              ? 'Absensi berhasil'
                              : 'Belum melakukan absensi',
                          style: const TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 5),
                        Text(
                          sudah
                              ? 'Kehadiran Anda sudah tercatat'
                              : 'Scan QR Code untuk melakukan absensi',
                          style: const TextStyle(
                            color: Colors.grey,
                            fontSize: 12,
                          ),
                        ),
                        const SizedBox(height: 15),
                        PrimaryButton(
                          label: 'SCAN QR CODE',
                          icon: Icons.qr_code_scanner,
                          height: 48,
                          onPressed: () => _go(context, const ScanQrPage()),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 25),
                  const SectionTitle('Menu'),
                  Row(
                    children: [
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
                    ],
                  ),
                  const SizedBox(height: 25),
                  const SectionTitle('Jadwal Hari Ini'),
                  if (today == null)
                    const AppCard(child: Text('Belum ada jadwal dari backend.'))
                  else
                    AppCard(
                      child: Row(
                        children: [
                          const IconBox(icon: Icons.school),
                          const SizedBox(width: 13),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  today.name,
                                  style: const TextStyle(
                                    fontWeight: FontWeight.bold,
                                    fontSize: 14,
                                  ),
                                ),
                                const SizedBox(height: 5),
                                Text(
                                  '${today.day}, ${today.time}',
                                  style: const TextStyle(
                                    color: Colors.grey,
                                    fontSize: 12,
                                  ),
                                ),
                                const SizedBox(height: 3),
                                Text(
                                  'Ruang ${today.room}',
                                  style: const TextStyle(
                                    color: Colors.grey,
                                    fontSize: 11,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          sudah
                              ? const StatusChip(
                                  label: 'Hadir',
                                  color: AppColors.success,
                                )
                              : const StatusChip(
                                  label: 'Belum',
                                  color: AppColors.warnText,
                                  background: AppColors.warnBg,
                                ),
                        ],
                      ),
                    ),
                  const SizedBox(height: 25),
                  LogoutButton(onPressed: _logout),
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}

class _DashboardData {
  final Map<String, dynamic> user;
  final List<Course> courses;
  final List<AttendanceRecord> history;

  const _DashboardData({
    required this.user,
    required this.courses,
    required this.history,
  });
}
