// For Debug Mode
Vue.config.devtools = true;

var app = new Vue({
  el: '#app',
  data: function() {
    return {
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
