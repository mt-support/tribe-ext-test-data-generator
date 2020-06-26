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
				<select id='numVenues' name='tribe-ext-test-data-generator[venues][quantity]'>
					<option value='0'>None</option>
					<option value='10'>10</option>
					<option value='100'>100</option>
					<option value='1000'>1,000</option>
					<option value='10000'>10,000</option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="numOrganizers">Generate Organizers</label>
			</th>
			<td>
				<select id='numOrganizers' name='tribe-ext-test-data-generator[organizers][quantity]'>
					<option value='0'>None</option>
					<option value='10'>10</option>
					<option value='100'>100</option>
					<option value='1000'>1,000</option>
					<option value='10000'>10,000</option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="numEvents">Generate Events</label>
			</th>
			<td>
				<select id='numEvents' name='tribe-ext-test-data-generator[events][quantity]'>
					<option value='0'>None</option>
					<option value='10'>10</option>
					<option value='100'>100</option>
					<option value='1000'>1,000</option>
					<option value='10000'>10,000</option>
				</select>
			</td>
		</tr>
		<tr class="tribe-dependent" data-depends="#numEvents" data-condition-not="0" style="background-color: whitesmoke">
			<td colspan="2">
				<label for="eventFromDate">Create events between </label>
				<select id='eventFromDate' name='tribe-ext-test-data-generator[events][fromDate]'>
					<option value='now'>Now</option>
					<option value='-1 week'>1 Week ago</option>
					<option value='-2 weeks'>2 Weeks ago</option>
					<option value='-3 weeks'>3 Weeks ago</option>
					<option value='-1 month'>1 Month ago</option>
					<option value='-2 months'>2 Months ago</option>
					<option value='-3 months'>3 Months ago</option>
					<option value='-4 months'>4 Months ago</option>
					<option value='-5 months'>5 Months ago</option>
					<option value='-6 months'>6 Months ago</option>
					<option value='-7 months'>7 Months ago</option>
					<option value='-8 months'>8 Months ago</option>
					<option value='-9 months'>9 Months ago</option>
					<option value='-10 months'>10 Months ago</option>
					<option value='-11 months'>11 Months ago</option>
					<option value='-1 year'>1 Year ago</option>
					<option value='-2 years'>2 Years ago</option>
				</select>
				<label for="eventToDate"> and </label>
				<select id='eventToDate' name='tribe-ext-test-data-generator[events][toDate]'>
					<option value='+1 day'>Tomorrow</option>
					<option value='+1 week'>1 Week ahead</option>
					<option value='+2 weeks'>2 Weeks ahead</option>
					<option value='+3 weeks'>3 Weeks ahead</option>
					<option value='+1 month'>1 Month ahead</option>
					<option value='+2 months'>2 Months ahead</option>
					<option value='+3 months'>3 Months ahead</option>
					<option value='+4 months'>4 Months ahead</option>
					<option value='+5 months'>5 Months ahead</option>
					<option value='+6 months'>6 Months ahead</option>
					<option value='+7 months'>7 Months ahead</option>
					<option value='+8 months'>8 Months ahead</option>
					<option value='+9 months'>9 Months ahead</option>
					<option value='+10 months'>10 Months ahead</option>
					<option value='+11 months'>11 Months ahead</option>
					<option value='+1 year'>1 Year ahead</option>
					<option value='+2 years'>2 Years head</option>
				</select>
			</td>
		</tr>
		<?php if( class_exists( 'Tribe__Tickets__Main' ) ) : ?>
		<tr class="tribe-dependent" data-depends="#numEvents" data-condition-not="0" style="background-color: whitesmoke">
			<td colspan="2">
				<input type="checkbox" id='addRSVP' name='tribe-ext-test-data-generator[events][rsvp]'/>
				<label for="addRSVP">Add RSVP to generated events.</label>
			</td>
		</tr>
		<?php endif; ?>
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
