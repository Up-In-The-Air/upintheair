var mapLoaded = false;
function initMap() {
  var map = new google.maps.Map(document.getElementById('route-map'), {
    center: {lat: 0, lng: 0},
    zoom: 1,
  });
}
$(document).ready(function() {
  $('ul.tabs').tabs();
  $('#comment').characterCounter();
  $('.datepicker').pickadate({
    selectMonths: true,
    selectYears: 15,
    format: 'yyyy-mm-dd'
  });
  $('.timepicker').pickatime({
    autoclose: false,
    twelvehour: false
  });
  // Google map
  $('.flight-info-trigger').click(function() {
    if (!mapLoaded) {
      $.getScript('https://maps.googleapis.com/maps/api/js?key=AIzaSyCdDkk14fyMgdX9DOODWwgwBdXYHDJoCt8&sensor=true&callback=initMap');
      mapLoaded = true;
    }
  });
  // flight data api
  $.ajax({
    method: 'GET',
    url: 'api/get_airlines.php',
    success: function(resp) {
      if (resp && resp.status === 'success') {
        var sourceObj = {};
        resp.data.forEach(function(airline) {
          sourceObj[airline.iata + ' / ' + airline.name] = null;
        });
        $('.airline-autocomplete').autocomplete({
          data: sourceObj,
          limit: 10
        });
      }
    }
  });
  $.ajax({
    method: 'GET',
    url: 'api/get_aircrafts.php',
    success: function(resp) {
      if (resp && resp.status === 'success') {
        var sourceObj = {};
        resp.data.forEach(function(aircraft) {
          sourceObj[aircraft.iata + ' / ' + aircraft.name] = null;
        });
        $('.aircraft-autocomplete').autocomplete({
          data: sourceObj,
          limit: 10
        });
      }
    }
  });
});
