<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\LocationMaster */
/* @var $form yii\widgets\ActiveForm */
?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>

<div class="location-master-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="form-group field-locationmaster-name required has-error">
        <input id="pac-input" class="controls" type="text" placeholder="Search Box">
        <div id="map-canvas"></div>
        <input  id="locationmaster-lat" name="LocationMaster[lat]" type="hidden" required>
        <input id="locationmaster-lng" name="LocationMaster[lng]" type="hidden" required>
        <input id="locationmaster-customer_id" name="LocationMaster[customer_id]" value="4" type="hidden" required>
    </div>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php
//        $bikerlist_query = Yii::$app->db->createCommand(" SELECT  `name`,`id`,`status`,`last_lat`, `last_long`, SQRT(
//                                                        POW(69.1 * (`last_lat` - " . $model->pickAddress->lat . "), 2) +
//                                                        POW(69.1 * (" . $model->pickAddress->lng . " - `last_long`) * COS(`last_lat` / 57.3), 2)) AS distance
//                                                    FROM delivery_boy_master  WHERE `status`=0 ORDER BY distance ");
    $interest = frontend\models\InterestMaster::find()->where(['status' => 1])->all();
    $interestlistData = [];
    if ($interest != null) {
        $interestlistData = yii\helpers\ArrayHelper::map($interest, 'id', 'interest');
    }
    ?>

    <?= $form->field($model, 'interest_id')->dropDownList($interestlistData, ['prompt' => 'Select Interest']) ?>

    <?php
    $status = ['0' => 'Inactive', '1' => 'Active', '2' => 'Other'];
    ?>

    <?= $form->field($model, 'status')->dropDownList($status, ['prompt' => 'Select Status']) ?>

    <?= $form->field($upload_model, 'imageFiles')->fileInput(['accept' => 'image/*']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>

    function initialize() {


        var markers = [];
        var map = new google.maps.Map(document.getElementById('map-canvas'), {
            zoom: 22,
            mapTypeId: google.maps.MapTypeId.TERRAIN
        });
        marker = new google.maps.Marker({
            draggable: true,
            map: map,
            title: "Your location",
        });
        google.maps.event.addDomListener(marker, 'dragend', function (e) {
            $('#locationmaster-lat').val(this.getPosition().lat());
            $('#locationmaster-lng').val(this.getPosition().lng());
        });
        // Try HTML5 geolocation.
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                $('#locationmaster-lat').val(position.coords.latitude);
                $('#locationmaster-lng').val(position.coords.longitude);
                marker.setPosition(pos);
                map.setZoom(17);
                map.panTo(pos);
            }, function () {
//                handleLocationError(true, infoWindow, map.getCenter());
            });
        } else {
            // Browser doesn't support Geolocation
//            handleLocationError(false, infoWindow, map.getCenter());
        }
//        google.maps.event.addListener(map, 'click', function (event) {
//            //                    console.log("Latitude: " + event.latLng.lat() + " " + ", longitude: " + event.latLng.lng());
//            getPostalCodeByLatLong(event.latLng.lat(), event.latLng.lng());
//        });
        var defaultBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(-33.8902, 151.1759),
                new google.maps.LatLng(-33.8474, 151.2631));
        map.fitBounds(defaultBounds);
        // Create the search box and link it to the UI element.
        var input = /** @type {HTMLInputElement} */(
                document.getElementById('pac-input'));
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        var searchBox = new google.maps.places.SearchBox(
                /** @type {HTMLInputElement} */(input));
        // Listen for the event fired when the user selects an item from the
        // pick list. Retrieve the matching places for that item.
        google.maps.event.addListener(searchBox, 'places_changed', function () {
            var places = searchBox.getPlaces();
            if (places.length == 0) {
                return;
            }
            // For each place, get the icon, place name, and location.
            markers = [];
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0, place; place = places[i]; i++) {
                var image = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };
                // Create a marker for each place.
                marker.setPosition(place.geometry.location);
                map.setZoom(17);
                map.panTo(place.geometry.location);
                $('#locationmaster-lat').val(place.geometry.location.latitude);
                $('#locationmaster-lng').val(place.geometry.location.longitude);
//                var marker = new google.maps.Marker({
//                    map: map,
//                    icon: image,
//                    title: place.name,
//                    position: place.geometry.location
//                });
//                markers.push(marker);
                bounds.extend(place.geometry.location);
            }

            map.fitBounds(bounds);
        });
        // Bias the SearchBox results towards places that are within the bounds of the
        // current map's viewport.
        google.maps.event.addListener(map, 'bounds_changed', function () {
            var bounds = map.getBounds();
            searchBox.setBounds(bounds);
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);

    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
                'Error: The Geolocation service failed.' :
                'Error: Your browser doesn\'t support geolocation.');
    }

</script>