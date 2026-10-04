import 'package:flutter/material.dart';

class AppColors {
  static const primary = Color(0xFF1769FF);
  static const primaryLight = Color(0xFF4D8DFF);
  static const background = Color(0xFFF5F8FC);
  static const ink = Color(0xFF172B4D);
  static const tint = Color(0xFFE8F0FF);
  static const purple = Color(0xFF7B61FF);
  static const success = Color(0xFF1E9E63);
  static const danger = Color(0xFFE5484D);
  static const warnBg = Color(0xFFFFF3CD);
  static const warnText = Color(0xFF9A6700);

  static const gradient = LinearGradient(
    colors: [primary, primaryLight],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static List<BoxShadow> shadow([double alpha = 0.05]) => [
        BoxShadow(
          color: Colors.black.withValues(alpha: alpha),
          blurRadius: 12,
          offset: const Offset(0, 5),
        ),
      ];
}

class AppTheme {
  static ThemeData get light => ThemeData(
        useMaterial3: true,
        fontFamily: 'Roboto',
        scaffoldBackgroundColor: AppColors.background,
        colorScheme: ColorScheme.fromSeed(seedColor: AppColors.primary),
        appBarTheme: const AppBarTheme(
          backgroundColor: AppColors.background,
          foregroundColor: AppColors.ink,
          elevation: 0,
          scrolledUnderElevation: 0,
          centerTitle: true,
          titleTextStyle: TextStyle(
            color: AppColors.ink,
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
      );
}
