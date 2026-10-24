<?php

if ( PHP_SAPI !== 'cli' ) {
	exit( 'Run this check from the command line.' );
}

$wordpress = isset( $argv[1] ) ? rtrim( $argv[1], '/\\' ) : dirname( __DIR__, 4 );
$directory = isset( $argv[2] ) ? rtrim( $argv[2], '/\\' ) : dirname( __DIR__ ) . '/languages';
$pomo      = $wordpress . '/wp-includes/pomo';

if ( ! is_file( $pomo . '/po.php' ) || ! is_file( $pomo . '/mo.php' ) ) {
	fwrite( STDERR, "Usage: php scripts/check-translations.php [wordpress-root] [catalog-directory]\nWordPress/ClassicPress POMO classes were not found.\n" );
	exit( 1 );
}

require_once $pomo . '/po.php';
require_once $pomo . '/mo.php';

function ms_i18n_placeholders( $text ) {
	preg_match_all( '/%%|%(?:([1-9][0-9]*)\$)?[-+0 \'#]*(?:[0-9]+|\*)?(?:\.(?:[0-9]+|\*))?([bcdeEfFgGosuxX])/', $text, $matches, PREG_SET_ORDER );
	$result = array();
	$index  = 0;
	foreach ( $matches as $match ) {
		if ( '%%' === $match[0] ) {
			$result[] = 'literal:%%';
			continue;
		}
		$position = '' !== $match[1] ? (int) $match[1] : ++$index;
		$result[] = $position . ':' . $match[2];
	}
	sort( $result );
	return $result;
}

function ms_i18n_markup( $text ) {
	preg_match_all( '/<\/?([a-z][a-z0-9]*)\b[^>]*>/i', $text, $matches, PREG_SET_ORDER );
	$result = array();
	foreach ( $matches as $match ) {
		$result[] = ( strpos( $match[0], '</' ) === 0 ? '/' : '' ) . strtolower( $match[1] );
	}
	return $result;
}

function ms_i18n_protected_values( $text ) {
	preg_match_all( '/(?:href|src|class|id)\s*=\s*["\']([^"\']*)["\']|\[(?:\/?ms[-_][^\]]*|username)\]/i', $text, $matches );
	sort( $matches[0] );
	return $matches[0];
}

function ms_i18n_load( $filename, $class ) {
	$catalog = new $class();
	if ( ! $catalog->import_from_file( $filename ) ) {
		fwrite( STDERR, "Cannot load translation catalog: $filename\n" );
		exit( 1 );
	}
	return $catalog;
}

$template = ms_i18n_load( $directory . '/membership2.pot', 'PO' );
$errors   = array();
if ( ! count( $template->entries ) ) {
	fwrite( STDERR, "The translation template is empty.\n" );
	exit( 1 );
}

foreach ( array( 'en_US', 'en_GB' ) as $locale ) {
	$po = ms_i18n_load( $directory . "/membership2-$locale.po", 'PO' );
	$mo = ms_i18n_load( $directory . "/membership2-$locale.mo", 'MO' );

	foreach ( array( 'PO' => $po, 'MO' => $mo ) as $format => $catalog ) {
		if ( ! isset( $catalog->headers['Language'] ) || $locale !== $catalog->headers['Language'] ) {
			$errors[] = "$locale $format: incorrect Language header";
		}
		if ( ! isset( $catalog->headers['Plural-Forms'] ) || 'nplurals=2; plural=(n != 1);' !== $catalog->headers['Plural-Forms'] ) {
			$errors[] = "$locale $format: incorrect English plural rules";
		}
		foreach ( array_diff_key( $template->entries, $catalog->entries ) as $entry ) {
			$errors[] = "$locale $format: missing " . $entry->singular;
		}
		foreach ( array_diff_key( $catalog->entries, $template->entries ) as $entry ) {
			$errors[] = "$locale $format: obsolete " . $entry->singular;
		}
	}

	foreach ( $po->entries as $key => $entry ) {
		$label = "$locale: " . $entry->singular;
		if ( in_array( 'fuzzy', $entry->flags, true ) ) {
			$errors[] = "$label: fuzzy translation";
		}
		$source = isset( $template->entries[ $key ] ) ? $template->entries[ $key ] : $entry;
		$count  = $source->is_plural ? 2 : 1;
		if ( $entry->plural !== $source->plural || count( $entry->translations ) !== $count ) {
			$errors[] = "$label: incorrect plural forms";
		}
		foreach ( $entry->translations as $index => $translation ) {
			if ( '' === trim( $translation ) ) {
				$errors[] = "$label: empty translation";
			}
			$original = $index === 0 ? $source->singular : $source->plural;
			if ( ms_i18n_placeholders( $original ) !== ms_i18n_placeholders( $translation ) ) {
				$errors[] = "$label [$index]: printf placeholders differ";
			}
			if ( ms_i18n_markup( $original ) !== ms_i18n_markup( $translation ) ) {
				$errors[] = "$label [$index]: HTML tags differ";
			}
			if ( ms_i18n_protected_values( $original ) !== ms_i18n_protected_values( $translation ) ) {
				$errors[] = "$label [$index]: HTML attributes or shortcode tokens differ";
			}
		}
		if ( isset( $mo->entries[ $key ] ) && $mo->entries[ $key ]->translations !== $entry->translations ) {
			$errors[] = "$label: compiled MO differs from PO";
		}
		if ( isset( $entry->translations[0] ) && $mo->translate( $entry->singular, $entry->context ) !== $entry->translations[0] ) {
			$errors[] = "$label: runtime singular lookup differs";
		}
		if ( $entry->is_plural && isset( $entry->translations[1] ) ) {
			foreach ( array( 0, 1, 2, 5 ) as $number ) {
				$expected = $entry->translations[ $number === 1 ? 0 : 1 ];
				if ( $mo->translate_plural( $entry->singular, $entry->plural, $number, $entry->context ) !== $expected ) {
					$errors[] = "$label: runtime plural lookup differs for $number";
				}
			}
		}
	}

	printf( "%s: %d template entries, %d PO entries, %d compiled MO entries\n", $locale, count( $template->entries ), count( $po->entries ), count( $mo->entries ) );
}

if ( $errors ) {
	foreach ( $errors as $error ) {
		fwrite( STDERR, $error . "\n" );
	}
	exit( 1 );
}

echo "English catalogs are complete; plural forms, placeholders, markup and compiled translations match.\n";
