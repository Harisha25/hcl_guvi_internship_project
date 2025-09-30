$(function(){
  $('#loginBtn').on('click', function(){
    $('#loginAlert').html('');
    const data = {
      email: $('#email').val().trim(),
      password: $('#password').val()
    };
    if (!data.email || !data.password) {
      $('#loginAlert').html('<div class="alert alert-danger">Email and password required.</div>');
      return;
    }

    $.ajax({
      url: '../php/login.php',
      method: 'POST',
      dataType: 'json',
      data: data,
      success: function(resp){
        if (resp.success && resp.token) {
          // store token in localStorage per requirements
          localStorage.setItem('auth_token', resp.token);
          // also store minimal user email for convenience
          localStorage.setItem('auth_email', resp.email);
          window.location.href = 'profile.html';
        } else {
          $('#loginAlert').html('<div class="alert alert-danger">' + (resp.error || 'Login failed') + '</div>');
        }
      },
      error: function(){
        $('#loginAlert').html('<div class="alert alert-danger">Server error.</div>');
      }
    });
  });
});
