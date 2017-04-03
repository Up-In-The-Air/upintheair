var API_KEY = '306bcf74-75f0-468d-abae-cc82b5aed993';
var GOOGLE_MAP_API_KEY = 'AIzaSyCdDkk14fyMgdX9DOODWwgwBdXYHDJoCt8';

// For Debug Mode
Vue.config.devtools = true;

var app = new Vue({
  el: '#app',
  data: function() {
    return {
      user: {},
      date: '',
      flightNumber: '',
      depAirport: {
        name: '',
        city: '',
        iata: ''
      },
      arrAirport: {
        name: '',
        city: '',
        iata: ''
      },
      depTime: '',
      arrTime: '',
      airline: {
        iata: '',
        name: ''
      },
      aircraft: {
        iata: '',
        name: ''
      },
      class: '',
      purpose: '',
      seat: '',
      seatNum: '',
      comment: ''
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
      });
    }
  },
  methods: {
    onFlightNumberBlur: function() {
      this.flightNumber = this.flightNumber.toUpperCase();
      var re = /^\d$/;
      for (var i = this.flightNumber.length - 1; i >= 0; i--) {
        if (!re.test(this.flightNumber[i])) {
          this.airline.iata = this.flightNumber.substr(0, i + 1);
          break;
        }
      }

      var _this = this;
      $.ajax({
        method: 'GET',
        url: 'https://iatacodes.org/api/v6/routes.jsonp',
        dataType: "jsonp",
        data: {
          api_key: API_KEY,
          flight_number: this.flightNumber
        },
        success: function(resp) {
          if (resp.response && resp.response[0]) {
            _this.depAirport.iata = resp.response[0].departure;
            _this.arrAirport.iata = resp.response[0].arrival;
            _this.getAirpotDetail(_this.depAirport);
            _this.getAirpotDetail(_this.arrAirport);
          }
        }
      });
      $.ajax({
        method: 'GET',
        url: 'https://iatacodes.org/api/v6/airlines.jsonp',
        dataType: "jsonp",
        data: {
          api_key: API_KEY,
          code: this.airline.iata
        },
        success: function(resp) {
          if (resp.response) {
            _this.airline.name = resp.response[0].name;
            Vue.nextTick(function() {
              Materialize.updateTextFields();
            });
          }
        }
      });
    },
    onJumpTabClick: function(index) {
      $('ul.tabs').tabs('select_tab', 'tab-progress-' + index);
    },
    onAddFlightSubmitClick: function() {
      var missingList = [];
      if (!this.date) { missingList.push('Departure Date'); }
      if (!this.depAirport.iata) { missingList.push('Departure Airport'); }
      if (!this.arrAirport.iata) { missingList.push('Arrival Airport'); }
      if (missingList.length > 0) {
        Materialize.toast('Missing required field(s): ' + missingList.join(', '), 4000);
        return;
      }

      $.ajax({
        method: 'POST',
        url: 'api/add_flight.php',
        data: {
          user_id: this.user.id,
          date: this.date,
          dep_airport_iata: this.depAirport.iata,
          arr_airport_iata: this.arrAirport.iata,
          flight_number: this.flightNumber,
          dep_time: this.depTime,
          arr_time: this.arrTime,
          class: this.class,
          seat: this.seat,
          seat_num: this.seatNum,
          purpose: this.purpose,
          aircraft_iata: this.aircraft.iata,
          airline_iata: this.airline.iata
        },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          Materialize.toast('Add flight successfully! Redirecting...', 3000, '', function() {
            location.href = '/my_flights.html';
          });
        }
      });
    },
    getAirpotDetail: function(airport) {
      var _this = this;
      $.ajax({
        method: 'GET',
        url: 'api/get_airport.php',
        data: { iata: airport.iata },
        success: function(resp) {
          if (resp && resp.status === 'success') {
            airport.name = resp.data.name;
            airport.city = resp.data.city;
          }
          Vue.nextTick(function() {
            Materialize.updateTextFields();
          });
        }
      });
    },
    airportAutocomplete: function(query, tag) {
      var potentialAirportList;
      $.ajax({
        method: 'GET',
        url: 'https://iatacodes.org/api/v6/autocomplete.jsonp',
        dataType: "jsonp",
        data: {
          api_key: API_KEY,
          query: query
        },
        success: function(resp) {
          if (resp.response) {
            potentialAirportList = resp.response.airports_by_cities.concat(resp.response.airports);
            var potentialAiportObj = {};
            potentialAirportList.forEach(function(airport) {
              potentialAiportObj[airport.code + ' / ' + airport.name] = null;
            });
            $(tag).autocomplete({
              data: potentialAiportObj
            });
          }
        }
      });
    },
    onLogoutClick: function() {
      $.ajax({
        method: 'POST',
        url: 'api/log_out.php',
        data: { user_id: this.user.id },
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
    'depAirport.name': function(newVal) {
      if (!newVal) {
        this.depAirport.iata = '';
        this.depAirport.city = '';
      } else if (newVal.includes(' / ')) {
        var iata = newVal.split(' / ')[0];
        var name = newVal.split(' / ')[1];
        this.depAirport.iata = iata;
        this.depAirport.name = name;
        this.getAirpotDetail(this.depAirport);
      } else {
        this.airportAutocomplete(newVal, 'input.dep-airport-autocomplete');
      }
    },
    'arrAirport.name': function(newVal) {
      if (!newVal) {
        this.arrAirport.iata = '';
        this.arrAirport.city = '';
      } else if (newVal.includes(' / ')) {
        var iata = newVal.split(' / ')[0];
        var name = newVal.split(' / ')[1];
        this.arrAirport.iata = iata;
        this.arrAirport.name = name;
        this.getAirpotDetail(this.arrAirport);
      } else {
        this.airportAutocomplete(newVal, 'input.arr-airport-autocomplete');
      }
    },
    'airline.name': function(newVal) {
      if (!newVal) {
        this.airline.iata = '';
      } else if (newVal.includes(' / ')) {
        var iata = newVal.split(' / ')[0];
        var name = newVal.split(' / ')[1];
        this.airline.iata = iata;
        this.airline.name = name;
      }
    },
    'aircraft.name': function(newVal) {
      if (!newVal) {
        this.aircraft.iata = '';
      } else if (newVal.includes(' / ')) {
        var iata = newVal.split(' / ')[0];
        var name = newVal.split(' / ')[1];
        this.aircraft.iata = iata;
        this.aircraft.name = name;
      }
    }
  }
});
