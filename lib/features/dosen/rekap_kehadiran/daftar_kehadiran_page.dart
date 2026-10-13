
import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/format.dart';
import '../../../core/widgets/app_widgets.dart';
import '../../../shared/data/dummy_data.dart';
import '../../../shared/models/models.dart';

class DaftarKehadiranPage extends StatefulWidget {
  final Course? course;
  const DaftarKehadiranPage({super.key, this.course});

  @override
  State<DaftarKehadiranPage> createState() => _DaftarKehadiranPageState();
}

class _DaftarKehadiranPageState extends State<DaftarKehadiranPage> {
  late Course _course;

  @override
  void initState() {
    super.initState();
    _course = widget.course ?? DummyData.courses.first;
  }

  void _info(String msg) =>
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));

  Future<void> _pilihMatkul() async {
    final hasil = await showModalBottomSheet<Course>(
      context: context,
      backgroundColor: Colors.white,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(25)),
      ),
      builder: (_) => _PilihMatkulSheet(selected: _course),
    );

    if (!mounted) return;
    if (hasil != null && hasil.id != _course.id) {
      setState(() => _course = hasil);
    }
  }

  Widget _counter(String label, int n, Color c) => Expanded(
    child: Column(children: [
      Text('$n', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: c)),
      const SizedBox(height: 2),
      Text(label, style: const TextStyle(color: Colors.grey, fontSize: 12)),
    ]),
  );

  Widget _kartuMatkul() => AppCard(
    onTap: _pilihMatkul,
    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Row(children: [
        const IconBox(icon: Icons.school),
        const SizedBox(width: 13),
        Expanded(
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Mata Kuliah', style: TextStyle(color: Colors.grey, fontSize: 12)),
            const SizedBox(height: 3),
            Text(_course.name, style: const TextStyle(
              fontWeight: FontWeight.bold, fontSize: 15, color: AppColors.ink,
            )),
          ]),
        ),
        const Icon(Icons.unfold_more_rounded, color: Colors.grey),
      ]),
      const SizedBox(height: 14),
      Wrap(spacing: 8, runSpacing: 8, children: [
        _InfoChip(Icons.calendar_today_outlined, _course.day),
        _InfoChip(Icons.schedule, _course.time),
        _InfoChip(Icons.meeting_room_outlined, _course.room),
        if (_course.kelas.isNotEmpty) _InfoChip(Icons.groups_outlined, _course.kelas),
      ]),
    ]),
  );

  @override
  Widget build(BuildContext context) {
    final state = AppState.instance;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Daftar Kehadiran'),
        actions: [
          IconButton(
            tooltip: 'Perbarui data',
            icon: const Icon(Icons.refresh),
            onPressed: () => _info('Data diperbarui'),
          ),
        ],
      ),
      body: ListenableBuilder(
        listenable: state,
        builder: (context, _) => Padding(
          padding: const EdgeInsets.fromLTRB(20, 8, 20, 20),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            _kartuMatkul(),
            const SizedBox(height: 14),
            Text(tanggalPanjang(DateTime.now()),
                style: const TextStyle(color: Colors.grey, fontSize: 13)),
            const SizedBox(height: 16),
            AppCard(
              child: Row(children: [
                _counter('Hadir', state.count(AttendStatus.hadir), AppColors.success),
                _counter('Tidak Hadir', state.count(AttendStatus.tidak), AppColors.danger),
              ]),
            ),
            const SizedBox(height: 16),
            const Text('Daftar Mahasiswa', style: TextStyle(
              fontSize: 15, fontWeight: FontWeight.bold, color: AppColors.ink,
            )),
            const SizedBox(height: 12),
            Expanded(
              child: AppCard(
                padding: const EdgeInsets.symmetric(vertical: 6),
                child: ListView.separated(
                  itemCount: state.students.length,
                  separatorBuilder: (_, _) => Divider(
                    height: 1, color: Colors.grey.withValues(alpha: 0.2),
                  ),
                  itemBuilder: (_, i) {
                    final s = state.students[i];
                    return ListTile(
                      leading: CircleAvatar(
                        backgroundColor: AppColors.tint,
                        child: Text(s.name[0], style: const TextStyle(
                          color: AppColors.primary, fontWeight: FontWeight.bold,
                        )),
                      ),
                      title: Text(s.name, style: const TextStyle(
                        fontWeight: FontWeight.w600, fontSize: 14,
                      )),
                      trailing: StatusChip(label: s.status.label, color: s.status.color),
                    );
                  },
                ),
              ),
            ),
            const SizedBox(height: 12),
            PrimaryButton(
              label: 'EXPORT REKAP',
              icon: Icons.download_outlined,
              onPressed: () => _info('Ekspor tersedia setelah backend terhubung'),
            ),
          ]),
        ),
      ),
    );
  }
}

// Bottom sheet untuk memilih mata kuliah
class _PilihMatkulSheet extends StatelessWidget {
  final Course selected;
  const _PilihMatkulSheet({required this.selected});

  @override
  Widget build(BuildContext context) {
    final daftar = [...DummyData.courses]
      ..sort((a, b) => (b.isToday ? 1 : 0).compareTo(a.isToday ? 1 : 0));

    return SafeArea(
      child: Padding(
        padding: const EdgeInsets.fromLTRB(20, 12, 20, 16),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(
            width: 42, height: 4,
            decoration: BoxDecoration(
              color: Colors.grey.shade300,
              borderRadius: BorderRadius.circular(4),
            ),
          ),
          const SizedBox(height: 16),
          const Align(
            alignment: Alignment.centerLeft,
            child: Text('Pilih Mata Kuliah', style: TextStyle(
              fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.ink,
            )),
          ),
          const SizedBox(height: 12),
          Flexible(
            child: ListView.separated(
              shrinkWrap: true,
              itemCount: daftar.length,
              separatorBuilder: (_, _) => const SizedBox(height: 8),
              itemBuilder: (context, i) {
                final c = daftar[i];
                final dipilih = c.id == selected.id;

                return Material(
                  color: dipilih ? AppColors.tint : Colors.transparent,
                  borderRadius: BorderRadius.circular(16),
                  child: InkWell(
                    borderRadius: BorderRadius.circular(16),
                    onTap: () => Navigator.pop(context, c),
                    child: Padding(
                      padding: const EdgeInsets.all(12),
                      child: Row(children: [
                        const IconBox(icon: Icons.school),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            Text(c.name, style: const TextStyle(
                              fontWeight: FontWeight.bold, fontSize: 14,
                            )),
                            const SizedBox(height: 4),
                            Text('${c.day}, ${c.time}', style: const TextStyle(
                              color: Colors.grey, fontSize: 12,
                            )),
                            const SizedBox(height: 2),
                            Text('${c.room} • ${c.kelas}', style: const TextStyle(
                              color: Colors.grey, fontSize: 11,
                            )),
                          ]),
                        ),
                        Icon(
                          dipilih ? Icons.check_circle : Icons.radio_button_unchecked,
                          color: dipilih ? AppColors.primary : Colors.grey.shade400,
                        ),
                      ]),
                    ),
                  ),
                );
              },
            ),
          ),
        ]),
      ),
    );
  }
}

// Chip informasi mata kuliah
class _InfoChip extends StatelessWidget {
  final IconData icon;
  final String text;
  const _InfoChip(this.icon, this.text);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(10),
      ),
      child: Row(mainAxisSize: MainAxisSize.min, children: [
        Icon(icon, size: 14, color: Colors.grey.shade600),
        const SizedBox(width: 5),
        Text(text, style: TextStyle(fontSize: 12, color: Colors.grey.shade700)),
      ]),
    );
  }
}
