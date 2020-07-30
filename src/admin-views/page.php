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
				<label for="numVenues">Generate Venues</label>
			</th>
			<td>
                <input type="number" id='numVenues' name='tribe-ext-test-data-generator[venues][quantity]'
                        placeholder="None" style="width: 90px">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="numOrganizers">Generate Organizers</label>
			</th>
			<td>
                <input type="number" id='numOrganizers' name='tribe-ext-test-data-generator[organizers][quantity]'
                       placeholder="None" style="width: 90px">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="numEvents">Generate Events</label>
			</th>
			<td>
                <input type="number" id='numEvents' name='tribe-ext-test-data-generator[events][quantity]'
                       placeholder="None" style="width: 90px">
			</td>
		</tr>
		<tr class="tribe-dependent" data-depends="#numEvents" data-condition-not="0" style="background-color: whitesmoke">
			<td colspan="2">
				<label for="eventFromDate">Create events between </label>
                <input list="date_ranges" id='eventFromDate' name='tribe-ext-test-data-generator[events][fromDate]'
                placeholder="now, -1 week OR YYYY-MM-DD" value="now" class="date-range" onClick="this.select();">
				<label for="eventToDate"> and </label>
                <input list="date_ranges" id='eventToDate' name='tribe-ext-test-data-generator[events][toDate]'
                       placeholder="now, +1 week OR YYYY-MM-DD" value="tomorrow" class="date-range" onClick="this.select();">
                <br/>
                <p style="color: royalblue"><em>For the date range, you can use date strings like <strong>"now", "tomorrow",
                            "-2 weeks", "+3 months"</strong> or a specific date using <strong>YYYY-MM-DD</strong> format.
                        <br/>Start typing in fields for suggestions of commonly used ranges.</em></p>
            </td>
		</tr>
        <?php if( class_exists( 'Tribe\Events\Virtual\Plugin' ) ) : ?>
            <tr class="tribe-dependent" data-depends="#numEvents" data-condition-not="0" style="background-color: whitesmoke">
                <td colspan="2">
                    <input type="checkbox" id='markVirtual' name='tribe-ext-test-data-generator[events][virtual]'/>
                    <label for="markVirtual">Make them Virtual events.</label>
                </td>
            </tr>
        <?php endif; ?>
        <?php if( class_exists( 'Tribe__Events__Pro__Main' ) ) : ?>
            <tr class="tribe-dependent" data-depends="#numEvents" data-condition-not="0" style="background-color: whitesmoke">
                <td>
                    <input type="checkbox" id='isRecurring' name='tribe-ext-test-data-generator[events][recurring]'/>
                    <label for="isRecurring">Make them Recurring.</label>
                </td>
                <td class="tribe-dependent" data-depends="#isRecurring" data-condition-is-checked="true" style="background-color: whitesmoke">
                    <label for="recurringType">Recurrence: </label>
                    <select id='recurringType' name='tribe-ext-test-data-generator[events][recurring-type]'>
                        <option value='all'>All</option>
                        <option value='daily'>Daily</option>
                        <option value='weekly'>Weekly</option>
                        <option value='monthly'>Monthly</option>
                        <option value='yearly'>Yearly</option>
                </td>
            </tr>
        <?php endif; ?>
		<?php if( class_exists( 'Tribe__Tickets__Main' ) ) : ?>
		<tr class="tribe-dependent" data-depends="#numEvents" data-condition-not="0" style="background-color: whitesmoke">
			<td colspan="2">
				<input type="checkbox" id='addRSVP' name='tribe-ext-test-data-generator[events][rsvp]'/>
				<label for="addRSVP">Add RSVP to generated events.</label>
			</td>
		</tr>
		<?php endif; ?>
		<?php if( class_exists( 'Tribe__Tickets__Tickets' ) ) : ?>
			<?php
			$providers = Tribe__Tickets__Tickets::modules();
			unset( $providers[ 'Tribe__Tickets__RSVP' ] );
			if ( 0 < count( $providers ) ) : ?>
				<tr class="tribe-dependent" data-depends="#numEvents" data-condition-not="0" style="background-color: whitesmoke">
					<td colspan="2">
						<input type="checkbox" id='addTicket' name='tribe-ext-test-data-generator[events][ticket]'/>
						<label for="addTicket">Add Ticket to generated events.</label>
					</td>
				</tr>
			<?php else: ?>
				<tr class="tribe-dependent" data-depends="#numEvents" data-condition-not="0" style="background-color: whitesmoke">
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
					<input type="checkbox" id='clearGeneratedData' name='tribe-ext-test-data-generator[clearGenerated]'/>
					<label for="clearGeneratedData"><strong>DELETE Events, Venues and Organizers generated by this tool.</strong></label>
					<div class="tribe-dependent" data-depends="#clearGeneratedData" data-condition-checked="true">
						<input type="checkbox" id='clearAllEventsData' name='tribe-ext-test-data-generator[clearEventsData]'/>
						<label for="clearAllEventsData" style="color: crimson"><strong>ALSO DELETE ALL other Events, Venues and Organizers on this site.</strong></label>
					</div>
				</th>
				<td style="vertical-align: bottom">
					<?php submit_button( 'DELETE Data' ); ?>
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