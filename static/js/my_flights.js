var CLASS_MAP = {
  0: 'Economy',
  1: 'Economy+',
  2: 'Business',
  3: 'First'
};
var PURPOSE_MAP = {
  0: 'Leisure',
  1: 'Business',
  2: 'Crew',
  3: 'Others'
};
var SEAT_MAP = {
  0: 'W',
  1: 'M',
  2: 'A'
};

// For Debug Mode
Vue.config.devtools = true;

var app = new Vue({
  el: '#app',
  data: function() {
    return {
      user: {},
      classMap: CLASS_MAP,
      purposeMap: PURPOSE_MAP,
      seatMap: SEAT_MAP,
      flightList: [],
      editMode: false,
      editIndex: 0,
      updateDate: '',
      updateDepTime: '',
      updateArrTime: '',
      updateClass: '',
      updatePurpose: ''
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
          _this.getFlightLists();
        },
        error: function() { location.href = '/'; }
      });
    }
  },
  methods: {
    isRecordEditting: function(index) {
      return this.editMode && index == this.editIndex;
    },
    getFlightLists: function() {
      var _this = this;
      $.ajax({
        method: 'GET',
        url: 'api/get_flights.php',
        data: { user_id: this.user.id },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          _this.flightList = resp.data;
          Vue.nextTick(function() {
            $('.tooltipped').tooltip({ delay: 50 });
            $('.datepicker').pickadate({
              selectMonths: true,
              selectYears: 15,
              format: 'yyyy-mm-dd'
            });
            $('.timepicker').pickatime({
              autoclose: false,
              twelvehour: false
            });
            $('select').material_select();
          });
        }
      });
    },
    onFlightRecordEditClick: function(index, flightId) {
      this.editMode = true;
      this.editIndex = index;
      this.updateDate = this.flightList[index].date;
      this.updateDepTime = this.flightList[index].dep_time;
      this.updateArrTime = this.flightList[index].arr_time;
      this.updateClass = this.flightList[index].class;
      this.updatePurpose = this.flightList[index].purpose;
      $('select').material_select();
    },
    onFlightRecordUpdateCancel: function() {
      this.editMode = false;
      this.editIndex = 0;
    },
    onFlightRecordUpdateConfirm: function() {
      var _this = this;
      $.ajax({
        method: 'POST',
        url: 'api/update_flight.php',
        data: {
          flight_id: this.flightList[this.editIndex].id,
          date: this.updateDate,
          dep_time: this.updateDepTime,
          arr_time: this.updateArrTime,
          class: this.updateClass,
          purpose: this.updatePurpose
        },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          Materialize.toast('Update record successfully', 3000);
          _this.getFlightLists();
        }
      });
      this.editMode = false;
      this.editIndex = 0;
    },
    onFlightRecordDeleteClick: function(flightId) {
      var _this = this;
      $.ajax({
        method: 'POST',
        url: 'api/delete_flight.php',
        data: { flight_id: flightId },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          Materialize.toast('Delete record successfully', 3000);
          _this.getFlightLists();
        }
      });
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
  }
});
