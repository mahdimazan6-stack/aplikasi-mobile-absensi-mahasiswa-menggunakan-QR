import 'package:absensi_mahasiswa/services/api_service.dart';
import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';

class ScanQrPage extends StatefulWidget {
  const ScanQrPage({super.key});

  @override
  State<ScanQrPage> createState() => _ScanQrPageState();
}

class _ScanQrPageState extends State<ScanQrPage> {
  final MobileScannerController _controller = MobileScannerController(
    detectionSpeed: DetectionSpeed.noDuplicates,
  );

  bool _processing = false;
  bool _flashOn = false;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> kirimAbsensi(String qrToken) async {
    try {
      final berhasil = await ApiService.scanQr(qrToken);

      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            berhasil
                ? 'Absensi berhasil disimpan'
                : 'Absensi gagal atau QR tidak valid',
          ),
        ),
      );
    } catch (e) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Terjadi kesalahan: $e'),
        ),
      );
    }
  }

  String _ambilNilai(String teks, String label) {
    final lines = teks.split('\n');

    for (final line in lines) {
      if (line.toLowerCase().contains(label.toLowerCase())) {
        final index = line.indexOf(':');
        if (index != -1) {
          return line.substring(index + 1).trim();
        }
      }
    }

    return '-';
  }

  Future<void> _onDetect(BarcodeCapture capture) async {
    if (_processing) return;

    final rawValue = capture.barcodes.firstOrNull?.rawValue;
    if (rawValue == null || rawValue.isEmpty) return;

    setState(() {
      _processing = true;
    });

    await _controller.stop();
    await _showResult(rawValue);
  }

  Future<void> _showResult(String value) async {
    if (!mounted) return;

    final bool valid = value.toLowerCase().contains('mata kuliah') &&
        value.toLowerCase().contains('no. token');

    final mataKuliah = _ambilNilai(value, 'Mata Kuliah');
    final hari = _ambilNilai(value, 'Hari');
    final jam = _ambilNilai(value, 'Jam Kuliah');
    final ruangan = _ambilNilai(value, 'Ruangan');
    final kelas = _ambilNilai(value, 'Kelas');
    final tanggal = _ambilNilai(value, 'Tanggal Scan');
    final token = _ambilNilai(value, 'No. Token');

    await showDialog<void>(
      context: context,
      barrierDismissible: false,
      builder: (dialogContext) {
        return AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
          ),
          title: Column(
            children: [
              Icon(
                valid ? Icons.check_circle : Icons.qr_code,
                color: valid ? Colors.green : Colors.blue,
                size: 55,
              ),
              const SizedBox(height: 10),
              Text(
                valid ? 'QR Absensi Terbaca!' : 'QR Code Terbaca',
                textAlign: TextAlign.center,
              ),
            ],
          ),
          content: SingleChildScrollView(
            child: valid
                ? Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      _detail('Mata Kuliah', mataKuliah),
                      _detail('Hari', hari),
                      _detail('Jam Kuliah', jam),
                      _detail('Ruangan', ruangan),
                      _detail('Kelas', kelas),
                      _detail('Tanggal Scan', tanggal),
                      _detail('No. Token', token),
                      const SizedBox(height: 12),
                      const Text(
                        'QR berhasil dibaca. '
                        'Pencatatan kehadiran perlu divalidasi '
                        'oleh sistem absensi.',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.green,
                        ),
                      ),
                    ],
                  )
                : SelectableText(
                    'Isi QR Code:\n\n$value',
                  ),
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.pop(dialogContext);
              },
              child: const Text('Scan Lagi'),
            ),
          ],
        );
      },
    );

    if (valid && token != '-' && mounted) {
      await kirimAbsensi(token);
    }

    if (!mounted) return;

    setState(() {
      _processing = false;
    });

    await _controller.start();
  }

  Widget _detail(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 5),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            flex: 4,
            child: Text(
              label,
              style: const TextStyle(fontWeight: FontWeight.w600),
            ),
          ),
          const Text(': '),
          Expanded(
            flex: 5,
            child: Text(value),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF3F6FC),
      appBar: AppBar(
        title: const Text(
          'Scan QR Absensi',
          style: TextStyle(fontWeight: FontWeight.bold),
        ),
        centerTitle: true,
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF19345B),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Column(
        children: [
          const SizedBox(height: 22),
          const Text(
            'Arahkan kamera ke QR Code absensi',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: Color(0xFF19345B),
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 8),
          const Text(
            'Pastikan QR Code terlihat jelas.',
            style: TextStyle(color: Colors.grey),
          ),
          const SizedBox(height: 20),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 18),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(24),
                child: Stack(
                  fit: StackFit.expand,
                  children: [
                    MobileScanner(
                      controller: _controller,
                      onDetect: _onDetect,
                      errorBuilder: (context, error) {
                        return Center(
                          child: Padding(
                            padding: const EdgeInsets.all(20),
                            child: Text(
                              'Kamera gagal dibuka.\n'
                              'Periksa izin kamera HP kamu.\n'
                              '${error.errorCode.name}',
                              textAlign: TextAlign.center,
                            ),
                          ),
                        );
                      },
                    ),
                    Center(
                      child: Container(
                        width: 260,
                        height: 260,
                        decoration: BoxDecoration(
                          border: Border.all(
                            color: Colors.greenAccent,
                            width: 4,
                          ),
                          borderRadius: BorderRadius.circular(22),
                        ),
                      ),
                    ),
                    const Positioned(
                      left: 10,
                      right: 10,
                      bottom: 18,
                      child: Text(
                        'Letakkan QR Code di tengah kotak',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          shadows: [
                            Shadow(
                              color: Colors.black,
                              blurRadius: 5,
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () async {
                      await _controller.toggleTorch();
                      if (mounted) {
                        setState(() {
                          _flashOn = !_flashOn;
                        });
                      }
                    },
                    icon: Icon(
                      _flashOn ? Icons.flash_off : Icons.flash_on,
                    ),
                    label: Text(
                      _flashOn ? 'Matikan Flash' : 'Lampu Flash',
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () => _controller.switchCamera(),
                    icon: const Icon(Icons.cameraswitch),
                    label: const Text('Ganti Kamera'),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
