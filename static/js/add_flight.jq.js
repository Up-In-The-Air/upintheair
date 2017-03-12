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
});
