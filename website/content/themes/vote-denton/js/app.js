/*
Author:
*/

var $, $button, $doc, $map, $query, colors, create_gmap_latlng_from_coordinate_pairs, create_gmap_path, detail_zoom, district_bounds, district_contains_point, districts, do_map, downtown, find_district_by_point, geocoder, geocoder_success, i, load_district_data, load_districts, make_region, map, mark_point, marker, region_zoom, reject, reset_map, _i;

for (i = _i = 1; _i <= 100; i = ++_i) {
  [!(i % 3) ? 'fizz' : void 0] + [!(i % 5) ? 'buzz' : void 0] || i;
}

/*
color data is present in the JSON feeds but each district is the same, so we assign unique values
*/


colors = {
  "DISTRICT 1": "#ABD9E9",
  "DISTRICT 2": "#FDAE61",
  "DISTRICT 3": "#2C7BB6",
  "DISTRICT 4": "#D7191C"
};

/*
jquery and cached selectors
*/


$ = jQuery;

$doc = $(document);

$query = $('#address');

$button = $('#map-button');

$map = $('#map-canvas');

/*
google map options
*/


downtown = new google.maps.LatLng(33.214851, -97.133045);

region_zoom = 11;

detail_zoom = region_zoom + 4;

district_bounds = new google.maps.LatLngBounds();

district_bounds.extend(downtown);

map = null;

marker = null;

reset_map = function() {
  if (marker) {
    marker.setMap(null);
  }
  map.panToBounds(district_bounds);
  return map.setZoom(region_zoom);
};

/*
geocoder for doing address/latlng lookups
*/


geocoder = new google.maps.Geocoder();

/*
handle data from google geocoder api
do some basic checks to ensure data is a street
*/


geocoder_success = function(results, status) {
  var address;
  address = results[0].formatted_address;
  if (status === !google.maps.GeocoderStatus.OK) {
    reset_map();
  }
  if (status === google.maps.GeocoderStatus.OK) {
    if (results[0].types.indexOf('street_address') > -1) {
      address = results[0].formatted_address;
      return mark_point(results[0].geometry.location, detail_zoom, address);
    } else {
      return mark_point(results[0].geometry.location, detail_zoom);
    }
  }
};

districts = {};

/*
the map object
*/


map = null;

/*
marker, we only have one
*/


marker = null;

/*
mimic ruby's reject method
*/


reject = function(array, predicate) {
  var res, value, _j, _len;
  res = [];
  for (_j = 0, _len = array.length; _j < _len; _j++) {
    value = array[_j];
    if (!predicate(value)) {
      res.push(value);
    }
  }
  return res;
};

/*
take a string ex. -97.127557979037221,33.156515808050976
split it into it's lat/lng component
return a google LatLng object
*/


create_gmap_latlng_from_coordinate_pairs = function(pair) {
  var parts, point;
  parts = pair.split(",");
  if (parts.length === !2) {
    console.log('data is malformed', pair);
  }
  point = new google.maps.LatLng(parts[1], parts[0]);
  district_bounds.extend(point);
  return point;
};

/*
*/


create_gmap_path = function(data) {
  var pair, _j, _len, _ref, _results;
  _ref = data.LinearRing.coordinates.split(" ");
  _results = [];
  for (_j = 0, _len = _ref.length; _j < _len; _j++) {
    pair = _ref[_j];
    _results.push(create_gmap_latlng_from_coordinate_pairs(pair));
  }
  return _results;
};

/*
a district will have one outerBoundaryIs object
a distrcit _may_ have an innerBoundaryIs array
*/


make_region = function(data, district) {
  var inner, interior, interior_boundaries, paths, polygon, _j, _len;
  paths = [];
  paths.push(create_gmap_path(data.outerBoundaryIs));
  if (data.innerBoundaryIs) {
    interior_boundaries = (function() {
      var _j, _len, _ref, _results;
      _ref = data.innerBoundaryIs;
      _results = [];
      for (_j = 0, _len = _ref.length; _j < _len; _j++) {
        inner = _ref[_j];
        _results.push(create_gmap_path(inner));
      }
      return _results;
    })();
  }
  if (interior_boundaries) {
    for (_j = 0, _len = interior_boundaries.length; _j < _len; _j++) {
      interior = interior_boundaries[_j];
      paths.push(interior);
    }
  }
  polygon = new google.maps.Polygon({
    paths: paths,
    strokeColor: colors[district],
    strokeWeight: 1,
    fillColor: colors[district],
    fillOpacity: 0.4,
    map: map
  });
  google.maps.event.addListener(polygon, 'click', function(event) {
    data = {
      latLng: event.latLng
    };
    return geocoder.geocode(data, geocoder_success);
  });
  return polygon;
};

