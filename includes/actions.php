<?php

// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print cc field in donation form conditionally.
 *
 * @param $form_id
 *
 * @return bool
 * @since 1.0
 */
function give_utrust_cc_form_callback( $form_id ) {
    $description = give_get_option( 'utrust_description');
	printf(
		'
				<fieldset class="no-fields">
					<div style="display: flex; justify-content: center; margin-top: 20px; align-items: center;">
                        <svg style="
                        width: 40px;
                        height: 40px;
                    " viewBox="0 0 36 36" preserveAspectRatio="xMidYMid meet" fill="#5B47FF" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Icon"><path d="M26.6262 8.38233V12.9484V26.3557H9.37212V12.0981V8.38233H26.6262V3H16.0401L4 12.3362V18.3273C4 26.1602 10.4095 32.7363 18.2423 32.6036C25.8507 32.4744 32 26.2452 32 18.6062V8.38233H26.6262Z"></path></svg>
					</div>
					<p style="text-align: center;"><b>%1$s</b></p>
				</fieldset>
				', $description
	);
	return true;
}

add_action( 'give_utrust_cc_form', 'give_utrust_cc_form_callback' );
