<?php
/**
 * Template for the Test Data Generation page in Settings.
 *
 * @var string $nonce_action_key The nonce action key.
 *
 * @version 1.0.0
 */

?>
<h1><?php echo get_admin_page_title() ?></h1>
<?php do_action( 'tribe_ext_test_data_generator_notices' ) ?>
<form method="post" action="" novalidate="novalidate">
	<?php wp_nonce_field( $nonce_action_key ); ?>
	<table class="form-table" role="presentation">
		<tbody>

		<tr>
			<th scope="row">
				<label for="num_venues">Generate Venues</label>
			</th>
			<td>
                <input type="number" id='num_venues' name='tribe-ext-test-data-generator[venues][quantity]'
                        placeholder="None" style="width: 90px">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="num_organizers">Generate Organizers</label>
			</th>
			<td>
                <input type="number" id='num_organizers' name='tribe-ext-test-data-generator[organizers][quantity]'
                       placeholder="None" style="width: 90px">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="num_events">Generate Events</label>
			</th>
			<td>
                <input type="number" id='num_events' name='tribe-ext-test-data-generator[events][quantity]'
                       placeholder="None" style="width: 90px">
			</td>
		</tr>
        <tr>
            <td colspan="2">
                <p style="color: royalblue">
                    <span style="padding-right: 10px; padding-left: 5px">ℹ</span>
                    <em><strong>Server Time-out warning:</strong> the amount of Venues, Organizers and Events you can create are limited by this server's capabilities (available memory, processing power, etc.).<br/>Some Venues, Organizers and Events are going to be generated even if the request times out. If your request times out, just navigate "Back". Don't reload the page, otherwise the request will be re-sent.</em>
                </p>
            </td>
        </tr>
		<tr class="tribe-dependent" data-depends="#num_events" data-condition-not="0" style="background-color: whitesmoke">
			<td colspan="2">
				<label for="event_from_date">Create events between </label>
                <input list="date_ranges" id='event_from_date' name='tribe-ext-test-data-generator[events][from_date]'
                placeholder="now, -1 week OR YYYY-MM-DD" value="now" class="date-range" onClick="this.select();">
				<label for="event_to_date"> and </label>
                <input list="date_ranges" id='event_to_date' name='tribe-ext-test-data-generator[events][to_date]'
                       placeholder="now, +1 week OR YYYY-MM-DD" value="tomorrow" class="date-range" onClick="this.select();">
                <br/>
                <p style="color: royalblue"><em>For the date range, you can use date strings like <strong>"now", "tomorrow",
                            "-2 weeks", "+3 months"</strong> or a specific date using <strong>YYYY-MM-DD</strong> format.
                        <br/>Start typing in fields for suggestions of commonly used ranges.</em></p>
            </td>
		</tr>
        <tr class="tribe-dependent" data-depends="#num_events" data-condition-not="0" style="background-color: whitesmoke">
            <td colspan="2">
                <input type="checkbox" id='mark_featured' name='tribe-ext-test-data-generator[events][featured]'/>
                <label for="mark_featured">Make them Featured events.</label>
            </td>
        </tr>
        <tr class="tribe-dependent" data-depends="#num_events" data-condition-not="0" style="background-color: whitesmoke">
            <td>
                <input type="checkbox" id='add_custom_category' name='tribe-ext-test-data-generator[events][add_custom_category]'/>
                <label for="add_custom_category">Custom Event Category.</label>
            </td>
            <td class="tribe-dependent" data-depends="#add_custom_category" data-condition-is-checked="true" style="background-color: whitesmoke">
                <label for="custom_category">Event Category: </label>
                <input type="text" id='custom_category' name='tribe-ext-test-data-generator[events][custom_category]'
                       placeholder="Category name" style="width: 130px">
        </tr>
        <tr class="tribe-dependent" data-depends="#num_events" data-condition-not="0" style="background-color: whitesmoke">
            <td>
                <input type="checkbox" id='add_custom_tag' name='tribe-ext-test-data-generator[events][add_custom_tag]'/>
                <label for="add_custom_tag">Custom Tag.</label>
            </td>
            <td class="tribe-dependent" data-depends="#add_custom_tag" data-condition-is-checked="true" style="background-color: whitesmoke">
                <label for="custom_tag">Tag: </label>
                <input type="text" id='custom_tag' name='tribe-ext-test-data-generator[events][custom_tag]'
                       placeholder="Tag name" style="width: 130px">
        </tr>
        <?php if( class_exists( 'Tribe\Events\Virtual\Plugin' ) ) : ?>
            <tr class="tribe-dependent" data-depends="#num_events" data-condition-not="0" style="background-color: whitesmoke">
                <td colspan="2">
                    <input type="checkbox" id='mark_virtual' name='tribe-ext-test-data-generator[events][virtual]'/>
                    <label for="mark_virtual">Make them Virtual events.</label>
                </td>
            </tr>
        <?php endif; ?>
        <?php if( class_exists( 'Tribe__Events__Pro__Main' ) ) : ?>
            <tr class="tribe-dependent" data-depends="#num_events" data-condition-not="0" style="background-color: whitesmoke">
                <td>
                    <input type="checkbox" id='is_recurring' name='tribe-ext-test-data-generator[events][recurring]'/>
                    <label for="is_recurring">Make them Recurring.</label>
                </td>
                <td class="tribe-dependent" data-depends="#is_recurring" data-condition-is-checked="true" style="background-color: whitesmoke">
                    <label for="recurring_type">Recurrence: </label>
                    <select id='recurring_type' name='tribe-ext-test-data-generator[events][recurring_type]'>
                        <option value='all'>All</option>
                        <option value='daily'>Daily</option>
                        <option value='weekly'>Weekly</option>
                        <option value='monthly'>Monthly</option>
                        <option value='yearly'>Yearly</option>
                </td>
            </tr>
        <?php endif; ?>
		<?php if( class_exists( 'Tribe__Tickets__Main' ) ) : ?>
		<tr class="tribe-dependent" data-depends="#num_events" data-condition-not="0" style="background-color: whitesmoke">
			<td colspan="2">
				<input type="checkbox" id='add_RSVP' name='tribe-ext-test-data-generator[events][rsvp]'/>
				<label for="add_RSVP">Add RSVP to generated events.</label>
			</td>
		</tr>
		<?php endif; ?>
		<?php if( class_exists( 'Tribe__Tickets__Tickets' ) ) : ?>
			<?php
			$providers = Tribe__Tickets__Tickets::modules();
			unset( $providers[ 'Tribe__Tickets__RSVP' ] );
			if ( 0 < count( $providers ) ) : ?>
				<tr class="tribe-dependent" data-depends="#num_events" data-condition-not="0" style="background-color: whitesmoke">
					<td colspan="2">
						<input type="checkbox" id='add_ticket' name='tribe-ext-test-data-generator[events][ticket]'/>
						<label for="add_ticket">Add Ticket to generated events.</label>
					</td>
				</tr>
			<?php else: ?>
				<tr class="tribe-dependent" data-depends="#num_events" data-condition-not="0" style="background-color: whitesmoke">
					<td colspan="2">
						<p style="color: royalblue">
							<span style="padding-right: 10px; padding-left: 5px">ℹ</span>
							<em>️Setup <strong>TribeCommerce</strong> or <strong>WooCommerce + Event Tickets Plus</strong> to add Tickets to Events.</em>
						</p>
					</td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>
		</tbody>
	</table>
	<?php submit_button( 'Generate Data' ); ?>
