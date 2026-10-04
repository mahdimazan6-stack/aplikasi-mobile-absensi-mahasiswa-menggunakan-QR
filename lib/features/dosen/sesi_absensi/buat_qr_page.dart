import 'dart:async';
import 'package:flutter/material.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/format.dart';
import '../../../core/widgets/app_widgets.dart';
import '../../../shared/data/dummy_data.dart';
import '../../../shared/models/models.dart';
import '../rekap_kehadiran/daftar_kehadiran_page.dart';

class BuatQrPage extends StatefulWidget {
  final Course course;
  const BuatQrPage({super.key, required this.course});
  @override
  State<BuatQrPage> createState() => _BuatQrPageState();
}

class _BuatQrPageState extends State<BuatQrPage> {
  static const _durasi = 300; // detik (5 menit)
  Timer? _timer;
  int _sisa = _durasi;
  bool _aktif = false;
  late String _token = _buatToken();

  // TODO: token sebaiknya dibuat & divalidasi server.
  String _buatToken() =>
      'ABSENSI|${widget.course.id}|${DateTime.now().millisecondsSinceEpoch}';

  void _mulai() {
    setState(() {
      _aktif = true;
      _sisa = _durasi;
      _token = _buatToken();
    });
    AppState.instance.setSession(true);
    _timer = Timer.periodic(const Duration(seconds: 1), (_) {
      setState(() {
        _sisa--;
        if (_sisa <= 0) {
          _sisa = _durasi;
          _token = _buatToken(); // QR diperbarui otomatis
        }
      });
    });
  }

  void _akhiri() {
    _timer?.cancel();
    setState(() => _aktif = false);
    AppState.instance.setSession(false);
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Buat QR Absensi')),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          AppCard(
            child: Row(children: [
              const IconBox(icon: Icons.school),
              const SizedBox(width: 13),
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Mata Kuliah',
                      style: TextStyle(color: Colors.grey, fontSize: 12)),
                  const SizedBox(height: 3),
                  Text(widget.course.name,
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                ]),
              ),
            ]),
          ),
          const SizedBox(height: 20),
          Center(
            child: Container(
              padding: const EdgeInsets.all(22),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(25),
                boxShadow: AppColors.shadow(0.07),
              ),
              child: Stack(alignment: Alignment.center, children: [
                Opacity(
                  opacity: _aktif ? 1 : 0.12,
                  child: QrImageView(data: _token, size: 220),
                ),
                if (!_aktif)
                  const Text('QR aktif setelah\nsesi dimulai',
                      textAlign: TextAlign.center,
                      style: TextStyle(fontWeight: FontWeight.w600, color: Colors.grey)),
              ]),
            ),
          ),
          const SizedBox(height: 16),
          Center(
            child: Text(
              _aktif ? 'Kode aktif selama: ${menitDetik(_sisa)}' : 'Sesi belum dimulai',
              style: const TextStyle(
                  fontWeight: FontWeight.bold, fontSize: 16, color: AppColors.ink),
            ),
          ),
          const SizedBox(height: 20),
          if (!_aktif)
            PrimaryButton(label: 'MULAI SESI ABSENSI', onPressed: _mulai)
          else ...[
            PrimaryButton(
              label: 'PANTAU KEHADIRAN',
              icon: Icons.groups_outlined,
              onPressed: () => Navigator.push(context,
                  MaterialPageRoute(builder: (_) => const DaftarKehadiranPage())),
            ),
            const SizedBox(height: 12),
            PrimaryButton(label: 'AKHIRI SESI', color: AppColors.danger, onPressed: _akhiri),
          ],
          const SizedBox(height: 14),
          const Center(
            child: Text('QR ditampilkan kepada mahasiswa untuk dipindai.',
                style: TextStyle(color: Colors.grey, fontSize: 12)),
          ),
        ],
      ),
    );
  }
}