/*
grab JSON from the server for a district
*/


load_district_data = function(district) {
  return $.getJSON("/content/themes/vote-denton/js/" + district + ".json", function(data, status) {
    /*
        district data, among other things, will contain:
        several polygons that encompass the boundaries
    */

    var district_data, district_name, regions;
    district_name = data.Placemark.ExtendedData.SchemaData.SimpleData[2]['#text'];
    if (data.Placemark.MultiGeometry.Polygon) {
      regions = (function() {
        var _j, _len, _ref, _results;
        _ref = data.Placemark.MultiGeometry.Polygon;
        _results = [];
        for (_j = 0, _len = _ref.length; _j < _len; _j++) {
          district_data = _ref[_j];
          _results.push(make_region(district_data, district_name));
        }
        return _results;
      })();
    }
    return districts[district_name] = regions;
  });
};

/*
*/


load_districts = function() {
  var district, _j, _len, _ref, _results;
  _ref = ["d1", "d2", "d3", "d4"];
  _results = [];
  for (_j = 0, _len = _ref.length; _j < _len; _j++) {
    district = _ref[_j];
    _results.push(load_district_data(district));
  }
  return _results;
};

$doc.ready(load_districts);

/*
districts have many regions
*/


district_contains_point = function(district, region, point) {
  var foo, results;
  foo = (function() {
    var _j, _len, _ref, _results;
    _ref = districts[district];
    _results = [];
    for (_j = 0, _len = _ref.length; _j < _len; _j++) {
      region = _ref[_j];
      _results.push(region.Contains(point));
    }
    return _results;
  })();
  results = reject(foo, function(value) {
    return value === false;
  });
  if (results.length > 0) {
    return district;
  }
  return false;
};

find_district_by_point = function(point) {
  var district, final_district, foo, region, results;
  final_district = [];
  foo = (function() {
    var _results;
    _results = [];
    for (district in districts) {
      region = districts[district];
      _results.push(district_contains_point(district, region, point));
    }
    return _results;
  })();
  results = reject(foo, function(value) {
    return value === false;
  });
  if (results.length === !1) {
    return false;
  }
  return results[0];
};

/*
given a location on the map, via address search or click
show that location on the map
*/


mark_point = function(point, zoom, address) {
  var district, infoWindow, marker_data, tmpl, tmplString;
  if (zoom == null) {
    zoom = detail_zoom;
  }
  if (address == null) {
    address = "Location";
  }
  district = find_district_by_point(point);
  map.setCenter(point);
  map.setZoom(detail_zoom);
  marker_data = {
    map: map,
    position: point
  };
  if (marker) {
    marker.setMap(null);
  }
  marker = new google.maps.Marker(marker_data);
  tmplString = "<p class='map-popover'><span class='address'>{{=it.address}}</span> is in Denton City <span class='district'>{{=it.district}}</span></p>";
  tmpl = doT.template(tmplString);
  if (address === "Location") {
    $query.val("Denton, TX");
  }
  if (address !== "Location") {
    $query.val(address);
  }
  infoWindow = new google.maps.InfoWindow({
    content: tmpl({
      district: district,
      address: address
    })
  });
  return infoWindow.open(map, marker);
};

do_map = function() {
  var lookup_address, map_options;
  map_options = {
    zoom: region_zoom,
    center: downtown,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map-canvas'), map_options);
  google.maps.event.addListener(map, 'click', function() {
    reset_map();
    return $('#your_district').show().text("Location indicated doesn't appear to be part of a Denton city district. Please type in your address, or click on the map to find your district.");
  });
  lookup_address = function(event) {
    var data;
    event.preventDefault();
    data = {
      'address': $query.val() + " Denton TX"
    };
    return geocoder.geocode(data, geocoder_success);
  };
  return $button.click(lookup_address);
};

$doc.ready(do_map);
