<?php

$instances = g::get('kmap.instances');
g::set('kmap.instances', $instances+1);

if(!isset($id))      $id      = 'map-' . uniqid();
if(!isset($width))   $width   = 300;
if(!isset($height))  $height  = 300;
if(!isset($type))    $type    = 'roadmap'; // roadmap, sattelite, hybrid, terrain 
if(!isset($class))   $class   = 'map';
if(!isset($zoom))    $zoom    = 15;
if(!isset($address)) $address = 'Mannheim, Germany';
if(!isset($addresses)) $addresses = [$address];

?>
<?php if(!$instances): ?>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">

var kmap = {
  
  init : function(options) {

    var img = document.getElementById(options.element);
    if(!img) return false;
    
    var elem = document.createElement('div');
    elem.setAttribute('id', options.element);
    elem.setAttribute('class', options.uclass);
    elem.style.width  = options.width + 'px';
    elem.style.height = options.height + 'px';

    img.parentNode.replaceChild(elem, img);
        
    var geocoder, map;

    if(!options.zoom) options.zoom = 12;
    if(!options.type) options.type = 'roadmap';

    options.type = options.type.toUpperCase();

    switch(options.type) {
      case 'ROADMAP': case 'SATELLITE': case 'HYBRID': case 'TERRAIN': break;
      default: options.type = 'ROADMAP';
    }
            
    map = new google.maps.Map(elem, {
      zoom : options.zoom,
      center : new google.maps.LatLng(49.46097, 8.49042),
      mapTypeId : google.maps.MapTypeId[ options.type ]
    });

    geocoder = new google.maps.Geocoder();
    for (var i = options.addresses.length - 1; i >= 0; i--) {
      geocoder.geocode({'address': options.addresses[i]}, function(results, status) {
        if(status != google.maps.GeocoderStatus.OK) return;
        map.setCenter(results[0].geometry.location);
        new google.maps.Marker({map: map, position: results[0].geometry.location});
      });
    };
  },

  load : function(options) {
    var onload  = window.onload;
    window.onload = function() {
      if(typeof onload == 'function') onload();
      kmap.init(options);
    };
  }
    
};
</script>
<?php endif; ?>
<script type="text/javascript">
kmap.load({
  addresses : <?php echo json_encode($addresses) ?>,
  zoom      : <?php echo $zoom ?>,
  element   : '<?php echo $id ?>',
  type      : '<?php echo $type ?>',
  width     : '<?php echo $width ?>',
  height    : '<?php echo $height ?>',
  uclass    : '<?php echo $class ?>'
});
</script>
<noscript id="<?php echo $id ?>" class="<?php echo $class ?>">
  <img src="http://maps.google.com/maps/api/staticmap?center=<?php echo urlencode($addresses[0]) ?>&zoom=<?php echo $zoom ?>&size=<?php echo $width ?>x<?php echo $height ?>&maptype=<?php echo str::lower($type) ?>&markers=color:red|color:red|<?php echo urlencode($addresses[0]) ?>&sensor=false" width="<?php echo $width ?>" height="<?php echo $height ?>" class="<?php echo $class ?>" alt="<?php echo html($addresses[0]) ?>" />
</noscript>