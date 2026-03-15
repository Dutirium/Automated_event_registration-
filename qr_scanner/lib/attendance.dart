import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:audioplayers/audioplayers.dart';
import '/core/constants.dart';

const String baseUrl = AppConfig.baseUrl;

class AttendancePage extends StatefulWidget {
  final int eventId;
  final String eventName;
  final Map? lastResponse;

  const AttendancePage({
    super.key,
    required this.eventId,
    required this.eventName,
    this.lastResponse,
  });

  @override
  State<AttendancePage> createState() => _AttendancePageState();
}

class _AttendancePageState extends State<AttendancePage>
    with SingleTickerProviderStateMixin {
  List verified = [];
  List absent = [];
  bool loading = true;

  bool showBanner = false;
  late AnimationController _controller;
  late Animation<Offset> _slideAnimation;

  final AudioPlayer _audioPlayer = AudioPlayer();

  @override
  void initState() {
    super.initState();
    fetchAttendance();

    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 400),
    );

    _slideAnimation =
        Tween<Offset>(begin: const Offset(0, -1), end: const Offset(0, 0))
            .animate(CurvedAnimation(
      parent: _controller,
      curve: Curves.easeOut,
    ));

    if (widget.lastResponse != null) {
      showBanner = true;
      _controller.forward();
      playSound(widget.lastResponse?["status"]);
      Future.delayed(const Duration(seconds: 3), () {
        if (mounted) _controller.reverse();
      });
    }
  }

  Future<void> playSound(String? status) async {
    if (status == "success") {
      await _audioPlayer.play(AssetSource('success.mp3'));
    }
  }

  Future<void> fetchAttendance() async {
    setState(() => loading = true);

    try {
      final response = await http.get(
        Uri.parse("$baseUrl/attendence.php?event_id=${widget.eventId}"),
      );

      final data = jsonDecode(response.body);

      setState(() {
        verified = data["verified"] ?? [];
        absent = data["absent"] ?? [];
        loading = false;
      });
    } catch (_) {
      setState(() => loading = false);
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    _audioPlayer.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final status = widget.lastResponse?["status"];
    final message = widget.lastResponse?["message"];

    Color bannerColor;

    if (status == "success") {
      bannerColor = const Color(0xFF00F3FF);
    } else if (status == "already") {
      bannerColor = const Color(0xFFFF00FF);
    } else {
      bannerColor = Colors.redAccent;
    }

    return Scaffold(
      backgroundColor: const Color(0xFF050505),
      appBar: AppBar(
        backgroundColor: const Color(0xFF050505),
        elevation: 0,
        centerTitle: true,
        title: Text(
          "ATTENDANCE: ${widget.eventName.toUpperCase()}",
          style: const TextStyle(
            color: Color(0xFF00F3FF),
            fontWeight: FontWeight.bold,
            letterSpacing: 1.2,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Color(0xFFFF00FF)),
            onPressed: fetchAttendance,
          ),
        ],
      ),
      body: Stack(
        children: [

          /// Main Content
          loading
              ? const Center(
                  child: CircularProgressIndicator(
                    color: Color(0xFF00F3FF),
                  ),
                )
              : RefreshIndicator(
                  color: const Color(0xFF00F3FF),
                  backgroundColor: Colors.black,
                  onRefresh: fetchAttendance,
                  child: ListView(
                    padding: const EdgeInsets.all(16),
                    children: [

                      const SizedBox(height: 60),

                      /// VERIFIED SECTION
                      Text(
                        "VERIFIED (${verified.length})",
                        style: const TextStyle(
                          color: Color(0xFF00F3FF),
                          fontWeight: FontWeight.bold,
                          letterSpacing: 1.5,
                        ),
                      ),
                      const SizedBox(height: 10),

                      ...verified.map(
                        (s) => cyberCard(
                          name: s["name"],
                          subtitle: s["verified_at"],
                          isVerified: true,
                        ),
                      ),

                      const SizedBox(height: 30),

                      /// ABSENT SECTION
                      Text(
                        "ABSENT (${absent.length})",
                        style: const TextStyle(
                          color: Colors.redAccent,
                          fontWeight: FontWeight.bold,
                          letterSpacing: 1.5,
                        ),
                      ),
                      const SizedBox(height: 10),

                      ...absent.map(
                        (s) => cyberCard(
                          name: s["name"],
                          subtitle: null,
                          isVerified: false,
                        ),
                      ),
                    ],
                  ),
                ),

          /// Slide Banner
          if (showBanner && message != null)
            Positioned(
              top: 0,
              left: 0,
              right: 0,
              child: SlideTransition(
                position: _slideAnimation,
                child: Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: bannerColor,
                    boxShadow: [
                      BoxShadow(
                        color: bannerColor.withOpacity(0.6),
                        blurRadius: 20,
                      ),
                    ],
                  ),
                  child: SafeArea(
                    child: Text(
                      message.toUpperCase(),
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        color: Colors.black,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 1.5,
                      ),
                    ),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget cyberCard({
    required String name,
    required String? subtitle,
    required bool isVerified,
  }) {
    final borderColor =
        isVerified ? const Color(0xFF00F3FF) : Colors.redAccent;

    return Container(
      margin: const EdgeInsets.symmetric(vertical: 6),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF0A0A0A),
        border: Border.all(color: borderColor, width: 1.5),
        boxShadow: [
          BoxShadow(
            color: borderColor.withOpacity(0.3),
            blurRadius: 15,
          ),
        ],
      ),
      child: Row(
        children: [
          Icon(
            isVerified ? Icons.check_circle : Icons.cancel,
            color: borderColor,
          ),
          const SizedBox(width: 15),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name.toUpperCase(),
                  style: TextStyle(
                    color: borderColor,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                if (subtitle != null)
                  Text(
                    "VERIFIED_AT: $subtitle",
                    style: const TextStyle(
                      color: Colors.white70,
                      fontSize: 12,
                    ),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}