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
						<code>includes/class-ckgf-api.php::1161</code>
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
				</table><h3 id="convertkit_api_form_subscribe_success">
						convertkit_api_form_subscribe_success
						<code>includes/class-ckgf-api.php::197</code>
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
							<td>string</td>
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
							<td>Custom Fields (false|array).</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_api_form_subscribe_success', function( $response, $form_id, $email, $first_name, $fields ) {
	// ... your code here
}, 10, 5 );
</pre>
<h3 id="convertkit_api_sequence_subscribe_success">
						convertkit_api_sequence_subscribe_success
						<code>includes/class-ckgf-api.php::313</code>
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
						<code>includes/class-ckgf-api.php::405</code>
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
							<td>string</td>
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
						<code>includes/class-ckgf-api.php::605</code>
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
						<code>includes/class-ckgf-api.php::739</code>
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
