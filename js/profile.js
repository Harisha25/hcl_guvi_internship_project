$(function(){
  function showAlert(msg, type='info') {
    $('#profileAlert').html('<div class="alert alert-' + type + '">' + msg + '</div>');
  }

  const token = localStorage.getItem('auth_token');
  if (!token) {
    // not logged in, redirect to login
    window.location.href = 'login.html';
  }

  // fetch profile
  $.ajax({
    url: '../php/profile_api.php',
    method: 'GET',
    dataType: 'json',
    data: { action: 'get_profile' },
    beforeSend: function(xhr){
      xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },
    success: function(resp){
      if (resp.success && resp.user) {
        $('#fullname').val(resp.user.fullname);
        $('#email').val(resp.user.email);
        $('#age').val(resp.user.age);
        $('#dob').val(resp.user.dob);
        $('#contact').val(resp.user.contact);
      } else {
        localStorage.removeItem('auth_token');
        window.location.href = 'login.html';
      }
    },
    error: function(){
      showAlert('Failed to load profile','danger');
    }
  });

  $('#saveProfileBtn').on('click', function(){
    const data = {
      action: 'update_profile',
      fullname: $('#fullname').val().trim(),
      age: $('#age').val(),
      dob: $('#dob').val(),
      contact: $('#contact').val()
    };
    $.ajax({
      url: '../php/profile_api.php',
      method: 'POST',
      dataType: 'json',
      data: data,
      beforeSend: function(xhr){
        xhr.setRequestHeader('Authorization', 'Bearer ' + token);
      },
      success: function(resp){
        if (resp.success) {
          showAlert('Profile updated successfully','success');
        } else {
          showAlert(resp.error || 'Update failed','danger');
        }
      },
      error: function(){
        showAlert('Server error','danger');
      }
    });
  });

  $('#logoutBtn').on('click', function(){
    // remove local token and inform backend to delete token
    const token = localStorage.getItem('auth_token');
    if (token) {
      $.ajax({
        url: '../php/profile_api.php',
        method: 'POST',
        dataType: 'json',
        data: { action: 'logout' },
        beforeSend: function(xhr){
          xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        },
        complete: function(){
          localStorage.removeItem('auth_token');
          localStorage.removeItem('auth_email');
          window.location.href = 'login.html';
        }
      });
    } else {
      localStorage.removeItem('auth_token');
      window.location.href = 'login.html';
    }
  });
});
