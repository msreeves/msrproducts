<?php
/**
 * Fix media 404s when WordPress lives in /msrproducts/ but URLs omit that prefix.
 *
 * Runs only on msreeves.co.uk (not local). Safe if URLs are already correct.
 *
 * @package msrproducts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return bool
 */
function msrproducts_is_live_msreeves_host() {
	if ( empty( $_SERVER['HTTP_HOST'] ) ) {
		return false;
	}
	$h = strtolower( (string) $_SERVER['HTTP_HOST'] );

	return strpos( $h, 'msreeves.co.uk' ) !== false;
}

/**
 * @param string $url Absolute URL to uploads or file under wp-content/uploads.
 * @return string
 */
function msrproducts_fix_subdir_upload_url( $url ) {
	if ( ! is_string( $url ) || $url === '' ) {
		return $url;
	}
	if ( strpos( $url, '/msrproducts/wp-content/uploads' ) !== false ) {
		return $url;
	}

	return (string) preg_replace(
		'#^(https?://(?:www\.)?msreeves\.co\.uk)(/wp-content/uploads)#i',
		'$1/msrproducts$2',
		$url
	);
}

/**
 * @param string $html Mixed HTML (content, img tags, etc.).
 * @return string
 */
function msrproducts_fix_subdir_uploads_in_html( $html ) {
	if ( ! is_string( $html ) || $html === '' ) {
		return $html;
	}

	$pairs = array(
		'https://msreeves.co.uk/wp-content/uploads'       => 'https://msreeves.co.uk/msrproducts/wp-content/uploads',
		'https://www.msreeves.co.uk/wp-content/uploads'   => 'https://www.msreeves.co.uk/msrproducts/wp-content/uploads',
		'http://msreeves.co.uk/wp-content/uploads'        => 'https://msreeves.co.uk/msrproducts/wp-content/uploads',
		'http://www.msreeves.co.uk/wp-content/uploads'    => 'https://www.msreeves.co.uk/msrproducts/wp-content/uploads',
	);

	$html = str_replace( array_keys( $pairs ), array_values( $pairs ), $html );

	// Root-relative URLs resolve to domain root and skip /msrproducts/.
	$html = preg_replace( '#([\'"])(/wp-content/uploads/)#', '$1/msrproducts$2', $html );

	return $html;
}

/**
 * If siteurl/home in the database are the domain root, WordPress builds wrong upload + REST URLs
 * (Media Library grid blank, thumbnails 404). Constants in wp-config override on some installs only.
 *
 * @param string $url siteurl or home from options.
 * @return string
 */
function msrproducts_fix_subdir_siteurl_option( $url ) {
	if ( ! is_string( $url ) || $url === '' ) {
		return $url;
	}
	$url = trim( $url );
	if ( preg_match( '#/msrproducts(?:/|$|\?)#i', $url ) ) {
		return $url;
	}
	if ( preg_match( '#^https?://(?:www\.)?msreeves\.co\.uk/*$#i', rtrim( $url, '/' ) ) ) {
		return rtrim( $url, '/' ) . '/msrproducts';
	}

	return $url;
}

if ( ! msrproducts_is_live_msreeves_host() ) {
	return;
}

add_filter( 'option_siteurl', 'msrproducts_fix_subdir_siteurl_option', 99 );
add_filter( 'option_home', 'msrproducts_fix_subdir_siteurl_option', 99 );

/**
 * Wrong custom "Full URL path to files" breaks Media Library; force default derivation from siteurl.
 *
 * @param mixed $value Stored option.
 * @return mixed
 */
function msrproducts_fix_upload_url_path_option( $value ) {
	$v = is_string( $value ) ? trim( $value ) : '';
	if ( $v === '' ) {
		return $value;
	}
	if ( preg_match( '#^https?://(?:www\.)?msreeves\.co\.uk/wp-content/uploads#i', $v )
		&& strpos( $v, '/msrproducts/' ) === false ) {
		return '';
	}

	return $value;
}

