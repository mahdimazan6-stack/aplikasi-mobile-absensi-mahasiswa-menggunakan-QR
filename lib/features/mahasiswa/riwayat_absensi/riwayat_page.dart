import 'package:flutter/material.dart';
import '../../../core/utils/format.dart';
import '../../../core/widgets/app_widgets.dart';
import '../../../shared/data/dummy_data.dart';
import '../../../shared/models/models.dart';

class RiwayatPage extends StatelessWidget {
  const RiwayatPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Riwayat Absensi')),
      body: ListenableBuilder(
        listenable: AppState.instance,
        builder: (context, _) {
          final items = AppState.instance.history;
          if (items.isEmpty) {
            return const Center(child: Text('Belum ada riwayat. Scan QR untuk absen.'));
          }
          return ListView.separated(
            padding: const EdgeInsets.all(20),
            itemCount: items.length,
            separatorBuilder: (_, _) => const SizedBox(height: 12),
            itemBuilder: (_, i) {
              final r = items[i];
              return AppCard(
                child: Row(children: [
                  IconBox(icon: Icons.history, color: r.status.color),
                  const SizedBox(width: 13),
                  Expanded(
                    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Text(r.courseName,
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                      const SizedBox(height: 5),
                      Text('${tanggalPanjang(r.date)}, ${jam(r.date)}',
                          style: const TextStyle(color: Colors.grey, fontSize: 12)),
                    ]),
                  ),
                  StatusChip(label: r.status.label, color: r.status.color),
                ]),
              );
            },
          );
        },
      ),
    );
  }
}
