import 'package:absensi_mahasiswa/main.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('Login page is shown', (tester) async {
    await tester.pumpWidget(const AbsensiApp());

    expect(find.text('MASUK'), findsOneWidget);
  });
}
