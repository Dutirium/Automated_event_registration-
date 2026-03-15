//lib/core/constants.dart

class AppConfig {
  //domain of the website afte hosting below is an example of local hosting chanes with hotspot used
  static const String baseUrl = "http://10.186.242.67/event_registration_website/backend";
  
  // website api other endpoints
  static const String attendanceUrl = "$baseUrl/attendance.php";
  static const String verifyUrl = "$baseUrl/verify.php";
}