<h1>Filters</h1><table>
				<thead>
					<tr>
						<th>File</th>
						<th>Filter Name</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody><tr>
						<td colspan="3">../includes/class-ckgf-api.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_api_get_timeout"><code>convertkit_api_get_timeout</code></a></td>
						<td>Defines the maximum time to allow the API request to run.</td>
					</tr>
					</tbody>
				</table><h3 id="convertkit_api_get_timeout">
						convertkit_api_get_timeout
						<code>includes/class-ckgf-api.php::1242</code>
					</h3><h4>Overview</h4>
						<p>Defines the maximum time to allow the API request to run.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$timeout</td>
							<td>int</td>
							<td>Timeout, in seconds.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_api_get_timeout', function( $timeout ) {
	// ... your code here
	// Return value
	return $timeout;
}, 10, 1 );
</pre>
<h1>Actions</h1><table>
				<thead>
					<tr>
						<th>File</th>
						<th>Filter Name</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody><tr>
						<td colspan="3">../includes/class-wp-ckgf.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_gravity_forms_initialize_admin"><code>convertkit_gravity_forms_initialize_admin</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_gravity_forms_initialize_frontend"><code>convertkit_gravity_forms_initialize_frontend</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_gravity_forms_initialize_global"><code>convertkit_gravity_forms_initialize_global</code></a></td>
						<td></td>
					</tr><tr>
						<td colspan="3">../includes/class-ckgf-api.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_api_form_subscribe_success"><code>convertkit_api_form_subscribe_success</code></a></td>
						<td>Runs actions immediately after the email address was successfully subscribed to the form.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_api_sequence_subscribe_success"><code>convertkit_api_sequence_subscribe_success</code></a></td>
						<td>Runs actions immediately after the email address was successfully subscribed to the sequence.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_api_tag_subscribe_success"><code>convertkit_api_tag_subscribe_success</code></a></td>
						<td>Runs actions immediately after the email address was successfully subscribed to the tag.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_api_form_unsubscribe_success"><code>convertkit_api_form_unsubscribe_success</code></a></td>
						<td>Runs actions immediately after the email address was successfully unsubscribed.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_api_purchase_create_success"><code>convertkit_api_purchase_create_success</code></a></td>
						<td>Runs actions immediately after the purchase data address was successfully created.</td>
					</tr>
					</tbody>
				</table><h3 id="convertkit_gravity_forms_initialize_admin">
						convertkit_gravity_forms_initialize_admin
						<code>includes/class-wp-ckgf.php::104</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_gravity_forms_initialize_admin', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_gravity_forms_initialize_frontend">
						convertkit_gravity_forms_initialize_frontend
						<code>includes/class-wp-ckgf.php::125</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_gravity_forms_initialize_frontend', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_gravity_forms_initialize_global">
						convertkit_gravity_forms_initialize_global
						<code>includes/class-wp-ckgf.php::144</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_gravity_forms_initialize_global', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_api_form_subscribe_success">
						convertkit_api_form_subscribe_success
						<code>includes/class-ckgf-api.php::212</code>
					</h3><h4>Overview</h4>
						<p>Runs actions immediately after the email address was successfully subscribed to the form.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$response</td>
							<td>array</td>
							<td>API Response</td>
						</tr><tr>
							<td>$form_id</td>
							<td>int</td>
							<td>Form ID</td>
						</tr><tr>
							<td>$email</td>
							<td>string</td>
							<td>Email Address</td>
						</tr><tr>
							<td>$first_name</td>
							<td>string</td>
							<td>First</td>
						</tr><tr>
							<td>$fields</td>
							<td>mixed</td>
							<td>Custom Fields (false|array)</td>
						</tr><tr>
							<td>$tag_ids</td>
							<td>mixed</td>
							<td>Tags (false|array)</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_api_form_subscribe_success', function( $response, $form_id, $email, $first_name, $fields, $tag_ids ) {
	// ... your code here
}, 10, 6 );
</pre>
<h3 id="convertkit_api_sequence_subscribe_success">
						convertkit_api_sequence_subscribe_success
						<code>includes/class-ckgf-api.php::344</code>
					</h3><h4>Overview</h4>
						<p>Runs actions immediately after the email address was successfully subscribed to the sequence.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$response</td>
							<td>array</td>
							<td>API Response</td>
						</tr><tr>
							<td>$sequence_id</td>
							<td>string</td>
							<td>Sequence ID</td>
						</tr><tr>
							<td>$email</td>
							<td>string</td>
							<td>Email Address</td>
						</tr><tr>
							<td>$fields</td>
							<td>mixed</td>
							<td>Custom Fields (false|array)</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_api_sequence_subscribe_success', function( $response, $sequence_id, $email, $fields ) {
	// ... your code here
}, 10, 4 );
</pre>
<h3 id="convertkit_api_tag_subscribe_success">
						convertkit_api_tag_subscribe_success
						<code>includes/class-ckgf-api.php::452</code>
					</h3><h4>Overview</h4>
						<p>Runs actions immediately after the email address was successfully subscribed to the tag.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$response</td>
							<td>array</td>
							<td>API Response</td>
						</tr><tr>
							<td>$tag_id</td>
							<td>int</td>
							<td>Tag ID</td>
						</tr><tr>
							<td>$email</td>
							<td>string</td>
							<td>Email Address</td>
						</tr><tr>
							<td>$fields</td>
							<td>mixed</td>
							<td>Custom Fields (false|array).</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_api_tag_subscribe_success', function( $response, $tag_id, $email, $fields ) {
	// ... your code here
}, 10, 4 );
</pre>
<h3 id="convertkit_api_form_unsubscribe_success">
						convertkit_api_form_unsubscribe_success
						<code>includes/class-ckgf-api.php::687</code>
					</h3><h4>Overview</h4>
						<p>Runs actions immediately after the email address was successfully unsubscribed.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$response</td>
							<td>array</td>
							<td>API Response</td>
						</tr><tr>
							<td>$email</td>
							<td>string</td>
							<td>Email Address</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_api_form_unsubscribe_success', function( $response, $email ) {
	// ... your code here
}, 10, 2 );
</pre>
<h3 id="convertkit_api_purchase_create_success">
						convertkit_api_purchase_create_success
						<code>includes/class-ckgf-api.php::821</code>
					</h3><h4>Overview</h4>
						<p>Runs actions immediately after the purchase data address was successfully created.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$response</td>
							<td>array</td>
							<td>API Response</td>
						</tr><tr>
							<td>$purchase</td>
							<td>array</td>
							<td>Purchase Data</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_api_purchase_create_success', function( $response, $purchase ) {
	// ... your code here
}, 10, 2 );
</pre>
