import 'package:absensi_mahasiswa/shared/models/models.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test(
    'uses the class session date for attendance without a scan timestamp',
    () {
      final record = AttendanceRecord.fromJson({
        'attended_at': null,
        'status': 'alpa',
        'session': {
          'created_at': '2026-11-19T08:00:00+07:00',
          'course': {'name': 'Basis Data'},
        },
      });

      expect(record.date, DateTime.parse('2026-11-19T08:00:00+07:00'));
      expect(record.courseName, 'Basis Data');
      expect(record.status, AttendStatus.tidak);
    },
  );

  test('prefers the actual attendance scan timestamp', () {
    final record = AttendanceRecord.fromJson({
      'attended_at': '2026-11-19T08:15:00+07:00',
      'status': 'hadir',
      'session': {
        'created_at': '2026-11-19T08:00:00+07:00',
        'course': {'name': 'Basis Data'},
      },
    });

    expect(record.date, DateTime.parse('2026-11-19T08:15:00+07:00'));
    expect(record.status, AttendStatus.hadir);
  });
}
