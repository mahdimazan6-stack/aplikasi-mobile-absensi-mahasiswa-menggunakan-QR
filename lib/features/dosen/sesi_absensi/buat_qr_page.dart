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
  /// Mata kuliah awal yang terpilih (bisa diganti dosen di halaman ini).
  final Course course;
  const BuatQrPage({super.key, required this.course});
  @override
  State<BuatQrPage> createState() => _BuatQrPageState();
}

class _BuatQrPageState extends State<BuatQrPage> {
  static const _opsiDurasi = [30, 60, 300]; // detik

  Timer? _timer;
  late Course _course = widget.course;
  int _durasi = 60;
  int _sisa = 60;
  bool _aktif = false;
  late String _token = _buatToken();

  // TODO: token sebaiknya dibuat & divalidasi server.
  String _buatToken() {
    final sekarang = DateTime.now();

    return 'QR ABSENSI BERHASIL DIPINDAI!\n'
        '--------------------------\n'
        'Mata Kuliah : ${_course.name}\n'
        'Hari        : ${_course.day}\n'
        'Jam Kuliah  : ${_course.time}\n'
        'Ruangan     : ${_course.room}\n'
        'Kelas       : ${_course.kelas}\n'
        'Tanggal Scan: ${tanggalPanjang(sekarang)}\n'
        'No. Token   : ${sekarang.millisecondsSinceEpoch}';
  }

  String _labelDurasi(int d) => d < 60 ? '$d detik' : '${d ~/ 60} menit';

  // ---------- aksi ----------