</form>

<div style="background-color: whitesmoke; padding-left: 30px; padding-top: 10px; margin-right: 80px">
	<h2>Handy Tools</h2>
	<form method="post" action="" novalidate="novalidate">
		<?php wp_nonce_field( $nonce_action_key ); ?>
		<table class="form-table" role="presentation">
			<tbody>
			<tr>
				<th scope="row">
					<label>Upload </label>
					<select id='numImages' name='tribe-ext-test-data-generator[uploads][quantity]'>
					<option value='0'>None</option>
					<option value='10'>Few</option>
					<option value='100'>Bunch</option>
					</select>
					<label for="numImages">Random Images</label>
				</th>
				<td style="vertical-align: bottom">
					<?php submit_button( 'Upload Images' ); ?>
				</td>
			</tr>
            <tr class="tribe-dependent" data-depends="#numImages" data-condition="100" style="background-color: whitesmoke">
                <td colspan="2">
                    <p class="tribe-dependent" data-depends="#numImages" data-condition-not="0" style="color: royalblue; background-color: whitesmoke">
                        <span style="padding-right: 10px; padding-left: 5px">ℹ</span>
                        <em><strong>Server Time-out warning:</strong> the amount of images that can be uploaded are limited by this server's capabilities (available memory, processing power, etc.).<br/>Some images are going to be uploaded even if the request times out. If your request times out, just navigate "Back". Don't reload the page, otherwise the request will be re-sent.</em>
                    </p>
                </td>
            </tr>
			</tbody>
		</table>
	</form>

	<form method="post" action="" novalidate="novalidate">
		<?php wp_nonce_field( $nonce_action_key ); ?>
		<table class="form-table" role="presentation">
			<tbody>
			<tr>
				<th scope="row">
					<h4>Clear Events Data</h4>
					<input type="checkbox" id='clear_generated_data' name='tribe-ext-test-data-generator[clear_generated]'/>
					<label for="clear_generated_data"><strong>DELETE Events, Venues and Organizers generated by this tool.</strong></label>
					<div class="tribe-dependent" data-depends="#clear_generated_data" data-condition-checked="true">
						<input type="checkbox" id='clear_all_events_data' name='tribe-ext-test-data-generator[clear_events_data]'/>
						<label for="clear_all_events_data" style="color: crimson"><strong>ALSO DELETE ALL other Events, Venues and Organizers on this site.</strong></label>
					</div>
				</th>
				<td style="vertical-align: bottom">
					<?php submit_button( 'DELETE Data' ); ?>
				</td>
			</tr>
			</tbody>
		</table>
	</form>

    <form method="post" action="" novalidate="novalidate">
        <?php wp_nonce_field( $nonce_action_key ); ?>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row">
                    <h4>Reset TEC Settings</h4>
                    <input type="checkbox" id='reset_tec_settings' name='tribe-ext-test-data-generator[reset_tec_settings]'/>
                    <label for="reset_tec_settings"><strong>RESET TEC Settings</strong></label>
                    <p class="tribe-dependent"
                       data-depends="#reset_tec_settings"
                       data-condition-checked="true"
                       style="color: crimson">
                        Deletes all saved options / settings for TEC, TEC Widgets and TEC-related Transients from the db.</p>
                </th>
                <td style="vertical-align: bottom">
                    <?php submit_button( 'RESET Settings' ); ?>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>

<datalist id="date_ranges" style="display: none">
    <option value="-2 years">
    <option value="-1 year">
    <option value="-6 months">
    <option value="-3 months">
    <option value="-2 months">
    <option value="-1 months">
    <option value="-3 weeks">
    <option value="-2 weeks">
    <option value="-1 week">
    <option value="now">
    <option value="tomorrow">
    <option value="+1 week">
    <option value="+2 weeks">
    <option value="+3 weeks">
    <option value="+1 month">
    <option value="+2 months">
    <option value="+3 months">
    <option value="+6 months">
    <option value="+1 year">
    <option value="+2 years">
</datalist>
<style>
    input.date-range {
        min-height: 30px;
        padding-left: 8px;
    }
</style>