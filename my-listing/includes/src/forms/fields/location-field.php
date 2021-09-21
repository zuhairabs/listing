<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Location_Field extends Base_Field {

	public $modifiers = [
		'short' => 'Short Address',
		'lat' => '%s Latitude',
		'lng' => '%s Longitude',
	];

	public function get_posted_value() {
		return isset( $_POST[ $this->key ] )
			? sanitize_text_field( stripslashes( $_POST[ $this->key ] ) )
			: '';
	}

	public function validate() {
		$value = $this->get_posted_value();
		//
	}

	public function update() {
		$value = $this->get_posted_value();
		update_post_meta( $this->listing->get_id(), '_'.$this->key, $value );

		// save address coordinates and lockpin state
		if ( ! empty( $_POST['job_location'] ) && ! empty( $_POST['job_location__latitude'] ) && ! empty( $_POST['job_location__longitude'] ) ) {
			$lockpin = ! empty( $_POST['job_location__lock_pin'] ) && $_POST['job_location__lock_pin'] == 'yes';
			$latitude = (float) $_POST['job_location__latitude'];
			$longitude = (float) $_POST['job_location__longitude'];

			// validate lat/lng
			if ( $latitude && $longitude && ( $latitude <= 90 ) && ( $latitude >= -90 ) && ( $longitude <= 180 ) && ( $longitude >= -180 ) ) {
				update_post_meta( $this->listing->get_id(), 'geolocation_lat', $latitude );
				update_post_meta( $this->listing->get_id(), 'geolocation_long', $longitude );
			}

			update_post_meta( $this->listing->get_id(), 'job_location__lock_pin', $lockpin ? 'yes' : false );
		}
	}

	public function field_props() {
		$this->props['type'] = 'location';
		$this->props['map-skin'] = false;
		$this->props['map-zoom'] = 12;
		$this->props['map-default-location'] = [
			'lat' => 0,
			'lng' => 0,
		];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getPlaceholderField();
		$this->getDescriptionField();
		$this->getMapSkinField();
		$this->getMapDefaultLocationField();
		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
	}

	public function getMapSkinField() { ?>
		<div class="form-group">
			<label>Map Skin</label>
			<div class="select-wrapper">
				<select v-model="field['map-skin']">
					<?php foreach ( \MyListing\Apis\Maps\get_skins() as $skin => $label ): ?>
						<option value="<?php echo esc_attr( $skin ) ?>"><?php echo esc_html( $label ) ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
	<?php }

	public function getMapDefaultLocationField() { ?>
		<div class="form-group">
			<label>Default map location</label>
			<input type="number" min="-90" max="90" v-model="field['map-default-location']['lat']" step="any" style="width: 49%;" placeholder="Latitude">
			<input type="number" min="-180" max="180" v-model="field['map-default-location']['lng']" step="any" style="width: 49%; float: right;" placeholder="Longitude">
			<p class="mb0">When the field is empty, this will be used as the map center.</p>
		</div>

		<div class="form-group">
			<label>Default map zoom level</label>
			<input type="number" min="0" max="22" v-model="field['map-zoom']" style="width: 49%;">
			<p class="mb0">Enter a value between 0 (no zoom) and 22 (maximum zoom). Default: 12.</p>
		</div>
	<?php }

	public function string_value( $modifier = null ) {
		if ( $modifier === 'short' ) {
			$address = $this->get_value();
	        $parts = explode(',', $address);
	        return trim( $parts[0] );
		}

		if ( $modifier === 'lat' ) {
			return $this->listing->get_special_key(':lat');
		}

		if ( $modifier === 'lng' ) {
			return $this->listing->get_special_key(':lng');
		}

		return $this->get_value();
	}
}