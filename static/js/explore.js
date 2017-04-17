var API_KEY = '306bcf74-75f0-468d-abae-cc82b5aed993';

// For Debug Mode
Vue.config.devtools = true;

var app = new Vue({
  el: '#app',
  data: function() {
    return {
      user: {},
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
      airline: {
        iata: '',
        name: ''
      },
      airport: {
        name: '',
        city: '',
        iata: ''
      }
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
    getAirpotDetail: function(airport, callback) {
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
          if (callback) {
            callback();
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
    'airport.name': function(newVal) {
      if (!newVal) {
        this.airport.iata = '';
        this.airport.city = '';
      } else if (newVal.includes(' / ')) {
        var iata = newVal.split(' / ')[0];
        var name = newVal.split(' / ')[1];
        this.airport.iata = iata;
        this.airport.name = name;
        this.getAirpotDetail(this.airport);
      } else {
        this.airportAutocomplete(newVal, 'input.airport-autocomplete');
      }
    }
  }
});
