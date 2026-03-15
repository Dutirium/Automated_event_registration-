import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:http/http.dart' as http;
import 'attendance.dart';
import '/core/constants.dart';

const String baseUrl = AppConfig.baseUrl;

class QRScannerPage extends StatefulWidget {
  final int eventId;
  final String eventName;

  const QRScannerPage({
    super.key,
    required this.eventId,
    required this.eventName,
  });

  @override
  State<QRScannerPage> createState() => _QRScannerPageState();
}

class _QRScannerPageState extends State<QRScannerPage> {
  final MobileScannerController controller = MobileScannerController();
  bool isProcessing = false;

  Future<void> verifyQR(String qrData) async {
    if (isProcessing) return;

    setState(() => isProcessing = true);

    try {
      final response = await http.post(
        Uri.parse("$baseUrl/verify.php"),
        body: {
          "token": qrData,
          "event_id": widget.eventId.toString(),
        },
      );

      final data = jsonDecode(response.body);

      await controller.stop();

      await Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) => AttendancePage(
            eventId: widget.eventId,
            eventName: widget.eventName,
            lastResponse: data,
          ),
        ),
      );

      await controller.start();

    } catch (e) {
      debugPrint("Scan error: $e");
    }

    setState(() => isProcessing = false);
  }

  @override
  void dispose() {
    controller.dispose();
    super.dispose();
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
          "SCAN_MODE: ${widget.eventName.toUpperCase()}",
          style: const TextStyle(
            color: Color(0xFF00F3FF),
            fontWeight: FontWeight.bold,
            letterSpacing: 1.2,
          ),
        ),
      ),
      body: Stack(
        children: [

          /// Camera View
          MobileScanner(
            controller: controller,
            onDetect: (barcodeCapture) {
              if (isProcessing) return;

              final barcode = barcodeCapture.barcodes.first;
              final String? code = barcode.rawValue;

              if (code != null) {
                verifyQR(code);
              }
            },
          ),

          /// Neon Scanner Frame Overlay
          Center(
            child: Container(
              width: 260,
              height: 260,
              decoration: BoxDecoration(
                border: Border.all(
                  color: const Color(0xFF00F3FF),
                  width: 2,
                ),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF00F3FF).withOpacity(0.5),
                    blurRadius: 20,
                    spreadRadius: 2,
                  ),
                ],
              ),
            ),
          ),

          /// Top Instruction Text
          Positioned(
            top: 100,
            left: 0,
            right: 0,
            child: Text(
              "ALIGN_QR_WITHIN_FRAME",
              textAlign: TextAlign.center,
              style: TextStyle(
                color: const Color(0xFFFF00FF).withOpacity(0.8),
                letterSpacing: 2,
                fontSize: 12,
              ),
            ),
          ),
        ],
      ),
    );
  }
}