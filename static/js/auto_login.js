(function() {
  var cookies = document.cookie.split('; ');
  var cookieObj = {};
  cookies.forEach(function(cookieStr) {
    var cookieName = cookieStr.split('=')[0];
    var cookieContent = cookieStr.split('=')[1];
    cookieObj[cookieName] = cookieName === 'upintheairAuth' ? JSON.parse(cookieContent) : cookieContent;
  });
  if (cookieObj.hasOwnProperty('upintheairAuth')) {
    $.ajax({
      method: 'POST',
      url: 'api/auto_login.php',
      data: cookieObj['upintheairAuth'],
      success: function(resp) {
        if (!resp || resp.status !== 'success') {
          location.href = '/';
          return;
        }
        window.firstName = resp.data.first_name;
        window.lastName = resp.data.last_name;
        window.email = resp.data.email;
      },
      error: function() { location.href = '/'; }
    })
  }
})();
