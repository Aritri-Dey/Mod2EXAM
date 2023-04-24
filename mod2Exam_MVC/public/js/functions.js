/**
 * Function to validate data entered by user in signup form , and enter data to
 * database using ajax.
 */
function signUp() {
  var name = document.getElementById("regName");
  var email = document.getElementById("regEmail");
  var phone = document.getElementById("regPhone");
  var password = document.getElementById("regPassword");
  // Validating feilds of form.
  if (name == NULL || !checkNameRegex(name)) {
    $("#err").text("Enter proper username");
    return FALSE;
  }
  else if (email == NULL) {
    $("#err").text("Enter proper email");
    return FALSE;
  }
  else if (phone == NULL || !checkNumberRegex(phone)) {
    $("#err").text("Enter valid phone number");
    return FALSE;
  }
  else if (password == NULL) {
    $("#err").text("Enter password");
    return FALSE;
  }
  // If all fields are filled properly then data is sent to controller and entered
  // in database.
  else {
    $.ajax({
      url: "/addUser",
      type: "POST",
      data: {
        userName: name,
        email: email,
        phone: phone,
        password: password
      },
      datatype: "text",
      success: function(output) {
        $(".newDiv").append(output);
      },
    });
  }
}

/**
 * Function to check validity of name field.
 * 
 *  @return bool
 *    Returns true if name is valid else returns false.
 */
function checkNameRegex(data) {
  const alphabetRegex = /^[a-zA-Z]+$/;
  if (!alphabetRegex.test(data)) {
    return FALSE;
  }
  return TRUE;
}

/**
 * Fucntion to check validity of phone number field.
 * 
 *  @return bool
 *    Returns true if phone number is valid else returns false.
 */
function checkNumberRegex(data) {
  const phoneRegex = /^\+91\d{10}$/;
  if (!phoneRegex.test(data)) {
    return FALSE;
  }
  return TRUE;
}

/**
 * Function to update an entry of the current user using ajax.
 * 
 */
function submitUpdate() {
  var stockName = document.getElementById("stockName");
  var stockPrice = document.getElementById("stockPrice");
  if (stockName != NULL && stockPrice != NULL) {
    $.ajax({
      url: "/updateStock",
      type: "POST",
      data: {
        stockName: stockName,
        stockPrice: stockPricemail,
      },
      datatype: "text",
      success: function(output) {
        $(".newDiv").append(output);
      },
    });
  }
  else {
    $("#err").text("Please fill both fields");
    return FALSE;
  }
}

/**
 * Function to delete an entry made by current logged in user.
 */
function deleteEntry(userId, stockId) {
  $.ajax({
    url: "/deleteStock",
    type: "POST",
    data: {
      userId: userId,
      stockId: stockId,
    },
    success: function(msg) {
      $(".newDiv").append(msg);
    },
  });
}