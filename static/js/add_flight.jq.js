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