  Future<void> _pilihMatkul() async {
    if (_aktif) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
        content: Text('Akhiri sesi dulu untuk mengganti mata kuliah'),
      ));
      return;
    }
    final hasil = await showModalBottomSheet<Course>(
      context: context,
      backgroundColor: Colors.white,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(25)),
      ),
      builder: (_) => _PilihMatkulSheet(selected: _course),
    );
    if (hasil != null && hasil.id != _course.id) {
      setState(() {
        _course = hasil;
        _token = _buatToken();
      });
    }
  }

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

  Future<bool> _konfirmasiAkhiri() async {
    final ya = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text('Akhiri sesi absensi?'),
        content: const Text(
            'QR tidak berlaku lagi dan mahasiswa tidak bisa absen setelah ini.'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(ctx, false),
              child: const Text('Batal')),
          TextButton(
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Akhiri',
                style: TextStyle(color: AppColors.danger)),
          ),
        ],
      ),
    );
    return ya ?? false;
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  // ---------- tampilan ----------

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: !_aktif,
      onPopInvokedWithResult: (didPop, _) async {
        if (didPop) return;
        final keluar = await _konfirmasiAkhiri();
        if (!keluar || !mounted) return;
        _akhiri();
        Navigator.pop(context);
      },
      child: Scaffold(
        appBar: AppBar(title: const Text('Buat QR Absensi')),
        body: ListView(
          padding: const EdgeInsets.fromLTRB(20, 8, 20, 30),
          children: [
            _kartuMatkul(),
            if (!_course.isToday && !_aktif) ...[
              const SizedBox(height: 10),
              _peringatanJadwal(),
            ],
            const SizedBox(height: 16),
            _kartuQr(),
            const SizedBox(height: 16),
            if (_aktif) _kartuKehadiran() else _pilihDurasi(),
            const SizedBox(height: 20),
            if (!_aktif)
              PrimaryButton(
                label: 'MULAI SESI ABSENSI',
                icon: Icons.play_arrow_rounded,
                onPressed: _mulai,
              )
            else ...[
              PrimaryButton(
                label: 'PANTAU KEHADIRAN',
                icon: Icons.groups_outlined,
                onPressed: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (_) => DaftarKehadiranPage(course: _course)),
                ),
              ),
              const SizedBox(height: 12),
              PrimaryButton(
                label: 'AKHIRI SESI',
                icon: Icons.stop_rounded,
                color: AppColors.danger,
                onPressed: () async {
                  if (await _konfirmasiAkhiri()) _akhiri();
                },
              ),
            ],
            const SizedBox(height: 14),
            const Center(
              child: Text('QR ditampilkan kepada mahasiswa untuk dipindai.',
                  style: TextStyle(color: Colors.grey, fontSize: 12)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _kartuMatkul() {
    final badge = courseBadge(_course);
    return AppCard(
      onTap: _pilihMatkul,
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          const IconBox(icon: Icons.school),
          const SizedBox(width: 13),
          Expanded(
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Text('Mata Kuliah',
                  style: TextStyle(color: Colors.grey, fontSize: 12)),
              const SizedBox(height: 3),
              Text(_course.name,
                  style: const TextStyle(
                      fontWeight: FontWeight.bold, fontSize: 15, color: AppColors.ink)),
            ]),
          ),
          if (badge != null) ...[badge, const SizedBox(width: 6)],
          Icon(_aktif ? Icons.lock_outline : Icons.unfold_more_rounded,
              color: Colors.grey),
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
  }

  Widget _peringatanJadwal() {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.warnBg,
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(children: [
        const Icon(Icons.info_outline, color: AppColors.warnText, size: 18),
        const SizedBox(width: 8),
        Expanded(
          child: Text('Jadwal mata kuliah ini hari ${_course.day}, bukan hari ini.',
              style: const TextStyle(color: AppColors.warnText, fontSize: 12)),
        ),
      ]),
    );
  }

  Widget _kartuQr() {
    final hampirHabis = _aktif && _sisa <= 10;
    return AppCard(
      radius: 25,
      padding: const EdgeInsets.all(20),
      child: Column(children: [
        StatusChip(
          label: _aktif ? '● SESI AKTIF' : 'SESI BELUM DIMULAI',
          color: _aktif ? AppColors.success : Colors.grey,
        ),
        const SizedBox(height: 16),
        Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
              color: _aktif ? AppColors.success : AppColors.tint,
              width: 3,
            ),
          ),
          child: Stack(alignment: Alignment.center, children: [
            AnimatedOpacity(
              duration: const Duration(milliseconds: 300),
              opacity: _aktif ? 1 : 0.12,
              child: QrImageView(data: _token, size: 210),
            ),
            if (!_aktif)
              const Column(mainAxisSize: MainAxisSize.min, children: [
                Icon(Icons.lock_outline, color: Colors.grey, size: 30),
                SizedBox(height: 6),
                Text('QR aktif setelah\nsesi dimulai',
                    textAlign: TextAlign.center,
                    style: TextStyle(fontWeight: FontWeight.w600, color: Colors.grey)),
              ]),
          ]),
        ),
        const SizedBox(height: 18),
        if (_aktif) ...[
          Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            const Text('QR diperbarui dalam',
                style: TextStyle(color: Colors.grey, fontSize: 12)),
            Text(menitDetik(_sisa),
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                  color: hampirHabis ? AppColors.danger : AppColors.ink,
                )),
          ]),
          const SizedBox(height: 8),
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: LinearProgressIndicator(
              value: _sisa / _durasi,
              minHeight: 8,
              backgroundColor: AppColors.tint,
              color: hampirHabis ? AppColors.danger : AppColors.primary,
            ),
          ),
        ] else
          Text('QR akan diperbarui otomatis tiap ${_labelDurasi(_durasi)}',
              style: const TextStyle(color: Colors.grey, fontSize: 12)),
      ]),
    );
  }

  Widget _pilihDurasi() {
    return AppCard(
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        const Text('Perbarui QR setiap',
            style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
        const SizedBox(height: 4),
        const Text('Makin singkat, makin sulit QR dibagikan ke teman.',
            style: TextStyle(color: Colors.grey, fontSize: 11)),
        const SizedBox(height: 12),
        Row(children: [
          for (final d in _opsiDurasi) ...[
            Expanded(
              child: GestureDetector(
                onTap: () => setState(() {
                  _durasi = d;
                  _sisa = d;
                }),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 180),
                  padding: const EdgeInsets.symmetric(vertical: 11),
                  alignment: Alignment.center,
                  decoration: BoxDecoration(
                    color: _durasi == d ? AppColors.primary : AppColors.tint,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(_labelDurasi(d),
                      style: TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 13,
                        color: _durasi == d ? Colors.white : AppColors.primary,
                      )),
                ),
              ),
            ),
            if (d != _opsiDurasi.last) const SizedBox(width: 10),
          ],
        ]),
      ]),
    );
  }

  Widget _kartuKehadiran() {
    return ListenableBuilder(
      listenable: AppState.instance,
      builder: (context, _) {
        final s = AppState.instance;
        final hadir = s.count(AttendStatus.hadir);
        final total = s.students.length;
        final persen = total == 0 ? 0.0 : hadir / total;
        return AppCard(
          child: Column(children: [
            Row(children: [
              const IconBox(
                  icon: Icons.groups_outlined,
                  color: AppColors.success,
                  size: 45,
                  iconSize: 25),
              const SizedBox(width: 13),
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Kehadiran Masuk',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                  const SizedBox(height: 3),
                  Text('$hadir dari $total mahasiswa',
                      style: const TextStyle(color: Colors.grey, fontSize: 12)),
                ]),
              ),
              Text('${(persen * 100).round()}%',
                  style: const TextStyle(
                      fontWeight: FontWeight.w800,
                      fontSize: 20,
                      color: AppColors.success)),
            ]),
            const SizedBox(height: 14),
            ClipRRect(
              borderRadius: BorderRadius.circular(8),
              child: LinearProgressIndicator(
                value: persen,
                minHeight: 8,
                backgroundColor: AppColors.tint,
                color: AppColors.success,
              ),
            ),
          ]),
        );
      },
    );
  }
}

