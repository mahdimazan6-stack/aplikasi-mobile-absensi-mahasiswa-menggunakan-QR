import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

import '../shared/models/models.dart';

class ApiService {
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://192.168.1.9:8000/api',
  );

  static Future<bool> login(String nim, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({'nim': nim, 'password': password}),
    );

    if (response.statusCode == 200 || response.statusCode == 201) {
      final data = jsonDecode(response.body) as Map<String, dynamic>;
      final token = data['token'] as String?;
      final user = data['user'] as Map<String, dynamic>?;

      if (token == null || user == null) {
        throw const FormatException(
          'Respons login tidak berisi token atau data pengguna.',
        );
      }

      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('token', token);
      await prefs.setString('role', user['role']?.toString() ?? '');
      await prefs.setString('name', user['name']?.toString() ?? '');
      await prefs.setString('nim', user['nim']?.toString() ?? '');

      return true;
    }

    if (response.statusCode == 401 || response.statusCode == 422) {
      return false;
    }

    throw Exception('Server login merespons HTTP ${response.statusCode}.');
  }

  static Future<Map<String, dynamic>> getMe() async {
    final response = await http.get(
      Uri.parse('$baseUrl/me'),
      headers: await _authorizedHeaders(),
    );
    final data = _decodeResponse(response);
    return data['user'] as Map<String, dynamic>;
  }

  static Future<String?> getSavedRole() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('role');
  }

  static Future<List<Course>> getCourses() async {
    final response = await http.get(
      Uri.parse('$baseUrl/courses'),
      headers: await _authorizedHeaders(),
    );
    final data = _decodeResponse(response);
    final courses = data['courses'] as List<dynamic>;
    return courses
        .map((course) => Course.fromJson(course as Map<String, dynamic>))
        .toList();
  }

  static Future<AttendanceSessionInfo> createAttendanceSession(
    String courseId, {
    required int durationSeconds,
  }) async {
    final id = int.tryParse(courseId);
    if (id == null) {
      throw ArgumentError.value(
        courseId,
        'courseId',
        'ID mata kuliah tidak valid',
      );
    }

    final response = await http.post(
      Uri.parse('$baseUrl/attendance/sessions'),
      headers: await _authorizedHeaders(json: true),
      body: jsonEncode({'course_id': id, 'duration_seconds': durationSeconds}),
    );
    final data = _decodeResponse(response, expectedStatusCode: 201);
    return AttendanceSessionInfo.fromJson(data);
  }

  static Future<List<StudentAttendance>> getSessionAttendances(
    int sessionId,
  ) async {
    final response = await http.get(
      Uri.parse('$baseUrl/attendance/sessions/$sessionId/attendances'),
      headers: await _authorizedHeaders(),
    );
    final data = _decodeResponse(response);
    final attendances = data['attendances'] as List<dynamic>;
    return attendances
        .map((item) => StudentAttendance.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  static Future<List<StudentAttendance>> getCourseAttendances(
    String courseId,
  ) async {
    final id = int.tryParse(courseId);
    if (id == null) {
      throw ArgumentError.value(
        courseId,
        'courseId',
        'ID mata kuliah tidak valid',
      );
    }

    final response = await http.get(
      Uri.parse('$baseUrl/attendance/courses/$id/attendances'),
      headers: await _authorizedHeaders(),
    );
    final data = _decodeResponse(response);
    final attendances = data['attendances'] as List<dynamic>;
    return attendances
        .map((item) => StudentAttendance.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  static Future<void> endAttendanceSession(int sessionId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/attendance/sessions/$sessionId/end'),
      headers: await _authorizedHeaders(json: true),
    );
    _decodeResponse(response);
  }

  static Future<List<AttendanceRecord>> getHistory() async {
    final response = await http.get(
      Uri.parse('$baseUrl/attendance/history'),
      headers: await _authorizedHeaders(),
    );
    final data = _decodeResponse(response);
    final history = data['history'] as List<dynamic>;
    return history
        .map((item) => AttendanceRecord.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  static Future<bool> scanQr(String qrToken) async {
    final response = await http.post(
      Uri.parse('$baseUrl/attendance/scan'),
      headers: await _authorizedHeaders(json: true),
      body: jsonEncode({'qr_token': qrToken}),
    );

    if (response.statusCode == 201) return true;
    if (response.statusCode == 409 || response.statusCode == 422) return false;
    _decodeResponse(response);
    return false;
  }

  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    if (token != null && token.isNotEmpty) {
      final response = await http.post(
        Uri.parse('$baseUrl/logout'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );
      if (response.statusCode != 200 && response.statusCode != 401) {
        _decodeResponse(response);
      }
    }

    await prefs.remove('token');
    await prefs.remove('role');
    await prefs.remove('name');
    await prefs.remove('nim');
  }

  static Future<Map<String, String>> _authorizedHeaders({
    bool json = false,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    if (token == null || token.isEmpty) {
      throw StateError('Sesi login tidak ditemukan. Silakan login kembali.');
    }

    return {
      'Accept': 'application/json',
      if (json) 'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    };
  }

  static Map<String, dynamic> _decodeResponse(
    http.Response response, {
    int expectedStatusCode = 200,
  }) {
    if (response.statusCode != expectedStatusCode) {
      throw Exception('Server merespons HTTP ${response.statusCode}.');
    }

    final data = jsonDecode(response.body);
    if (data is! Map<String, dynamic>) {
      throw const FormatException('Respons server bukan objek JSON.');
    }
    return data;
  }
}
