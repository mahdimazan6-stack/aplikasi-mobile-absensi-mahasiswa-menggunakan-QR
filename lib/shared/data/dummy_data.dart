import 'package:flutter/foundation.dart';
import '../models/models.dart';

/// Data contoh. Nanti ganti dengan data dari API / database.
class DummyData {
  static const studentName = 'Mahdi Muhammad';
  static const studentNim = '3312511074';
  static const lecturerName = 'Dr. Syam';
  static const akunMahasiswa = {'id': '3312511074', 'password': '123456'};
  static const akunDosen = {'id': '3312511138', 'password': '123456'};

  static const courses = [
    Course(id: 'MOBILE', name: 'Mata Kuliah Pilihan Mobile', day: 'Senin', time: '19:00 - 22:00', room: 'Gedung Utama', kelas: 'GU-706'),
    Course(id: 'IF317', name: 'Interaksi Manusia dan Komputer', day: 'Selasa', time: '19:00 - 22:00', room: 'Gedung Utama', kelas: 'GU-805'),
    Course(id: 'PK1IF', name: 'Pendidikan Agama', day: 'Rabu', time: '18:00 - 20:00', room: 'Online', kelas: 'Zoom Meeting'),
    Course(id: 'IF314', name: 'Proyek Inovasi Agile', day: 'Rabu', time: '20:30 - 23:00', room: 'Gedung Utama', kelas: 'GU-707'),
    Course(id: 'PK3IF', name: 'Pendidikan Kewarganegaraan', day: 'Kamis', time: '18:00 - 19:30', room: 'Online', kelas: 'Zoom Meeting'),
    Course(id: 'IF315', name: 'Rekayasa Perangkat Lunak', day: 'Jumat', time: '19:00 - 21:00', room: 'Gedung Utama', kelas: 'GU-704'),
    Course(id: 'IF318', name: 'Statistika', day: 'Jumat', time: '21:00 - 22:30', room: 'Online', kelas: 'Zoom Meeting'),
  ];

  static Course courseById(String id) =>
      courses.firstWhere((c) => c.id == id, orElse: () => courses.first);
}

/// Penyimpan state sementara di memori (hilang saat aplikasi ditutup).
class AppState extends ChangeNotifier {
  AppState._();
  static final instance = AppState._();

  final List<AttendanceRecord> history = [
    AttendanceRecord(courseName: 'Basis Data', date: DateTime(2026, 9, 22, 10, 3), status: AttendStatus.hadir),
    AttendanceRecord(courseName: 'Rekayasa Perangkat Lunak', date: DateTime(2026, 9, 23, 13, 6), status: AttendStatus.hadir),
    AttendanceRecord(courseName: 'Jaringan Komputer', date: DateTime(2026, 9, 24, 9, 0), status: AttendStatus.tidak),
  ];

  final List<StudentAttendance> students = [
    StudentAttendance('Andi Saputra', AttendStatus.hadir),
    StudentAttendance('Budi Santoso', AttendStatus.hadir),
    StudentAttendance('Deni Pratama', AttendStatus.hadir),
    StudentAttendance('Eka Putri', AttendStatus.tidak),
    StudentAttendance('Fajar Ramadhan', AttendStatus.hadir),
  ];

  bool sessionActive = false;

  void addAttendance(AttendanceRecord r) {
    history.insert(0, r);
    final i = students.indexWhere((s) => s.name == DummyData.studentName);
    if (i >= 0) {
      students[i].status = r.status;
    } else {
      students.insert(0, StudentAttendance(DummyData.studentName, r.status));
    }
    notifyListeners();
  }

  void setSession(bool active) {
    sessionActive = active;
    notifyListeners();
  }

  bool get sudahAbsenHariIni {
    final now = DateTime.now();
    return history.any((r) =>
        r.courseName == DummyData.courses.first.name &&
        r.date.year == now.year &&
        r.date.month == now.month &&
        r.date.day == now.day);
  }

  int count(AttendStatus s) => students.where((e) => e.status == s).length;
}
