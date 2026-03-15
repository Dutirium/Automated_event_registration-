import 'package:flutter/material.dart';
import 'categories.dart';
import 'attendance_category.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        brightness: Brightness.dark,
        scaffoldBackgroundColor: const Color(0xFF050505),
        fontFamily: 'Courier',
        appBarTheme: const AppBarTheme(
          backgroundColor: Color(0xFF050505),
          elevation: 0,
          centerTitle: true,
          titleTextStyle: TextStyle(
            color: Color(0xFF00F3FF),
            fontSize: 18,
            fontWeight: FontWeight.bold,
            letterSpacing: 1.5,
          ),
        ),
      ),
      home: const Menu(),
    );
  }
}

class Menu extends StatelessWidget {
  const Menu({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("SYSTEM_ACCESS_PANEL"),
      ),
      body: Stack(
        children: [
          const GridBackground(),
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                const SizedBox(height: 60),

                buildMenuCard(
                  context,
                  title: "SCAN_QR_CODE",
                  subtitle: "Verify attendance by event",
                  icon: Icons.qr_code_scanner,
                  page: const CategoryPage(),
                ),

                const SizedBox(height: 25),

                buildMenuCard(
                  context,
                  title: "VIEW_ATTENDANCE",
                  subtitle: "See all participating contestants",
                  icon: Icons.list_alt,
                  page: const AttendanceCategoryPage(),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget buildMenuCard(
    BuildContext context, {
    required String title,
    required String subtitle,
    required IconData icon,
    required Widget page,
  }) {
    return InkWell(
      onTap: () {
        Navigator.push(context, MaterialPageRoute(builder: (_) => page));
      },
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: const Color(0xFF0A0A0A),
          border: Border.all(color: const Color(0xFF00F3FF), width: 1.5),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF00F3FF).withOpacity(0.3),
              blurRadius: 15,
              spreadRadius: 1,
            ),
          ],
        ),
        child: Row(
          children: [
            Icon(icon, size: 30, color: const Color(0xFF00F3FF)),
            const SizedBox(width: 20),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      color: Color(0xFF00F3FF),
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 1.2,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    subtitle,
                    style: const TextStyle(
                      color: Colors.white70,
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ),
            const Icon(Icons.arrow_forward_ios,
                size: 16, color: Color(0xFFFF00FF)),
          ],
        ),
      ),
    );
  }
}

/* =========================
   GRID BACKGROUND EFFECT
========================= */

class GridBackground extends StatelessWidget {
  const GridBackground({super.key});

  @override
  Widget build(BuildContext context) {
    return CustomPaint(
      size: Size.infinite,
      painter: GridPainter(),
    );
  }
}

class GridPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = const Color(0xFF00F3FF).withOpacity(0.05)
      ..strokeWidth = 1;

    const double gridSize = 30;

    for (double i = 0; i < size.width; i += gridSize) {
      canvas.drawLine(Offset(i, 0), Offset(i, size.height), paint);
    }

    for (double i = 0; i < size.height; i += gridSize) {
      canvas.drawLine(Offset(0, i), Offset(size.width, i), paint);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}