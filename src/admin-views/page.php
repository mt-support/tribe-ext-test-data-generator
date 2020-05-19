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
        </tbody>
    </table>
    <?php submit_button( 'Generate Data' ); ?>
</form>