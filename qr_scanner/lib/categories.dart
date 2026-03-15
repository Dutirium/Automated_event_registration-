import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'qr_scanner.dart';
import '/core/constants.dart';

const String baseUrl = AppConfig.baseUrl;

/* ========================
   CATEGORY PAGE
======================== */

class CategoryPage extends StatefulWidget {
  const CategoryPage({super.key});

  @override
  State<CategoryPage> createState() => _CategoryPageState();
}

class _CategoryPageState extends State<CategoryPage> {
  late Future<List<dynamic>> categories;

  @override
  void initState() {
    super.initState();
    categories = fetchCategories();
  }

  Future<List<dynamic>> fetchCategories() async {
    final response =
        await http.get(Uri.parse("$baseUrl/get_categories.php"));

    if (response.statusCode != 200) {
      throw Exception("Failed to load categories");
    }

    return jsonDecode(response.body);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF050505),
      appBar: AppBar(
        backgroundColor: const Color(0xFF050505),
        elevation: 0,
        centerTitle: true,
        title: const Text(
          "SELECT_CATEGORY",
          style: TextStyle(
            color: Color(0xFF00F3FF),
            fontWeight: FontWeight.bold,
            letterSpacing: 1.5,
          ),
        ),
      ),
      body: FutureBuilder<List<dynamic>>(
        future: categories,
        builder: (context, snapshot) {

          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(
              child: CircularProgressIndicator(
                color: Color(0xFF00F3FF),
              ),
            );
          }

          if (snapshot.hasError) {
            return Center(
              child: Text(
                "SYSTEM_ERROR",
                style: const TextStyle(color: Colors.redAccent),
              ),
            );
          }

          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return const Center(
              child: Text(
                "NO_CATEGORIES_FOUND",
                style: TextStyle(color: Colors.white70),
              ),
            );
          }

          final data = snapshot.data!;

          return ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: data.length,
            itemBuilder: (context, index) {
              final category = data[index];

              return cyberTile(
                title: category['category_name'],
                icon: Icons.arrow_forward_ios,
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => EventPage(
                        categoryId:
                            int.parse(category['category_id'].toString()),
                        categoryName: category['category_name'],
                      ),
                    ),
                  );
                },
              );
            },
          );
        },
      ),
    );
  }
}

/* ========================
   EVENT PAGE
======================== */

class EventPage extends StatefulWidget {
  final int categoryId;
  final String categoryName;

  const EventPage({
    super.key,
    required this.categoryId,
    required this.categoryName,
  });

  @override
  State<EventPage> createState() => _EventPageState();
}

class _EventPageState extends State<EventPage> {
  late Future<List<dynamic>> events;

  @override
  void initState() {
    super.initState();
    events = fetchEvents();
  }

  Future<List<dynamic>> fetchEvents() async {
    final response = await http.get(
      Uri.parse(
          "$baseUrl/get_events.php?category_id=${widget.categoryId}"),
    );

    if (response.statusCode != 200) {
      throw Exception("Failed to load events");
    }

    return jsonDecode(response.body);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF050505),
      appBar: AppBar(
        backgroundColor: const Color(0xFF050505),
        elevation: 0,
        centerTitle: true,
        title: Text(
          widget.categoryName.toUpperCase(),
          style: const TextStyle(
            color: Color(0xFF00F3FF),
            fontWeight: FontWeight.bold,
            letterSpacing: 1.5,
          ),
        ),
      ),
      body: FutureBuilder<List<dynamic>>(
        future: events,
        builder: (context, snapshot) {

          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(
              child: CircularProgressIndicator(
                color: Color(0xFF00F3FF),
              ),
            );
          }

          if (snapshot.hasError) {
            return const Center(
              child: Text(
                "SYSTEM_ERROR",
                style: TextStyle(color: Colors.redAccent),
              ),
            );
          }

          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return const Center(
              child: Text(
                "NO_EVENTS_FOUND",
                style: TextStyle(color: Colors.white70),
              ),
            );
          }

          final data = snapshot.data!;

          return ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: data.length,
            itemBuilder: (context, index) {
              final event = data[index];

              return cyberTile(
                title: event['event_name'],
                icon: Icons.qr_code_scanner,
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => QRScannerPage(
                        eventId:
                            int.parse(event['event_id'].toString()),
                        eventName: event['event_name'],
                      ),
                    ),
                  );
                },
              );
            },
          );
        },
      ),
    );
  }
}

/* ========================
   CYBER TILE WIDGET
======================== */

Widget cyberTile({
  required String title,
  required IconData icon,
  required VoidCallback onTap,
}) {
  return Container(
    margin: const EdgeInsets.symmetric(vertical: 8),
    decoration: BoxDecoration(
      color: const Color(0xFF0A0A0A),
      border: Border.all(color: const Color(0xFF00F3FF), width: 1.5),
      boxShadow: [
        BoxShadow(
          color: const Color(0xFF00F3FF).withOpacity(0.3),
          blurRadius: 15,
        ),
      ],
    ),
    child: ListTile(
      title: Text(
        title.toUpperCase(),
        style: const TextStyle(
          color: Color(0xFF00F3FF),
          fontWeight: FontWeight.bold,
          letterSpacing: 1.2,
        ),
      ),
      trailing: Icon(
        icon,
        color: const Color(0xFFFF00FF),
      ),
      onTap: onTap,
    ),
  );
}