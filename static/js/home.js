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
      isConfirmPasswordValid: true
      // TODO: For log in
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
          $('#modal-signup').modal('close');
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          Materialize.toast('Sign up successfully!', 4000, '', function() {
            // TODO: Success signed up, redirect to profile
          });
        }
      })
    },
    onSubmitLogin: function() {
      // TODO
    }
  },
  watch: {
    firstName: function() { this.isFirstNameValid = true; },
    lastName: function() { this.isLastNameValid = true; },
    email: function() { this.isEmailValid = true; },
    password: function() { this.isPasswordValid = true; },
    confirmedPassword: function() { this.isConfirmPasswordValid = true; }
  }
});
