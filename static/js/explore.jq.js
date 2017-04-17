$(document).ready(function() {
  $('ul.tabs').tabs();
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
  // rating
  $('.rating-div').starRating({
    starSize: 25,
    starShape: 'rounded',
    readOnly: true
  });
});
