import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/format.dart';
import '../../../core/widgets/app_widgets.dart';
import '../../../shared/models/models.dart';

class HasilAbsensiPage extends StatelessWidget {
  final AttendanceRecord record;
  const HasilAbsensiPage({super.key, required this.record});

  Widget _row(String k, String v) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 5),
        child: Row(children: [
          SizedBox(
              width: 100,
              child: Text(k, style: const TextStyle(color: Colors.grey, fontSize: 13))),
          Expanded(
              child: Text(v,
                  style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13))),
        ]),
      );

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(children: [
            const Spacer(),
            Container(
              width: 100,
              height: 100,
              decoration: BoxDecoration(
                color: AppColors.success,
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: AppColors.success.withValues(alpha: 0.3),
                    blurRadius: 20,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: const Icon(Icons.check, color: Colors.white, size: 58),
            ),
            const SizedBox(height: 22),
            const Text('Absensi Berhasil',
                style: TextStyle(
                    fontSize: 22, fontWeight: FontWeight.bold, color: AppColors.ink)),
            const SizedBox(height: 24),
            AppCard(
              padding: const EdgeInsets.all(20),
              child: Column(children: [
                _row('Mata Kuliah', record.courseName),
                _row('Tanggal', tanggalPendek(record.date)),
                _row('Waktu', jam(record.date)),
                _row('Status', record.status.label),
              ]),
            ),
            const Spacer(),
            PrimaryButton(
              label: 'SELESAI',
              onPressed: () => Navigator.of(context).popUntil((r) => r.isFirst),
            ),
          ]),
        ),
      ),
    );
  }
}
