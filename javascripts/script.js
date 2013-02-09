
/*
Author:
*/


(function() {
  var $, $doc, colors, create_gmap_latlng_from_coordinate_pairs, create_gmap_path, create_gmap_polygon, district_boundaries, district_contains_point, districts, do_map, find_district_by_point, load_district_data, load_districts, log, make_region, map, region_contains_point;

  colors = {
    d1: "#ff0000",
    d2: "#00ff00",
    d3: "#0000ff",
    d4: "#cccccc"
  };

  $ = jQuery;

  $doc = $(document);

  districts = {};

  district_boundaries = {};

  map = null;

  log = function(data) {
    return console.log(data);
  };

  /*
  take a string ex. -97.127557979037221,33.156515808050976
  split it into it's lat/lng component
  return a google LatLng object
  */


  create_gmap_latlng_from_coordinate_pairs = function(pair) {
    var parts;
    parts = pair.split(",");
    if (parts.length === !2) {
      console.log('data is malformed', pair);
    }
    return new google.maps.LatLng(parts[1], parts[0]);
  };

  create_gmap_path = function(data) {
    var pair, _i, _len, _ref, _results;
    _ref = data.LinearRing.coordinates.split(" ");
    _results = [];
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      pair = _ref[_i];
      _results.push(create_gmap_latlng_from_coordinate_pairs(pair));
    }
    return _results;
  };

  /* 
  take the raw JSON coordinates and turn it into a google maps polygon
  */


  create_gmap_polygon = function(data) {};

  /*
  a district will have one outerBoundaryIs object
  a distrcit _may_ have an innerBoundaryIs array
  */


  make_region = function(data, district) {
    var inner, interior, interior_boundaries, paths, _i, _len;
    paths = [];
    paths.push(create_gmap_path(data.outerBoundaryIs));
    if (data.innerBoundaryIs) {
      interior_boundaries = (function() {
        var _i, _len, _ref, _results;
        _ref = data.innerBoundaryIs;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          inner = _ref[_i];
          _results.push(create_gmap_path(inner));
        }
        return _results;
      })();
    }
    if (interior_boundaries) {
      for (_i = 0, _len = interior_boundaries.length; _i < _len; _i++) {
        interior = interior_boundaries[_i];
        paths.push(interior);
      }
    }
    return new google.maps.Polygon({
      paths: paths,
      strokeColor: colors[district],
      strokeWeight: 1,
      fillColor: colors[district],
      fillOpacity: 0.2,
      map: map
    });
  };

  /*
  grab JSON from the server for a district
  */


  load_district_data = function(district) {
    return $.getJSON("/json/" + district + ".json", function(data, status) {
      /*
      		district data, among other things, will contain:
      		several polygons that encompass the boundaries
      */

      var district_data, regions;
      if (data.Placemark.MultiGeometry.Polygon) {
        regions = (function() {
          var _i, _len, _ref, _results;
          _ref = data.Placemark.MultiGeometry.Polygon;
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            district_data = _ref[_i];
            _results.push(make_region(district_data, district));
          }
          return _results;
        })();
      }
      return districts[district] = regions;
    });
  };

  load_districts = function() {
    var district, _i, _len, _ref, _results;
    _ref = ["d1", "d2", "d3", "d4"];
    _results = [];
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      district = _ref[_i];
      _results.push(load_district_data(district));
    }
    return _results;
  };

  $doc.ready(load_districts);

  /*
  regions have an outer perimeter
  regions also have exclusion zones
  */


  region_contains_point = function(region, point) {
    if (region.Contains(point)) {
      return 1;
    }
    return 0;
  };

  /* 
  districts have many regions
  */


  district_contains_point = function(district, region, point) {
    var foo;
    foo = (function() {
      var _i, _len, _ref, _results;
      _ref = districts[district];
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        region = _ref[_i];
        _results.push(region_contains_point(region, point));
      }
      return _results;
    })();
    if (parseInt(foo.join(""), 10) !== 0) {
      return district;
    }
    return null;
  };

  find_district_by_point = function(point) {
    var district, final_district, foo, region;
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
    return console.log(foo);
  };

  do_map = function() {
    var $button, $map, $query, geocoder, latlng, lookup_address, map_options;
    $query = $('#address');
    $button = $('#map-button');
    $map = $('#map-canvas');
    geocoder = new google.maps.Geocoder();
    latlng = new google.maps.LatLng(33.214851, -97.133045);
    map_options = {
      zoom: 10,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById('map-canvas'), map_options);
    lookup_address = function(event) {
      var data, geocode_success;
      event.preventDefault();
      data = {
        'address': $query.val() + "Denton TX"
      };
      geocode_success = function(results, status) {
        var marker, marker_data;
        if (status === google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
          marker_data = {
            map: map,
            position: results[0].geometry.location
          };
          marker = new google.maps.Marker(marker_data);
          return find_district_by_point(results[0].geometry.location);
        }
      };
      return geocoder.geocode(data, geocode_success);
    };
    return $button.click(lookup_address);
  };

  $doc.ready(do_map);

}).call(this);
