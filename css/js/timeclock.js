function clockIn() {
  // Get the spreadsheet by ID
  var ss = SpreadsheetApp.openById("1RNd14QKV-cs40CKkxzuf2GjPYM-OLQf5FqDXR6wEyx4"); // Replace with your spreadsheet ID
  var sheet = ss.getSheetByName("Time Clock Data");
 
  // Get the last row with data
  var lastRow = sheet.getLastRow();

 
   // Get the current time with Moment.js
  var now = Moment.moment(); 
  Logger.log(now.format('MMMM Do YYYY, h:mm:ss a'))

  // Format the time
  var formattedTime = now.format("hh:mm a"); 


  // Get and format the date with Moment.js
var formattedDate = now.format("MMM D, YYYY");  

  // Find the last clock-in/out entry for the current user
  for (var i = lastRow; i > 0; i--) {
    var employee = sheet.getRange(i, 1).getValue();
      if (employee == Session.getActiveUser().getEmail()) {
      var clockInTime = sheet.getRange(i, 3).getValue();
      var clockOutTime = sheet.getRange(i, 4).getValue();

      if (clockInTime instanceof Date && !(clockOutTime instanceof Date)) {
      // Already clocked in
      Browser.msgBox("You already clocked in on " + formattedDate + " at " + formattedTime + ". Please clock out first.");
      return;
       } else {
        /// Clock in
        sheet.getRange(lastRow + 1, 1).setValue(Session.getActiveUser().getEmail());
        sheet.getRange(lastRow + 1, 2).setValue(formattedDate);
        sheet.getRange(lastRow + 1, 3).setValue(formattedTime);
        Browser.msgBox("You have clocked in on "+ formattedDate + " at " + formattedTime);
        return;
      }        
    }
  }
}
function clockOut() {
  // Get the spreadsheet by ID
  var ss = SpreadsheetApp.openById("1RNd14QKV-cs40CKkxzuf2GjPYM-OLQf5FqDXR6wEyx4"); 
  var sheet = ss.getSheetByName("Time Clock Data");
  
  // Get the last row with data
  var lastRow = sheet.getLastRow();
  
  // Get the current time with Moment.js
  var now = Moment.moment(); 
  Logger.log(now.format('MMMM Do YYYY, h:mm:ss a'));

  // Format the time to "hh:mm a" (time format, no date)
  var formattedTime = now.format("hh:mm a");
  
  // Get and format the date with Moment.js (to show in the message)
  var formattedDate = now.format("MMM D, YYYY");   

  // Find the last clock-in/out entry for the current user
  for (var i = lastRow; i > 0; i--) {
    var employee = sheet.getRange(i, 1).getValue(); // Column 1: Employee email
    if (employee == Session.getActiveUser().getEmail()) {
      var clockInTime = sheet.getRange(i, 3).getValue(); // Column 3: Clock-in time
      var clockOutTime = sheet.getRange(i, 4).getValue(); // Column 4: Clock-out time

      if (clockInTime && !clockOutTime) {
        // Clock out
        sheet.getRange(i, 4).setValue(formattedTime); // Set clock-out time as current time

        // Subtract clock-out time (D) from clock-in time (C) and set the result in column E
        var durationFormula = "=D" + i + " - C" + i;
        sheet.getRange(i, 5).setFormula(durationFormula); // Column 5: Duration formula (simple subtraction)

        Browser.msgBox("Stay Awesome. You have clocked out on " + formattedDate + " at " + formattedTime);
        return;
      } else {
        // If no previous entry found for the user
        Browser.msgBox("OOPS!! You gotta clock in before you can clock out."); 
        return;
      }
    }
  } 
}
