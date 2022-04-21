window.mdGoogleMapAttributeInit = function () {
 $('.googleMapAttributeCanvas').each(function () {

  try {
    var latitude = $(this).data('latitude');
    var longitude = $(this).data('longitude');
    var zoom = $(this).data('zoom');
    var marker = $(this).data('marker');

    var latlng = new google.maps.LatLng(latitude, longitude);

    var mapOptions = {
     zoom: zoom,
     center: latlng,
     mapTypeId: google.maps.MapTypeId.ROADMAP,
     streetViewControl: false,
     scrollwheel: true,
     draggable: true,
     mapTypeControl: false
    };

    var map = new google.maps.Map(this, mapOptions);

    if(marker){
      new google.maps.Marker({
       position: latlng,
       map: map
      });
    }

  } catch (e) {
    $(this).replaceWith($('<p />').text(e.message));
  }
 });
};
