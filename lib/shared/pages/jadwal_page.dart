import 'package:flutter/material.dart';
import '../../core/widgets/app_widgets.dart';
import '../data/dummy_data.dart';

class JadwalPage extends StatelessWidget {
  const JadwalPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Jadwal Kuliah')),
      body: ListView.separated(
        padding: const EdgeInsets.all(20),
        itemCount: DummyData.courses.length,
        separatorBuilder: (_, _) => const SizedBox(height: 12),
        itemBuilder: (_, i) {
          final c = DummyData.courses[i];
          return AppCard(
            child: Row(children: [
              const IconBox(icon: Icons.school),
              const SizedBox(width: 13),
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text(c.name,
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                  const SizedBox(height: 5),
                  Text('${c.day}, ${c.time}',
                      style: const TextStyle(color: Colors.grey, fontSize: 12)),
                  const SizedBox(height: 3),
                  Text('Ruang ${c.room}',
                      style: const TextStyle(color: Colors.grey, fontSize: 11)),
                ]),
              ),
            ]),
          );
        },
      ),
    );
  }
}
