import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl =  'http://192.168.1.9:8000/api';

  static Future<bool> login(String nim, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'nim': nim,
        'password': password,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body) as Map<String, dynamic>;
      final token = data['token'] as String?;
      final user = data['user'] as Map<String, dynamic>?;

      if (token == null || user == null) {
        return false;
      }

      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('token', token);
      await prefs.setString('role', user['role']?.toString() ?? '');

      return true;
    }

    return false;
  }

  static Future<List<dynamic>> getHistory() async {
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('token');

  final response = await http.get(
    Uri.parse('$baseUrl/attendance/history'),
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    },
  );

  if (response.statusCode == 200) {
    return jsonDecode(response.body)['history'];
  }

  throw Exception('Gagal mengambil riwayat absensi');
}

  static Future<bool> scanQr(String qrToken) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    if (token == null || token.isEmpty) {
      return false;
    }

    final response = await http.post(
      Uri.parse('$baseUrl/attendance/scan'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({
        'qr_token': qrToken,
      }),
    );

    return response.statusCode == 201;
  }
}
