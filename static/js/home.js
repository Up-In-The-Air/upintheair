var app = new Vue({
  el: '#app',
  data: function() {
    return {
      firstName: '',
      lastName: '',
      email: '',
      password: '',
      confirmedPassword: ''
    }
  },
  methods: {
    onSubmitSignup: function() {
      $.ajax({
        method: 'POST',
        url: 'sign_up.php',
        data: {
          first_name: this.firstName,
          last_name: this.lastName,
          email: this.email,
          password: this.password,
          confirmed_password: this.confirmedPassword
        },
        success: function(resp) {
          if (!resp || resp !== 'success') {
            Materialize.toast(resp.message, 4000)
            return;
          }
          // TODO: Success signed up, redirect to profile
        }
      })
    }
  }
});
