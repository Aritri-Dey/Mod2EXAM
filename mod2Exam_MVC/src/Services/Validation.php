<?php
  namespace App\Services;
  /**
   * Class to check backend validation of form fields.
   */
  class Validation 
  {

    /**
     * Function to check if data contains only alphabets.
     * 
     *   @param string $data
     *     Stores username filed of form.
     *   
     *   @return bool
     *     Returns TRUE if $data has only alphabets, else returns FALSE.
     */
    function validateName($data) {
      if (!preg_match("/^[a-zA-Z-' ]*$/" , $data)) {
        echo "Only letters and white space allowed";
        return FALSE;
      } 
      return TRUE;
    }

    /**
     * Function to check if data contains only numbers and + sybmbol
     * and length is not more than 13 .
     * 
     *   @param string $data
     *     Stores phone number of user.
     *   
     *   @return bool
     *     Returns TRUE if conditions are satisfied, else returns FALSE.
     */
    function validateNo($data) {
      if (!preg_match('/^[0-9+]{13}+$/', $data)) {
        return FALSE;
      } 
      return TRUE;
    }

    /**
     * Function to check if field is empty.
     * 
     *  @param string $data
     *    Stores data to be validated.
     * 
     *  @return bool
     *     Returns TRUE if field is not empty, else returns FALSE.
     */
    function checkEmpty($data) {
      if($data == NULL) {
        return FALSE;
      }
      return TRUE;
    }

    /**
     * Funciton to check whether the email id enterd by user is valid or not.
     * 
     *  @param string $mail
     *    Stores email id entered by user.
     * 
     *  @return bool
     *     Returns TRUE if email address is valid, else returns FALSE.
     */
    public function checkMail($emailAddress) {
      $password = $_ENV['API_KEY'];
      // Set API Access Key.
      $curl = curl_init();
      curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.apilayer.com/email_verification/check?email=$emailAddress",
        CURLOPT_HTTPHEADER => array(
          "Content-Type: text/plain",
          "apikey: $password"
        ),
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      $validationResult = json_decode($response, TRUE);
      // If validation is successful then returns TRUE, else returns FALSE.
      if ($validationResult != NULL && $validationResult["smtp_check"]) {
        return TRUE; 
      }
      return FALSE;
    }
  }
?>
