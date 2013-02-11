
###
Author:

###


###
color data is present in the JSON feeds but each district is the same, so we assign unique values
###
colors = 
	"DISTRICT 1": "#ff0000"
	"DISTRICT 2": "#00ff00"
	"DISTRICT 3": "#0000ff"
	"DISTRICT 4": "#FF7D40"

$ = jQuery

$doc = $(document)

districts = {}

map = null

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
	new google.maps.LatLng( parts[1], parts[0])



###

###
create_gmap_path = (data)->
	( create_gmap_latlng_from_coordinate_pairs pair for pair in data.LinearRing.coordinates.split(" ") )

# ### 
# take the raw JSON coordinates and turn it into a google maps polygon
# ###
# create_gmap_polygon = (data)->
# 	# coordinates = ( create_gmap_latlng_from_coordinate_pairs pair for pair in data.LinearRing.coordinates.split(" ") )
	


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
		fillOpacity: 0.2
		map: map
	google.maps.event.addListener polygon, 'click', ()->
		report_district district

	polygon


###
grab JSON from the server for a district
###
load_district_data = (district)->
	$.getJSON "/json/" + district + ".json", (data, status)->
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
regions have an outer perimeter
regions also have exclusion zones
###
region_contains_point = (region, point)->
	return true if region.Contains point
	false


### 
districts have many regions
###
district_contains_point = (district, region, point)->
	foo = ( region_contains_point region, point for region in districts[district] )
	results = reject foo, (value)-> value == false
	return district if results.length > 0
	false


find_district_by_point = (point)->
	final_district = []
	foo = ( district_contains_point district, region, point for district, region of districts )
	results = reject foo, (value)-> value == false
	console.log results 
	report_district results[0] if results.length is 1


report_district = (district)->
	$('#your_district').text( "You reside in " + district + "!")
	console.log district


do_map = ()->
	$query = $('#address')
	$button = $('#map-button')
	$map = $('#map-canvas')

	geocoder = new google.maps.Geocoder()
	latlng = new google.maps.LatLng(33.214851,-97.133045)
	map_options = 
		zoom: 11
		center: latlng
		mapTypeId: google.maps.MapTypeId.ROADMAP

	map = new google.maps.Map document.getElementById('map-canvas'), map_options

	google.maps.event.addListener map, 'click', ()->
		$('#your_district').text( "Location indicated doesn't appear to be part of a Denton county district. Please type in your address, or click on the map to find your district." )


	lookup_address = (event)->
		event.preventDefault()

		data = 
			'address':  $query.val() + " Denton TX"

		geocode_success = (results, status)->
			if status is google.maps.GeocoderStatus.OK
				# log results[0].geometry.location.lat
				map.setCenter results[0].geometry.location

				marker_data = 
					map: map,
					position: results[0].geometry.location
				marker = new google.maps.Marker marker_data

				find_district_by_point results[0].geometry.location


		geocoder.geocode data, geocode_success
	$button.click lookup_address

$doc.ready do_map
