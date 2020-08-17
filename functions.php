// include custom jQuery
function ao_include_custom_jquery() {

	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', array(), null, true);
}

// simple js defer
function defer_parsing_of_js( $url ) {
    if ( is_user_logged_in() ) return $url; //don't break WP Admin
    if ( FALSE === strpos( $url, '.js' ) ) return $url;
    if ( strpos( $url, 'jquery.js' ) ) return $url;
    return str_replace( ' src', ' defer src', $url );
}

// defer jquery & js
function ao_ccss_defer_jquery( $in ) {
	if (preg_match_all( '#<script.*>(.*)</script>#Usmi', $in, $matches, PREG_SET_ORDER ) ) {
	foreach( $matches as $match ) {
		if ( ( ! preg_match('/<script.* type\s?=.*>/', $match[0]) || preg_match( '/type\s*=\s*[\'"]?(?:text|application)\/(?:javascript|ecmascript)[\'"]?/i', $match[0] ) ) && $match[1] !== '' && ( strpos( $match[1], 'jQuery' ) !== false || strpos( $match[1], '$' ) !== false ) ) {
		// inline js that requires jquery, wrap deferring JS around it to defer it.
		$new_match = 'var aoDeferInlineJQuery=function(){'.$match[1].'}; if (document.readyState === "loading") {document.addEventListener("DOMContentLoaded", aoDeferInlineJQuery);} else {aoDeferInlineJQuery();}';
		$in = str_replace( $match[1], $new_match, $in );
		} else if ( $match[1] === '' && strpos( $match[0], 'src=' ) !== false && strpos( $match[0], 'defer' ) === false ) {
		// linked non-aggregated JS, defer it.
		$new_match = str_replace( '<script ', '<script defer ', $match[0] );
		$in = str_replace( $match[0], $new_match, $in );
		}
		}
	}
	return $in;
}

//add_filter( 'autoptimize_html_after_minify', 'ao_ccss_defer_jquery', 11, 1 );
//add_action('wp_enqueue_scripts', 'ao_include_custom_jquery');
//add_filter( 'script_loader_tag', 'defer_parsing_of_js', 10 );

