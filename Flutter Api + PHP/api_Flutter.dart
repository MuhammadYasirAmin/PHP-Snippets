Future<String> emi_Payments(
    String userID,
    String emiAmount,
    String card_owner_name,
    String card_owner_number,
    String card_MM,
    String card_DD,
    String card_owner_CVC) async {
  try {
    final response = await http.post(Uri.parse(apiURL), body: {
      'userID': userID,
      'emiAmount': emiAmount,
      'card_owner_name': card_owner_name,
      'card_owner_number': card_owner_number,
      'card_MM': card_MM,
      'card_DD': card_DD,
      'card_owner_CVC': card_owner_CVC
    });

    if (response.statusCode == 200) {
      final result = json.decode(response.body);
      return result;
      print(result);
    } else {
      return 'Server Error';
    }
  } catch (e) {
    return 'App Error';
  }
}
