
$(document).ready(function() {
    // When page loads for the first time
    // showLogin();

    // // when a 'read products' button was clicked
    // $(document).on('click', '.read-products-button', function(){
    //     showProducts();
    // });

});


function showLogin() {

  // html for listing products
  var read_login_html =
  `<div id='read-products' class='btn btn-primary pull-right m-b-15px read-products-button'>
      <span class='glyphicon glyphicon-list'></span> Read Products
  </div>

  <!-- 'create product' html form -->
  <form id='login-form' action='#' method='post' border='0'>
      <table class='table table-hover table-responsive table-bordered'>

          <!-- name field -->
          <tr>
              <td>User Emil</td>
              <td><input type='text' name='email' class='form-control' required /></td>
          </tr>

          <!-- price field -->
          <tr>
              <td>Password</td>
              <td><input type='text' name='password' class='form-control' required /></td>
          </tr>


          <!-- button to submit form -->
          <tr>
              <td></td>
              <td>
                  <button id='goodLogin' type='submit' class='btn btn-primary'>
                      <span class='glyphicon glyphicon-plus'></span> Login
                  </button>
              </td>
          </tr>

      </table>
  </form>`;

// inject to 'page-content' of our app
$("#page-content").html(read_login_html);

// chage page title
changePageTitle("Login page");


$(document).on('submit', '#login-form', function(){

var form_data = JSON.stringify($(this).serializeObject());

  $.ajax({
      url: "api/form/login.php",
      type : "POST",
      contentType : 'application/json',
      dataType : 'json',
      data : form_data,
      success : function(data) {
      sessionStorage.jwt = data['jwt'];
      sessionStorage.expire_claim = data['expire_claim'];
      // alert('Successfully retrieved token from the server! Token: ' + data['jwt']);
       showProducts();
      },error: function() {
      alert("Error: Login Failed Test");
      }
  });
return false;
});

}
