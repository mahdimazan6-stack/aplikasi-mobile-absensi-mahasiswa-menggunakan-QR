import 'package:flutter/material.dart';
import '../../core/theme/app_theme.dart';

class Course {
  final String id, name, day, time, room, kelas;
  const Course({
    required this.id,
    required this.name,
    required this.day,
    required this.time,
    required this.room,
    this.kelas = '',
  });
}

enum AttendStatus { hadir, tidak }

extension AttendStatusX on AttendStatus {
  String get label => switch (this) {
        AttendStatus.hadir => 'HADIR',
        AttendStatus.tidak => 'TIDAK HADIR',
      };
  Color get color => switch (this) {
        AttendStatus.hadir => AppColors.success,
        AttendStatus.tidak => AppColors.danger,
      };
}

class AttendanceRecord {
  final String courseName;
  final DateTime date;
  final AttendStatus status;
  const AttendanceRecord({
    required this.courseName,
    required this.date,
    required this.status,
  });
}

class StudentAttendance {
  final String name;
  AttendStatus status;
  StudentAttendance(this.name, this.status);
}

extension CourseX on Course {
  int get weekday =>
      const {'Senin': 1, 'Selasa': 2, 'Rabu': 3, 'Kamis': 4, 'Jumat': 5, 'Sabtu': 6, 'Minggu': 7}[day] ?? 0;

  int _menit(String hhmm) {
    final p = hhmm.trim().split(':');
    return int.parse(p[0]) * 60 + int.parse(p[1]);
  }

  int get mulaiMenit => _menit(time.split('-')[0]);
  int get selesaiMenit => _menit(time.split('-')[1]);

  bool get isToday => weekday == DateTime.now().weekday;

  bool get isOngoing {
    if (!isToday) return false;
    final n = DateTime.now();
    final m = n.hour * 60 + n.minute;
    return m >= mulaiMenit && m <= selesaiMenit;
  }
}
