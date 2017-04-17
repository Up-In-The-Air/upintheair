var API_KEY = '306bcf74-75f0-468d-abae-cc82b5aed993';

// For Debug Mode
Vue.config.devtools = true;

var app = new Vue({
  el: '#app',
  data: function() {
    return {
      user: {},
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
      airline: {
        iata: '',
        name: ''
      },
      airport: {
        name: '',
        city: '',
        iata: ''
      },
      flightComments: [],
      averageFlightRate: 0,
      flightRouteComments: [],
      averageFlightRouteRate: 0,
      airlineComments: [],
      averageAirlineRate: 0,
      airportComments: [],
      averageAirportRate: 0
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
    onFlightSearch: function() {
      if (!this.flightNumber) {
        Materialize.toast('Please provide flight number', 4000);
        return;
      }
      var _this = this;
      $.ajax({
        method: 'GET',
        url: 'api/explore_flights.php',
        data: { flight_number: this.flightNumber.toUpperCase() },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          _this.flightComments = resp.data.comments;
          _this.averageFlightRate = parseFloat(resp.data.average_rate);
        }
      });
    },
    onFlightRouteSearch: function() {
      if (!this.depAirport.iata || !this.arrAirport.iata) {
        Materialize.toast('Please provide departure or arrival airport', 4000);
        return;
      }
      var _this = this;
      $.ajax({
        method: 'GET',
        url: 'api/explore_routes.php',
        data: {
          dep_airport_iata: this.depAirport.iata,
          arr_airport_iata: this.arrAirport.iata
        },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          _this.flightRouteComments = resp.data.comments;
          _this.averageFlightRouteRate = parseFloat(resp.data.average_rate);
        }
      });
    },
    onAirlineSearch: function() {
      if (!this.airline.iata) {
        Materialize.toast('Please provide airline', 4000);
        return;
      }
      var _this = this;
      $.ajax({
        method: 'GET',
        url: 'api/explore_airlines.php',
        data: { airline_iata: this.airline.iata },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          _this.airlineComments = resp.data.comments;
          _this.averageAirlineRate = parseFloat(resp.data.average_rate);
        }
      });
    },
    onAirportSearch: function() {
      if (!this.airport.iata) {
        Materialize.toast('Please provide airport', 4000);
        return;
      }
      var _this = this;
      $.ajax({
        method: 'GET',
        url: 'api/explore_airports.php',
        data: { airport_iata: this.airport.iata },
        success: function(resp) {
          if (!resp || resp.status !== 'success') {
            Materialize.toast(resp.message, 4000);
            return;
          }
          _this.airportComments = resp.data.comments;
          _this.averageAirportRate = parseFloat(resp.data.average_rate);
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
    flightNumber: function(newVal) {
      this.flightComments = [];
      this.averageFlightRate = 0;
    },
    'depAirport.name': function(newVal) {
      this.flightRouteComments = [];
      this.averageFlightRouteRate = 0;
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
      this.flightRouteComments = [];
      this.averageFlightRouteRate = 0;
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
      this.airlineComments = [];
      this.averageAirlineRate = 0;
      if (!newVal) {
        this.airline.iata = '';
      } else if (newVal.includes(' / ')) {
        var iata = newVal.split(' / ')[0];
        var name = newVal.split(' / ')[1];
        this.airline.iata = iata;
        this.airline.name = name;
      }
    },
    'airport.name': function(newVal) {
      this.airportComments = [];
      this.averageAirportRate = 0;
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
