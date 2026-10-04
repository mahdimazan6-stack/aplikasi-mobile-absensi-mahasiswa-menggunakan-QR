import 'package:flutter/foundation.dart';
import '../models/models.dart';

/// Data contoh. Nanti ganti dengan data dari API / database.
class DummyData {
  static const studentName = 'Mahdi Muhammad';
  static const studentNim = '3312511074';
  static const lecturerName = 'Dr. Sari Wulandari';

  static const courses = [
    Course(id: 'PM01', name: 'Pemrograman Mobile', day: 'Senin', time: '08:00 - 10:00', room: 'Lab Komputer'),
    Course(id: 'BD02', name: 'Basis Data', day: 'Selasa', time: '10:00 - 12:00', room: 'Ruang 204'),
    Course(id: 'RPL03', name: 'Rekayasa Perangkat Lunak', day: 'Rabu', time: '13:00 - 15:00', room: 'Ruang 105'),
    Course(id: 'JK04', name: 'Jaringan Komputer', day: 'Kamis', time: '09:00 - 11:00', room: 'Lab Jaringan'),
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
    AttendanceRecord(courseName: 'Jaringan Komputer', date: DateTime(2026, 9, 24, 9, 0), status: AttendStatus.izin),
  ];

  final List<StudentAttendance> students = [
    StudentAttendance('Andi Saputra', AttendStatus.hadir),
    StudentAttendance('Budi Santoso', AttendStatus.hadir),
    StudentAttendance('Citra Lestari', AttendStatus.izin),
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
