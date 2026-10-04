import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/app_widgets.dart';
import '../../dosen/home/dosen_dashboard_page.dart';
import '../../mahasiswa/home/mahasiswa_dashboard_page.dart';

enum UserRole { mahasiswa, dosen }

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});
  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _idCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  UserRole _role = UserRole.mahasiswa;
  bool _hide = true;

  bool get _isDosen => _role == UserRole.dosen;

  void _login() {
    if (_idCtrl.text.trim().isEmpty || _passCtrl.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Isi email/ID dan password terlebih dahulu')),
      );
      return;
    }
    // TODO: ganti dengan panggilan API login.
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(
        builder: (_) =>
            _isDosen ? const DosenDashboardPage() : const MahasiswaDashboardPage(),
      ),
    );
  }

  @override
  void dispose() {
    _idCtrl.dispose();
    _passCtrl.dispose();
    super.dispose();
  }

  InputDecoration _decoration(String hint, IconData icon, {Widget? suffix}) {
    OutlineInputBorder border(Color c, double w) => OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: w == 0 ? BorderSide.none : BorderSide(color: c, width: w),
        );
    return InputDecoration(
      hintText: hint,
      prefixIcon: Icon(icon, color: AppColors.primary),
      suffixIcon: suffix,
      filled: true,
      fillColor: AppColors.background,
      border: border(Colors.transparent, 0),
      enabledBorder: border(Colors.transparent, 0),
      focusedBorder: border(AppColors.primary, 1.5),
    );
  }

  Widget _label(String t) => Padding(
        padding: const EdgeInsets.only(bottom: 8),
        child: Text(t, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
      );

  Widget _roleTab(String label, IconData icon, UserRole role) {
    final selected = _role == role;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _role = role),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 180),
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: selected ? AppColors.primary : AppColors.tint,
            borderRadius: BorderRadius.circular(12),
          ),
          child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [
            Icon(icon, size: 18, color: selected ? Colors.white : AppColors.primary),
            const SizedBox(width: 6),
            Text(label,
                style: TextStyle(
                  fontWeight: FontWeight.w600,
                  fontSize: 13,
                  color: selected ? Colors.white : AppColors.primary,
                )),
          ]),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(28, 45, 28, 40),
              decoration: const BoxDecoration(
                gradient: AppColors.gradient,
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(35),
                  bottomRight: Radius.circular(35),
                ),
              ),
              child: Column(children: [
                Container(
                  width: 75,
                  height: 75,
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.20),
                    borderRadius: BorderRadius.circular(22),
                  ),
                  child: const Icon(Icons.qr_code_2, size: 48, color: Colors.white),
                ),
                const SizedBox(height: 18),
                Text(
                  _isDosen ? 'Absensi QR Dosen' : 'Absensi QR Mahasiswa',
                  style: const TextStyle(
                      color: Colors.white, fontSize: 23, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 6),
                const Text(
                  'Sistem absensi mahasiswa berbasis QR Code',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: Colors.white70, fontSize: 13),
                ),
              ]),
            ),
            const SizedBox(height: 25),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 22),
              child: Container(
                padding: const EdgeInsets.all(22),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(25),
                  boxShadow: AppColors.shadow(0.07),
                ),
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Selamat Datang 👋',
                      style: TextStyle(
                          fontSize: 21, fontWeight: FontWeight.bold, color: AppColors.ink)),
                  const SizedBox(height: 5),
                  const Text('Silakan masuk untuk melanjutkan',
                      style: TextStyle(color: Colors.grey, fontSize: 13)),
                  const SizedBox(height: 20),
                  Row(children: [
                    _roleTab('Mahasiswa', Icons.school_outlined, UserRole.mahasiswa),
                    const SizedBox(width: 10),
                    _roleTab('Dosen', Icons.badge_outlined, UserRole.dosen),
                  ]),
                  const SizedBox(height: 22),
                  _label(_isDosen ? 'Email / NIDN' : 'Email / NIM'),
                  TextField(
                    controller: _idCtrl,
                    decoration: _decoration(
                      _isDosen ? 'Masukkan Email atau NIDN' : 'Masukkan Email atau NIM',
                      Icons.person_outline,
                    ),
                  ),
                  const SizedBox(height: 18),
                  _label('Password'),
                  TextField(
                    controller: _passCtrl,
                    obscureText: _hide,
                    decoration: _decoration(
                      'Masukkan password',
                      Icons.lock_outline,
                      suffix: IconButton(
                        onPressed: () => setState(() => _hide = !_hide),
                        icon: Icon(_hide
                            ? Icons.visibility_off_outlined
                            : Icons.visibility_outlined),
                      ),
                    ),
                  ),
                  const SizedBox(height: 10),
                  Align(
                    alignment: Alignment.centerRight,
                    child: TextButton(
                      onPressed: () => ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Fitur reset password belum tersedia')),
                      ),
                      child: const Text('Lupa password?',
                          style: TextStyle(color: AppColors.primary)),
                    ),
                  ),
                  const SizedBox(height: 10),
                  PrimaryButton(label: 'MASUK', onPressed: _login),
                ]),
              ),
            ),
            const SizedBox(height: 25),
            const Text('© 2026 Absensi Mahasiswa',
                style: TextStyle(color: Colors.grey, fontSize: 11)),
            const SizedBox(height: 20),
          ]),
        ),
      ),
    );
  }
}
