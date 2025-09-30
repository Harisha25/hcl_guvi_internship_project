$(function(){
  $('#registerBtn').on('click', function(){
    $('#registerAlert').html('');
    const data = {
      fullname: $('#fullname').val().trim(),
      email: $('#email').val().trim(),
      password: $('#password').val(),
      age: $('#age').val(),
      dob: $('#dob').val(),
      contact: $('#contact').val()
    };

    // basic front validation
    if (!data.fullname || !data.email || !data.password) {
      $('#registerAlert').html('<div class="alert alert-danger">Name, email and password are required.</div>');
      return;
    }

    $.ajax({
      url: '../php/register.php',
      method: 'POST',
      dataType: 'json',
      data: data,
      success: function(resp){
        if (resp.success) {
          $('#registerAlert').html('<div class="alert alert-success">Registered successfully. Redirecting to login...</div>');
          setTimeout(()=> window.location.href = 'login.html', 900);
        } else {
          $('#registerAlert').html('<div class="alert alert-danger">' + (resp.error || 'Registration failed') + '</div>');
        }
      },
      error: function(xhr){
        $('#registerAlert').html('<div class="alert alert-danger">Server error.</div>');
      }
    });
  });
});
