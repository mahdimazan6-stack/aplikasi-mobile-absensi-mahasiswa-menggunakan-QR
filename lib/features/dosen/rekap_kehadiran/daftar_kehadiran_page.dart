import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/format.dart';
import '../../../core/widgets/app_widgets.dart';
import '../../../shared/data/dummy_data.dart';
import '../../../shared/models/models.dart';

class DaftarKehadiranPage extends StatelessWidget {
  const DaftarKehadiranPage({super.key});

  void _info(BuildContext context, String msg) =>
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));

  Widget _counter(String label, int n, Color c) => Expanded(
        child: Column(children: [
          Text('$n', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800, color: c)),
          const SizedBox(height: 2),
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 12)),
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
            onPressed: () => _info(context, 'Data diperbarui'),
          ),
        ],
      ),
      body: ListenableBuilder(
        listenable: state,
        builder: (context, _) => Padding(
          padding: const EdgeInsets.fromLTRB(20, 8, 20, 20),
          child: Column(children: [
            Align(
              alignment: Alignment.centerLeft,
              child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(DummyData.courses.first.name,
                    style: const TextStyle(
                        fontWeight: FontWeight.bold, fontSize: 18, color: AppColors.ink)),
                const SizedBox(height: 2),
                Text(tanggalPanjang(DateTime.now()),
                    style: const TextStyle(color: Colors.grey, fontSize: 13)),
              ]),
            ),
            const SizedBox(height: 16),
            AppCard(
              child: Row(children: [
                _counter('Hadir', state.count(AttendStatus.hadir), AppColors.success),
                _counter('Izin', state.count(AttendStatus.izin), AppColors.warnText),
                _counter('Tidak', state.count(AttendStatus.tidak), AppColors.danger),
              ]),
            ),
            const SizedBox(height: 12),
            Expanded(
              child: AppCard(
                padding: const EdgeInsets.symmetric(vertical: 6),
                child: ListView.separated(
                  itemCount: state.students.length,
                  separatorBuilder: (_, _) => Divider(
                      height: 1, color: Colors.grey.withValues(alpha: 0.2)),
                  itemBuilder: (_, i) {
                    final s = state.students[i];
                    return ListTile(
                      leading: CircleAvatar(
                        backgroundColor: AppColors.tint,
                        child: Text(s.name[0],
                            style: const TextStyle(
                                color: AppColors.primary, fontWeight: FontWeight.bold)),
                      ),
                      title: Text(s.name,
                          style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14)),
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
              onPressed: () => _info(context, 'Ekspor tersedia setelah backend terhubung'),
            ),
          ]),
        ),
      ),
    );
  }
}
