if(JFuncs==undefined){
	var JFuncs = {};
}

/*
 * Gmaps API
 * Example:
 
	jQuery(function(){
		JFunc.JGmaps.start({
			olat:"{g_lat}",
			olng:"{g_lng}"
		});
	});
	
	<div>
		Lat: <input  name="g_lat" type="text" class="input" id="g_lat" value="{g_lat}" size="20" />
		Lng: <input name="g_lng" type="text" class="input" id="g_lng" value="{g_lng}" size="20" />
		<input type="button" id="btnGeocoding" value="GeoCoding">
		<input type="button" id="btnUpdate" value="Update">
	</div>
	<div id="geocoding_result"></div>
	<div>
		<div id="map_canvas" style="backgrond:blue;width:500px;height:400px;"></div>
	</div>
 
 * include <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&language=en-GB"></script>
 */
JFuncs.JGmaps = function(){
	this.geocoder=null;
	this.marker=null;
	this.map=null;
	this._options = {
        olat:null,
        olng:null,
		address_oname:"g_address",
		postcode_oname:"g_postcode",
		country_oname:"country",
		state_oname:"state",
		city_oname:"city",
		btnGeocoding_oname:"btnGeocoding",
		btnUpdate_oname:"btnUpdate",
		map_container:"map_canvas",
		geocoding_result:"geocoding_result",
		lat_oname:"g_lat",
		lng_oname:"g_lng",
		zoom:13,
		language:"en-GB",
        staticmap:0
	}

	this.init();
}
JFuncs.JGmaps.prototype = {
	init:function(){
		var $self = this;
		jQuery(function(){
			jQuery('#'+$self._options.btnGeocoding_oname).click(function(){
				var $address = jQuery('#'+$self._options.address_oname).val()+', '+jQuery('#'+$self._options.postcode_oname).val()+
				' '+jQuery('#'+$self._options.city_oname+'_container select.selected option:selected').attr('name')+
				', '+jQuery('#'+$self._options.state_oname+'_container select.selected option:selected').attr('name')+
				', '+jQuery('#'+$self._options.country_oname+'_container select.selected option:selected').attr('name');
				$self.codeAddress($address);
			});
			jQuery('#'+$self._options.btnUpdate_oname).click(function(){
				$self.codeUpdate();
			});
		});
	},
    staticmap:function(){
        var $self,$temp,$map_container;
        $self = this;
        $map_container = jQuery('#'+$self._options.map_container);
        $map_container.css('position','relative');
        $map_container.hide();
        $temp = jQuery('<img alt="Click here for a more detailed interactive map" src="http://maps.google.com/maps/api/staticmap?center='+$self._options.olat+','+$self._options.olng+'&amp;zoom='+$self._options.zoom+'&amp;size='+$map_container.width()+'x'+$map_container.height()+'&amp;maptype=roadmap&amp;markers=color:red%7C'+$self._options.olat+','+$self._options.olng+'&amp;sensor=false">');
        $temp.load(function(){
            $map_container.show();
        });
        $map_container.append($temp);        
        $temp = jQuery('<div style="position:absolute;top:0;left:0;width:100%;height:100%;"><table width="100%" height="100%"><tr><td align="center" valign="center" style="font-weight:bold;font-size:18px;padding-top:100px;">Click here for a more detailed interactive map</td></tr></table></div>');
        $temp.css({'cursor':'pointer','z-index':'999','background':'transparent'});
        $temp.click(function(){
           $map_container.html('');
           $self.initialize();
        });
        $map_container.append($temp);
    },
	start:function($o){
		var $self = this;
		$self._options = jQuery.extend($self._options, $o);
        if($self._options.staticmap){
            $self.staticmap();
        }else{
            $self.initialize();
        }        
	},
	initialize:function() {
        var $self,$lat,$lng;
		$self = this;
		$self.geocoder = new google.maps.Geocoder();
        if($self._options.olat!=undefined){
            $lat = $self._options.olat;
            $lng = $self._options.olng;
        }else{
            $lat = jQuery('#'+$self._options.lat_oname).val();
            $lng = jQuery('#'+$self._options.lng_oname).val();
        }
		var latlng = new google.maps.LatLng($lat, $lng);
		var myOptions = {
			zoom: $self._options.zoom,
			scrollwheel: false,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		$self.map = new google.maps.Map(document.getElementById($self._options.map_container), myOptions);
		$self.set_marker(latlng);
		$self.update_latlng();
		$self.codeUpdate();
	},
	codeAddress:function($address) {
		var $self = this;
		if($address==undefined){
			return;
		}
		$self.geocoder.geocode( {
			'address': $address
		}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				jQuery('#'+$self._options.geocoding_result).html(results[0].formatted_address);
				$self.map.setCenter(results[0].geometry.location);
				$self.set_marker(results[0].geometry.location);
				$self.update_latlng();
			} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
		});
	},
	codeUpdate:function() {
		var $self = this;
		var lat = jQuery('#'+$self._options.lat_oname).val();
		var lng = jQuery('#'+$self._options.lng_oname).val();
		if(lat==undefined || lng==undefined){
			return;
		}
		var latlng = new google.maps.LatLng(lat, lng);
		$self.map.setCenter(latlng);
		$self.set_marker(latlng);
	},
	set_marker:function(latlng){
		var $self = this;
		if($self.marker!=undefined){
			$self.marker.setMap(null);
		}
		$self.marker = new google.maps.Marker({
			map: $self.map,
			draggable:true,
			position: latlng
		});
		google.maps.event.addListener($self.marker, 'dragend', function() {
			$self.update_latlng();
		});
	},
	update_latlng:function(){
		var $self = this;
		jQuery('#'+$self._options.lat_oname).val($self.marker.getPosition().lat());
		jQuery('#'+$self._options.lng_oname).val($self.marker.getPosition().lng());
	}
}
jQuery(function(){
	JFunc.run('JGmaps');
});

/*
 * Initialize
 *
 */
if(JFunc==undefined){
	var JFunc = {
		runner:[],
		run: function($name){
			if(jQuery.isFunction(JFuncs[$name])){
				if(this[$name]==undefined){
					return this[$name] = new JFuncs[$name];
				}else{
					return this[$name];
				}

			}
			return false;
		}
	}
}