// For Debug Mode
Vue.config.devtools = true;

var app = new Vue({
  el: '#app',
  data: function() {
    return {
      // Login info
      user: {},
      // For sign up
      firstName: '',
      lastName: '',
      email: '',
      password: '',
      confirmedPassword: '',
      isFirstNameValid: true,
      isLastNameValid: true,
      isEmailValid: true,
      isPasswordValid: true,
      isConfirmPasswordValid: true,
      signupError: '',
      // For log in
      loginEmail: '',
      loginPassword: '',
      autoLogin: false
    }
  },
  ready: function() {
    // Auto login
    var cookies = document.cookie.split('; ');
    var cookieObj = {};
    cookies.forEach(function(cookieStr) {
      var cookieName = cookieStr.split('=')[0];
      var cookieContent = cookieStr.split('=')[1];
      cookieObj[cookieName] = cookieName === 'upintheairAuth' ? JSON.parse(cookieContent) : cookieContent;
    });

    var _this = this;
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
          _this.user = {
            firstName: resp.data.first_name,
            lastName: resp.data.last_name,
            email: resp.data.email
          };
        },
        error: function() { location.href = '/'; }
      })
    }
  },
  methods: {
    onSwitchLogIn: function() {
      $('#modal-signup').modal('close');
      $('#modal-login').modal('open');
    },
    onSwitchSignUp: function() {
      $('#modal-login').modal('close');
      $('#modal-signup').modal('open');
    },
    onCancelModal: function(modalId) {
      $(modalId).modal('close');
    },
    onSubmitSignup: function() {
      if (!this.firstName) { this.isFirstNameValid = false; }
      if (!this.lastName) { this.isLastNameValid = false; }
      if (!this.email) { this.isEmailValid = false; }
      if (!this.password) { this.isPasswordValid = false; }
      if (!this.confirmedPassword) { this.isConfirmPasswordValid = false; }

      if (!this.firstName ||
        !this.lastName ||
        !this.email ||
        !this.password ||
        !this.confirmedPassword
      ) { return; }

      if (this.password !== this.confirmedPassword) {
        this.isConfirmPasswordValid = false;
        return;
      }

      var _this = this;
      $.ajax({
        method: 'POST',
        url: 'api/sign_up.php',
        data: {
          first_name: this.firstName,
          last_name: this.lastName,
          email: this.email,
          password: this.password,
          confirmed_password: this.confirmedPassword
        },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            _this.signupError = resp.message;
            return;
          }
          $('#modal-signup').modal('close');
          if (resp.data.cookie) {
            document.cookie = 'upintheairAuth=' + JSON.stringify({ id: resp.data.cookie.id, key: resp.data.cookie.key }) + ' ; expires=' + new Date(resp.data.cookie.expires * 1000);
          }
          Materialize.toast('Sign up successfully! Redirecting...', 3000, '', function() {
            location.href = '/profile.html';
          });
        }
      })
    },
    onSubmitLogin: function() {
      var _this = this;
      $.ajax({
        method: 'POST',
        url: 'api/log_in.php',
        data: {
          email: this.loginEmail,
          password: this.loginPassword,
          auto_login: this.autoLogin
        },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          $('#modal-login').modal('close');
          // Set cookie
          if (_this.autoLogin && resp.data.cookie) {
            document.cookie = 'upintheairAuth=' + JSON.stringify({ id: resp.data.cookie.id, key: resp.data.cookie.key }) + ' ; expires=' + new Date(resp.data.cookie.expires * 1000);
          }
          Materialize.toast('Log in successfully!', 4000, '', function() {
            location.href = '/profile.html';
          });
        }
      })
    },
    onLogoutClick: function() {
      $.ajax({
        method: 'POST',
        url: 'api/log_out.php',
        data: { email: this.user.email },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          document.cookie = 'upintheairAuth=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
          location.href = '/';
        }
      });
    }
  },
  watch: {
    firstName: function() {
      this.isFirstNameValid = true;
      this.signupError = '';
    },
    lastName: function() {
      this.isLastNameValid = true;
      this.signupError = '';
    },
    email: function() {
      this.isEmailValid = true;
      this.signupError = '';
    },
    password: function() {
      this.isPasswordValid = true;
      this.signupError = '';
    },
    confirmedPassword: function() {
      this.isConfirmPasswordValid = true;
      this.signupError = '';
    }
  }
});
