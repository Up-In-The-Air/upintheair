// For Debug Mode
Vue.config.devtools = true;

var app = new Vue({
  el: '#app',
  data: function() {
    return {
      user: {},
      flightList: [{
        date: '2017-01-12',
        flight_num: 'AA3696',
        from: 'ORD',
        to: 'CMI',
        dep_time: '20.05',
        arr_time: '20.53',
        airline: 'AAL',
        aircraft: 'E145',
        seat: '3B(M)',
        comment: ''
      }, {
        date: '2017-01-12',
        flight_num: 'AA288',
        from: 'PVG',
        to: 'ORD',
        dep_time: '18:20',
        arr_time: '17:45',
        airline: 'AAL',
        aircraft: 'B788',
        seat: '30L(W)',
        comment: ''
      }],
      editMode: false
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
            email: resp.data.email,
            id: resp.data.id
          };
        },
        error: function() { location.href = '/'; }
      })
    }

    this.getFlightLists();
  },
  methods: {
    getFlightLists: function() {
      var _this = this;
      // $.ajax({
      //   method: 'POST',
      //   url: 'api/get_flights.php',
      //   data: { id: this.user.id },
      //   success: function(resp) {
      //     if (!resp || resp.status !== 'success') {
      //       Materialize.toast(resp.message, 4000);
      //       return;
      //     }
      //     Materialize.toast('Delete record successfully', 4000);
      //   },
      //   error: function() {
      //     Materialize.toast('Fail to delete the record', 4000);
      //   }
      // });
    },
    onFlightRecordEdit: function(flightId) {

    },
    onFlightRecordDelete: function(flightId) {
      var _this = this;
      // $.ajax({
      //   method: 'POST',
      //   url: 'api/delete_flight.php',
      //   data: { flight_id: flightId },
      //   success: function(resp) {
      //     if (!resp || resp.status !== 'success') {
      //       Materialize.toast(resp.message, 4000);
      //       return;
      //     }
      //     Materialize.toast('Delete record successfully', 4000);
      //   },
      //   error: function() {
      //     Materialize.toast('Fail to delete the record', 4000);
      //   }
      // });
    }
  }
});
