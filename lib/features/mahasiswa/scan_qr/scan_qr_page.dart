
import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';

class ScanQrPage extends StatefulWidget {
  const ScanQrPage({super.key});

  @override
  State<ScanQrPage> createState() => _ScanQrPageState();
}

class _ScanQrPageState extends State<ScanQrPage> {
  final MobileScannerController _controller =
      MobileScannerController();

  bool _isProcessing = false;
  String? _hasilScan;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _onDetect(BarcodeCapture capture) {
    if (_isProcessing) return;

    for (final barcode in capture.barcodes) {
      final String? value = barcode.rawValue;

      if (value != null && value.isNotEmpty) {
        _isProcessing = true;

        setState(() {
          _hasilScan = value;
        });

        _controller.stop();
        _tampilkanHasil(value);
        break;
      }
    }
  }

  Future<void> _tampilkanHasil(String value) async {
    if (!mounted) return;

    final bool formatAbsensi = value.startsWith('ABSENSI|');

    await showDialog<void>(
      context: context,
      barrierDismissible: false,
      builder: (dialogContext) => AlertDialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        icon: Icon(
          formatAbsensi
              ? Icons.check_circle
              : Icons.qr_code_scanner,
          color: formatAbsensi ? Colors.green : Colors.blue,
          size: 55,
        ),
        title: Text(
          formatAbsensi ? 'QR Absensi Terbaca' : 'QR Code Terbaca',
          textAlign: TextAlign.center,
        ),
        content: SingleChildScrollView(
          child: SelectableText(
            formatAbsensi
                ? 'Data QR absensi berhasil dibaca.\n\n$value'
                : 'Isi QR Code:\n\n$value\n\n'
                  'QR terbaca, tetapi format absensi '
                  'belum dapat diverifikasi.',
            textAlign: TextAlign.center,
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext),
            child: const Text('Scan Lagi'),
          ),
        ],
      ),
    );

    if (!mounted) return;

    setState(() {
      _hasilScan = null;
      _isProcessing = false;
    });

    await _controller.start();
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
        elevation: 0,
      ),
      body: Column(
        children: [
          const SizedBox(height: 24),

          const Text(
            'Arahkan kamera ke QR Code absensi',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: Color(0xFF19345B),
            ),
          ),

          const SizedBox(height: 8),

          const Text(
            'Pastikan QR Code terlihat jelas di dalam kotak.',
            textAlign: TextAlign.center,
            style: TextStyle(color: Colors.grey),
          ),

          const SizedBox(height: 24),

          Expanded(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(24),
                child: Stack(
                  fit: StackFit.expand,
                  children: [
                    MobileScanner(
                      controller: _controller,
                      onDetect: _onDetect,
                      errorBuilder: (
                        context,
                        error,
                      ) {
                        return Container(
                          color: Colors.black,
                          alignment: Alignment.center,
                          padding: const EdgeInsets.all(24),
                          child: Text(
                            'Kamera tidak dapat dibuka.\n'
                            'Periksa izin kamera HP kamu.\n\n'
                            '${error.errorCode.name}',
                            textAlign: TextAlign.center,
                            style: const TextStyle(
                              color: Colors.white,
                            ),
                          ),
                        );
                      },
                    ),

                    Center(
                      child: Container(
                        width: 250,
                        height: 250,
                        decoration: BoxDecoration(
                          border: Border.all(
                            color: Colors.greenAccent,
                            width: 4,
                          ),
                          borderRadius: BorderRadius.circular(24),
                        ),
                      ),
                    ),

                    Positioned(
                      bottom: 20,
                      left: 12,
                      right: 12,
                      child: Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.black54,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Text(
                          'Posisikan QR Code di tengah kotak',
                          textAlign: TextAlign.center,
                          style: TextStyle(color: Colors.white),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),

          const SizedBox(height: 20),

          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20),
            child: Row(
              children: [
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () =>
                        _controller.toggleTorch(),
                    icon: const Icon(Icons.flash_on),
                    label: const Text('Lampu Flash'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF246BFE),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(
                        vertical: 15,
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () =>
                        _controller.switchCamera(),
                    icon: const Icon(Icons.cameraswitch),
                    label: const Text('Ganti Kamera'),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(
                        vertical: 15,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 24),
        ],
      ),
    );
  }
}
