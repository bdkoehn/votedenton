
###
Author:

###
['fizz' unless i%3] + ['buzz' unless i%5] or i for i in [1..100]
###
color data is present in the JSON feeds but each district is the same, so we assign unique values
###
colors =
  "DISTRICT 1": "#ABD9E9"
  "DISTRICT 2": "#FDAE61"
  "DISTRICT 3": "#2C7BB6"
  "DISTRICT 4": "#D7191C"


###
jquery and cached selectors
###
$ = jQuery
$doc = $(document)
$query = $('#address')
$button = $('#map-button')
$map = $('#map-canvas')

###
reveal map when focused on the address form
###
$(document).ready ->
  $("#address").focus ->
    $("#collapse-district-map").collapse "show"


###
google map options
###
downtown = new google.maps.LatLng(33.214851,-97.133045)
region_zoom = 11
detail_zoom = region_zoom + 4
districts = {}
district_bounds = new google.maps.LatLngBounds()
district_bounds.extend downtown
map = null
marker = null


reset_map = ()->
  marker.setMap(null) if marker
  map.setZoom region_zoom
  map.panToBounds district_bounds
  console.log district_bounds


###
geocoder for doing address/latlng lookups
###
geocoder = new google.maps.Geocoder()

###
handle data from google geocoder api
do some basic checks to ensure data is a street
###
geocoder_success = (results, status)->
  address = results[0].formatted_address
  reset_map() if status is not google.maps.GeocoderStatus.OK
  if status is google.maps.GeocoderStatus.OK
    if results[0].types.indexOf('street_address') > -1
      address = results[0].formatted_address
      mark_point results[0].geometry.location, detail_zoom, address
    else
      mark_point results[0].geometry.location, detail_zoom



###
mimic ruby's reject method
###
reject = (array, predicate) ->
  res = []
  res.push(value) for value in array when not predicate value
  res


###
take a string ex. -97.127557979037221,33.156515808050976
split it into it's lat/lng component
return a google LatLng object
###
create_gmap_latlng_from_coordinate_pairs = (pair)->
  parts = pair.split(",")
  console.log 'data is malformed', pair if parts.length is not 2
  point = new google.maps.LatLng( parts[1], parts[0])
  district_bounds.extend point
  point


###

###
create_gmap_path = (data)->
  ( create_gmap_latlng_from_coordinate_pairs pair for pair in data.LinearRing.coordinates.split(" ") )


###
a district will have one outerBoundaryIs object
a distrcit _may_ have an innerBoundaryIs array
###
make_region = (data, district)->
  paths = []

  paths.push create_gmap_path(data.outerBoundaryIs)
  interior_boundaries = (create_gmap_path inner for inner in data.innerBoundaryIs) if data.innerBoundaryIs
  paths.push interior for interior in interior_boundaries if interior_boundaries

  polygon = new google.maps.Polygon
    paths: paths
    strokeColor: colors[district]
    strokeWeight: 1
    fillColor: colors[district]
    fillOpacity: 0.4
    map: map
  google.maps.event.addListener polygon, 'click', (event)->
    data =
      latLng: event.latLng
    geocoder.geocode data, geocoder_success
  return polygon


###
grab JSON from the server for a district
###
load_district_data = (district)->
  $.getJSON "/content/themes/vote-denton/js/" + district + ".json", (data, status)->
    ###
    district data, among other things, will contain:
    several polygons that encompass the boundaries
    ###
    district_name = data.Placemark.ExtendedData.SchemaData.SimpleData[2]['#text']
    regions = (make_region district_data, district_name for district_data in data.Placemark.MultiGeometry.Polygon ) if data.Placemark.MultiGeometry.Polygon
    districts[district_name] = regions


###

###
load_districts = ()->
  load_district_data district for district in [ "d1", "d2", "d3", "d4" ] #

$doc.ready load_districts


###
districts have many regions
###
district_contains_point = (district, region, point)->
  foo = ( region.Contains point for region in districts[district] )
  results = reject foo, (value)-> value == false
  return district if results.length > 0
  false


find_district_by_point = (point)->
  final_district = []
  foo = ( district_contains_point district, region, point for district, region of districts )
  results = reject foo, (value)-> value == false
  return false if results.length is not 1
  results[0]

###
given a location on the map, via address search or click
show that location on the map
###


mark_point = (point, zoom = detail_zoom, address = "Location" )->
  district = find_district_by_point point

  map.setCenter point
  map.setZoom detail_zoom

  marker_data =
    map: map,
    position: point
  marker.setMap(null) if marker
  marker = new google.maps.Marker marker_data


  tmplString = "<p class='map-popover'><span class='address'>{{=it.address}}</span> is in Denton City <span class='district'>{{=it.district}}</span></p>"
  tmpl = doT.template(tmplString)


  $query.val( "Denton, TX" ) if address is "Location"
  $query.val( address ) unless address is "Location"


  infoWindow = new google.maps.InfoWindow
    content: tmpl
      district: district
      address: address

  infoWindow.open(map, marker)
  google.maps.event.addListener infoWindow,'closeclick', reset_map


do_map = ()->

  map_options =
    zoom: region_zoom
    center: downtown
    mapTypeId: google.maps.MapTypeId.ROADMAP
    zoomControl: false
    scaleControl: false
    scrollwheel: false
    disableDoubleClickZoom: true
    streetViewControl: false

  map = new google.maps.Map document.getElementById('map-canvas'), map_options

  google.maps.event.addListener map, 'click', ()->
    reset_map()

    $('#your_district').show().text( "Location indicated doesn't appear to be part of a Denton city district. Please type in your address, or click on the map to find your district." )


  lookup_address = (event)->
    event.preventDefault()

    data =
      'address':  $query.val() + " Denton TX"

    geocoder.geocode data, geocoder_success
  $button.click lookup_address

$doc.ready do_map
