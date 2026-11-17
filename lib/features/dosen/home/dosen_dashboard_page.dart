import 'package:flutter/material.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/utils/format.dart';
import '../../../core/widgets/app_widgets.dart';
import '../../../services/api_service.dart';
import '../../../shared/models/models.dart';
import '../../../shared/pages/jadwal_page.dart';
import '../../auth/presentation/login_page.dart';
import '../rekap_kehadiran/daftar_kehadiran_page.dart';
import '../sesi_absensi/buat_qr_page.dart';

class DosenDashboardPage extends StatefulWidget {
  const DosenDashboardPage({super.key});

  @override
  State<DosenDashboardPage> createState() => _DosenDashboardPageState();
}

class _DosenDashboardPageState extends State<DosenDashboardPage> {
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
    ]);
    return _DashboardData(
      user: values[0] as Map<String, dynamic>,
      courses: values[1] as List<Course>,
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
            final courses = data.courses;
            final hariIni = courses.where((course) => course.isToday).toList()
              ..sort((a, b) => a.mulaiMenit.compareTo(b.mulaiMenit));
            final utama = hariIni.isEmpty
                ? (courses.isEmpty
                      ? null
                      : courses.firstWhere(
                          (course) => course.isOngoing,
                          orElse: () => courses.first,
                        ))
                : hariIni.firstWhere(
                    (course) => course.isOngoing,
                    orElse: () => hariIni.first,
                  );

            return RefreshIndicator(
              onRefresh: () async => setState(_loadDashboard),
              child: ListView(
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 30),
                children: [
                  DashboardHeader(
                    name: data.user['name']?.toString() ?? 'Dosen',
                    info: 'Dosen Pengampu',
                  ),
                  const SizedBox(height: 25),
                  Padding(
                    padding: const EdgeInsets.only(bottom: 12),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        const Text(
                          'Kelas Hari Ini',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: AppColors.ink,
                          ),
                        ),
                        Text(
                          tanggalPanjang(DateTime.now()),
                          style: const TextStyle(
                            color: Colors.grey,
                            fontSize: 12,
                          ),
                        ),
                      ],
                    ),
                  ),
                  if (hariIni.isEmpty)
                    _kosong(courses.isEmpty)
                  else
                    for (final course in hariIni) ...[
                      _kelasCard(course),
                      if (course != hariIni.last) const SizedBox(height: 12),
                    ],
                  if (utama != null) ...[
                    const SizedBox(height: 16),
                    PrimaryButton(
                      label: 'BUAT QR ABSENSI',
                      icon: Icons.qr_code_2,
                      onPressed: () => _go(
                        context,
                        BuatQrPage(course: utama, courses: courses),
                      ),
                    ),
                    const SizedBox(height: 25),
                    const SectionTitle('Menu'),
                    Row(
                      children: [
                        Expanded(
                          child: MenuCard(
                            icon: Icons.fact_check_outlined,
                            title: 'Rekap',
                            subtitle: 'Absensi',
                            color: AppColors.primary,
                            onTap: () => _go(
                              context,
                              DaftarKehadiranPage(course: utama),
                            ),
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
                      ],
                    ),
                  ],
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

  Widget _kelasCard(Course course) {
    final badge = courseBadge(course);
    return AppCard(
      child: Row(
        children: [
          const IconBox(icon: Icons.school),
          const SizedBox(width: 13),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Flexible(
                      child: Text(
                        course.name,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 14,
                        ),
                      ),
                    ),
                    if (badge != null) ...[const SizedBox(width: 6), badge],
                  ],
                ),
                const SizedBox(height: 5),
                Text(
                  course.time,
                  style: const TextStyle(color: Colors.grey, fontSize: 12),
                ),
                const SizedBox(height: 3),
                Text(
                  'Ruang ${course.room}  •  ${course.kelas}',
                  style: const TextStyle(color: Colors.grey, fontSize: 11),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _kosong(bool noCourses) => AppCard(
    padding: const EdgeInsets.all(22),
    child: Column(
      children: [
        const IconBox(icon: Icons.event_available, size: 56, iconSize: 30),
        const SizedBox(height: 12),
        Text(
          noCourses ? 'Belum ada mata kuliah' : 'Tidak ada kelas hari ini',
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
        ),
        const SizedBox(height: 4),
        Text(
          noCourses
              ? 'Tambahkan mata kuliah pada backend terlebih dahulu.'
              : 'Tidak ada jadwal kuliah hari ini.',
          style: const TextStyle(color: Colors.grey, fontSize: 12),
          textAlign: TextAlign.center,
        ),
      ],
    ),
  );
}

class _DashboardData {
  final Map<String, dynamic> user;
  final List<Course> courses;

  const _DashboardData({required this.user, required this.courses});
}

Widget? courseBadge(Course course) {
  if (course.isOngoing) {
    return const StatusChip(label: 'BERLANGSUNG', color: AppColors.success);
  }
  if (course.isToday) {
    return const StatusChip(label: 'HARI INI', color: AppColors.primary);
  }
  return null;
}
