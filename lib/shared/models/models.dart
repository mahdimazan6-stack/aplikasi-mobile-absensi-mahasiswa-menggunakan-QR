import 'package:flutter/material.dart';

import '../../core/theme/app_theme.dart';

class Course {
  final String id, code, name, day, time, room, kelas;
  const Course({
    required this.id,
    required this.name,
    required this.day,
    required this.time,
    required this.room,
    this.kelas = '',
    this.code = '',
  });

  factory Course.fromJson(Map<String, dynamic> json) => Course(
    id: json['id']?.toString() ?? '',
    code: json['code']?.toString() ?? '',
    name: json['name']?.toString() ?? '',
    day: json['day']?.toString() ?? '',
    time: json['time']?.toString() ?? '',
    room: json['room']?.toString() ?? '',
    kelas: json['kelas']?.toString() ?? '',
  );
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

  factory AttendanceRecord.fromJson(Map<String, dynamic> json) {
    final session = json['session'] as Map<String, dynamic>?;
    final course = session?['course'] as Map<String, dynamic>?;
    final attendanceDate = json['attended_at']?.toString()
        ?? session?['created_at']?.toString()
        ?? json['created_at']?.toString()
        ?? '';
    final attendedAt = DateTime.tryParse(attendanceDate);

    if (attendedAt == null) {
      throw const FormatException('Waktu absensi dari server tidak valid.');
    }

    return AttendanceRecord(
      courseName: course?['name']?.toString() ?? 'Mata kuliah',
      date: attendedAt,
      status: json['status']?.toString() == 'hadir'
          ? AttendStatus.hadir
          : AttendStatus.tidak,
    );
  }
}

class StudentAttendance {
  final String name;
  final String nim;
  AttendStatus status;
  StudentAttendance(this.name, this.status, {this.nim = ''});

  factory StudentAttendance.fromJson(Map<String, dynamic> json) {
    final student = json['student'] as Map<String, dynamic>?;
    return StudentAttendance(
      student?['name']?.toString() ?? 'Mahasiswa',
      json['status']?.toString() == 'hadir'
          ? AttendStatus.hadir
          : AttendStatus.tidak,
      nim: student?['nim']?.toString() ?? '',
    );
  }
}

class AttendanceSessionInfo {
  final int id;
  final String qrToken;
  final DateTime expiresAt;

  const AttendanceSessionInfo({
    required this.id,
    required this.qrToken,
    required this.expiresAt,
  });

  factory AttendanceSessionInfo.fromJson(Map<String, dynamic> json) {
    final id = int.tryParse(json['session_id']?.toString() ?? '');
    final expiresAt = DateTime.tryParse(json['expires_at']?.toString() ?? '');
    final qrToken = json['qr_token']?.toString();

    if (id == null || expiresAt == null || qrToken == null || qrToken.isEmpty) {
      throw const FormatException('Data sesi absensi dari server tidak valid.');
    }

    return AttendanceSessionInfo(
      id: id,
      qrToken: qrToken,
      expiresAt: expiresAt,
    );
  }
}

extension CourseX on Course {
  int get weekday =>
      const {
        'Senin': 1,
        'Selasa': 2,
        'Rabu': 3,
        'Kamis': 4,
        'Jumat': 5,
        'Sabtu': 6,
        'Minggu': 7,
      }[day] ??
      0;

  int _menit(String hhmm) {
    final p = hhmm.trim().split(':');
    if (p.length < 2) return 0;
    final jam = int.tryParse(p[0]);
    final menit = int.tryParse(p[1]);
    if (jam == null || menit == null) return 0;
    return jam * 60 + menit;
  }

  int get mulaiMenit {
    final bagian = time.split('-');
    return _menit(bagian.first);
  }

  int get selesaiMenit {
    final bagian = time.split('-');
    return bagian.length < 2 ? 0 : _menit(bagian[1]);
  }

  bool get isToday => weekday == DateTime.now().weekday;

  bool get isOngoing {
    if (!isToday) return false;
    final n = DateTime.now();
    final m = n.hour * 60 + n.minute;
    return m >= mulaiMenit && m <= selesaiMenit;
  }
}
