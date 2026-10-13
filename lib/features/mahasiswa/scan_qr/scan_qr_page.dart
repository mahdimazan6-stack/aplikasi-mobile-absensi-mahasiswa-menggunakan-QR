import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';

import '../../../core/theme/app_theme.dart';
import '../../../shared/data/dummy_data.dart';
import '../../../shared/models/models.dart';
import 'hasil_absensi_page.dart';

class ScanQrPage extends StatefulWidget {
  const ScanQrPage({super.key});

  @override
  State<ScanQrPage> createState() => _ScanQrPageState();
}

class _ScanQrPageState extends State<ScanQrPage> {
  late final MobileScannerController _controller;

  bool _handled = false;
  bool _processing = false;

  @override
  void initState() {
    super.initState();

    _controller = MobileScannerController(
      detectionSpeed: DetectionSpeed.noDuplicates,
      facing: CameraFacing.back,
      torchEnabled: false,
    );
  }

  Future<void> _onDetect(BarcodeCapture capture) async {
    if (_handled || _processing || !mounted) return;

    for (final barcode in capture.barcodes) {
      final raw = barcode.rawValue;

      if (raw == null || raw.trim().isEmpty) continue;

      final parts = raw.trim().split('|');

      if (parts.length != 3 || parts[0] != 'ABSENSI') {
        continue;
      }

      final courseId = parts[1].trim();
      final timestamp = int.tryParse(parts[2].trim());

      if (courseId.isEmpty || timestamp == null) continue;

      _processing = true;
      _handled = true;

      await _controller.stop();

      if (!mounted) return;

      try {
        final course = DummyData.courseById(courseId);

        final record = AttendanceRecord(
          courseName: course.name,
          date: DateTime.now(),
          status: AttendStatus.hadir,
        );

        AppState.instance.addAttendance(record);

        if (!mounted) return;

        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (_) => HasilAbsensiPage(record: record),
          ),
        );
      } catch (e) {
        _handled = false;
        _processing = false;

        if (!mounted) return;

        await _controller.start();

        if (!mounted) return;

        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text(
              'Mata kuliah tidak ditemukan. Silakan scan QR yang valid.',
            ),
          ),
        );
      }

      return;
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Scan QR Code'),
        centerTitle: true,
        actions: [
          IconButton(
            tooltip: 'Nyalakan atau matikan flash',
            onPressed: () => _controller.toggleTorch(),
            icon: const Icon(Icons.flash_on),
          ),
        ],
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            children: [
              const SizedBox(height: 12),

              const Icon(
                Icons.qr_code_scanner,
                size: 60,
                color: AppColors.primary,
              ),

              const SizedBox(height: 12),

              const Text(
                'Absensi Mahasiswa',
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                ),
              ),

              const SizedBox(height: 8),

              const Text(
                'Arahkan kamera ke QR Code yang ditampilkan dosen.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  color: Colors.grey,
                  fontSize: 14,
                ),
              ),

              const SizedBox(height: 24),

              Expanded(
                child: Container(
                  width: double.infinity,
                  clipBehavior: Clip.antiAlias,
                  decoration: BoxDecoration(
                    color: AppColors.ink,
                    borderRadius: BorderRadius.circular(24),
                    border: Border.all(
                      color: AppColors.primary,
                      width: 3,
                    ),
                    boxShadow: AppColors.shadow(0.1),
                  ),
                  child: Stack(
                    fit: StackFit.expand,
                    children: [
                      MobileScanner(
                        controller: _controller,
                        onDetect: _onDetect,
                        errorBuilder: (context, error) {
                          return const Center(
                            child: Padding(
                              padding: EdgeInsets.all(20),
                              child: Text(
                                'Kamera tidak dapat dibuka.\n'
                                'Periksa izin kamera di pengaturan HP.',
                                textAlign: TextAlign.center,
                                style: TextStyle(color: Colors.white),
                              ),
                            ),
                          );
                        },
                      ),

                      IgnorePointer(
                        child: Center(
                          child: Container(
                            width: 230,
                            height: 230,
                            decoration: BoxDecoration(
                              border: Border.all(
                                color: Colors.white,
                                width: 3,
                              ),
                              borderRadius: BorderRadius.circular(24),
                            ),
                          ),
                        ),
                      ),

                      if (_processing)
                        const Center(
                          child: CircularProgressIndicator(),
                        ),
                    ],
                  ),
                ),
              ),

              const SizedBox(height: 20),

              const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.center_focus_strong,
                    color: AppColors.primary,
                  ),
                  SizedBox(width: 8),
                  Flexible(
                    child: Text(
                      'Posisikan QR Code di dalam kotak.',
                      textAlign: TextAlign.center,
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 20),

              SizedBox(
                width: double.infinity,
                height: 50,
                child: OutlinedButton(
                  onPressed: () => Navigator.pop(context),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppColors.primary,
                    side: BorderSide(
                      color: AppColors.primary.withValues(alpha: 0.4),
                    ),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                  ),
                  child: const Text(
                    'BATAL',
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}