// ---------- komponen kecil ----------

/// Badge "BERLANGSUNG" / "HARI INI" untuk sebuah mata kuliah (null jika bukan hari ini).
Widget? courseBadge(Course c) {
  if (c.isOngoing) {
    return const StatusChip(label: 'BERLANGSUNG', color: AppColors.success);
  }
  if (c.isToday) {
    return const StatusChip(label: 'HARI INI', color: AppColors.primary);
  }
  return null;
}

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

class _PilihMatkulSheet extends StatelessWidget {
  final Course selected;
  const _PilihMatkulSheet({required this.selected});

  @override
  Widget build(BuildContext context) {
    // Mata kuliah hari ini ditaruh paling atas.
    final daftar = [...DummyData.courses]
      ..sort((a, b) => (b.isToday ? 1 : 0).compareTo(a.isToday ? 1 : 0));

    return SafeArea(
      child: Padding(
        padding: const EdgeInsets.fromLTRB(20, 12, 20, 16),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(
            width: 42,
            height: 4,
            decoration: BoxDecoration(
              color: Colors.grey.shade300,
              borderRadius: BorderRadius.circular(4),
            ),
          ),
          const SizedBox(height: 16),
          const Align(
            alignment: Alignment.centerLeft,
            child: Text('Pilih Mata Kuliah',
                style: TextStyle(
                    fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.ink)),
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
                final badge = courseBadge(c);
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
                          child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(children: [
                                  Flexible(
                                    child: Text(c.name,
                                        overflow: TextOverflow.ellipsis,
                                        style: const TextStyle(
                                            fontWeight: FontWeight.bold, fontSize: 14)),
                                  ),
                                  if (badge != null) ...[const SizedBox(width: 6), badge],
                                ]),
                                const SizedBox(height: 4),
                                Text('${c.day}, ${c.time}',
                                    style: const TextStyle(
                                        color: Colors.grey, fontSize: 12)),
                                const SizedBox(height: 2),
                                Text('${c.room}  •  ${c.kelas}',
                                    style: const TextStyle(
                                        color: Colors.grey, fontSize: 11)),
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
