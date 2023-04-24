/**
 * Function for frontend validation of login form.
 * 
 *  @return bool
 *    TRUE or FLASE depending on condition satisfaction. 
 */
function checkValid() {
  var userNameLogin = document.forms["loginForm"]["userName"].value;
  var emailLogin = document.forms["loginForm"]["email"].value;
  var passwordLogin = document.forms["loginForm"]["password"].value;

  if (userNameLogin == null) {
    $("#err").text("Enter username");
     return false;
  }
  else if (emailLogin == null) {
    $("#err").text("Enter a email id");
    return false;
  }
  else if (passwordLogin == null) {
    $("#err").text("Enter a password");
    return false;
  }
}
