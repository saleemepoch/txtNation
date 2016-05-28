<?php
/**
 * txtNation Setting & Credentials
 * Created by Saleem Beg <saleem@web-epoch.com>
 */

return [
	/* REQUIRED */
	'username' => '',
	'ekey' => '',
	// also known as sender id.
	'title' => '',

	/* OPTIONAL */
	// if set, requests to txtNation will also consists of the supplied shortcode
	'shortcode' => '',

	// required only if sending MT to Bill based on texted keywords
	'keywords' => [
		/*
		 * keywords and their corresponding amounts.
		 *
		 * This will set the value when billing the customer based on the keyword texted by the user
		 *
		 *
		'BILL10' => 10.00,
		'BILL5' => 5.00,
		*/
		'BILL1' => 1.00
	]
];