add_filter( 'option_upload_url_path', 'msrproducts_fix_upload_url_path_option', 99 );

add_filter(
	'upload_dir',
	function ( $uploads ) {
		if ( ! is_array( $uploads ) || ! empty( $uploads['error'] ) ) {
			return $uploads;
		}
		$baseurl = isset( $uploads['baseurl'] ) ? (string) $uploads['baseurl'] : '';
		if ( $baseurl !== '' && strpos( $baseurl, '/msrproducts/wp-content/uploads' ) === false
			&& preg_match( '#^https?://[^/]+/wp-content/uploads$#i', $baseurl ) ) {
			$uploads['baseurl'] = preg_replace( '#/wp-content/uploads$#', '/msrproducts/wp-content/uploads', $baseurl );
			$uploads['url']     = $uploads['baseurl'] . ( isset( $uploads['subdir'] ) ? $uploads['subdir'] : '' );
		}

		$expected = '';
		if ( defined( 'WP_CONTENT_DIR' ) ) {
			$expected = wp_normalize_path( trailingslashit( WP_CONTENT_DIR ) . 'uploads' );
			$expected = untrailingslashit( $expected );
		}
		if ( $expected !== '' && is_dir( $expected ) ) {
			$cur = isset( $uploads['basedir'] ) ? wp_normalize_path( (string) $uploads['basedir'] ) : '';
			if ( $cur !== $expected && ( $cur === '' || ! is_dir( $uploads['basedir'] ) ) ) {
				$uploads['basedir'] = $expected;
				$subdir           = isset( $uploads['subdir'] ) ? (string) $uploads['subdir'] : '';
				$uploads['path']    = $expected . $subdir;
			}
		}

		return $uploads;
	},
	999
);

add_filter( 'wp_get_attachment_url', 'msrproducts_fix_subdir_upload_url', 20 );

add_filter(
	'wp_calculate_image_srcset',
	function ( $sources ) {
		if ( ! is_array( $sources ) ) {
			return $sources;
		}
		foreach ( $sources as $w => $meta ) {
			if ( isset( $meta['url'] ) ) {
				$sources[ $w ]['url'] = msrproducts_fix_subdir_upload_url( (string) $meta['url'] );
			}
		}

		return $sources;
	},
	20
);

add_filter( 'the_content', 'msrproducts_fix_subdir_uploads_in_html', 999 );
add_filter( 'post_thumbnail_html', 'msrproducts_fix_subdir_uploads_in_html', 999 );
add_filter( 'widget_text', 'msrproducts_fix_subdir_uploads_in_html', 999 );
add_filter( 'widget_block_content', 'msrproducts_fix_subdir_uploads_in_html', 999 );

/**
 * Media Library grid/modal uses this for image src; without it, previews stay blank even when files exist.
 *
 * @param array<string,mixed> $response Attachment data for JS.
 * @return array<string,mixed>
 */
function msrproducts_prepare_attachment_js_urls( $response ) {
	if ( ! is_array( $response ) ) {
		return $response;
	}
	foreach ( array( 'url', 'link' ) as $key ) {
		if ( ! empty( $response[ $key ] ) && is_string( $response[ $key ] ) ) {
			$response[ $key ] = msrproducts_fix_subdir_upload_url( $response[ $key ] );
		}
	}
	if ( ! empty( $response['sizes'] ) && is_array( $response['sizes'] ) ) {
		foreach ( $response['sizes'] as $size => $data ) {
			if ( is_array( $data ) && ! empty( $data['url'] ) && is_string( $data['url'] ) ) {
				$response['sizes'][ $size ]['url'] = msrproducts_fix_subdir_upload_url( $data['url'] );
			}
		}
	}

	return $response;
}

add_filter( 'wp_prepare_attachment_for_js', 'msrproducts_prepare_attachment_js_urls', 99 );
