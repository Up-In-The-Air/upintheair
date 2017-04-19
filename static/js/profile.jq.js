function initMap() {
  geocoder = new google.maps.Geocoder();
  map = new google.maps.Map(document.getElementById('route-map'), {
    center: {lat: 0, lng: 0},
    zoom: 1,
  });
}
$(document).ready(function() {
  $('.button-collapse').sideNav();
});
