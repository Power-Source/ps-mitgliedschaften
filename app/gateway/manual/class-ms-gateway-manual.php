<?php
/**
 * Manual Gateway.
 *
 * Process manual payments (Eg. check, bank transfer)
 *
 * Persisted by parent class MS_Model_Option. Singleton.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage Model
 */
class MS_Gateway_Manual extends MS_Gateway {

	const ID = 'manual';

	/**
	 * Gateway singleton instance.
	 *
	 * @since  1.0.0
	 * @var string $instance
	 */
	public static $instance;

	/**
	 * Payment information for customer.
	 *
	 * The payment procedures like bank account, agency, etc.
	 *
	 * @since  1.0.0
	 * @var string $payment_info
	 */
	protected $payment_info;

	/**
	 * Bank account holder for SEPA transfers.
	 *
	 * @since 1.0.5
	 * @var string
	 */
	protected $account_name = '';

	/**
	 * Bank name for SEPA transfers.
	 *
	 * @since 1.0.5
	 * @var string
	 */
	protected $bank_name = '';

	/**
	 * Recipient IBAN for SEPA transfers.
	 *
	 * @since 1.0.5
	 * @var string
	 */
	protected $iban = '';

	/**
	 * Recipient BIC for SEPA transfers.
	 *
	 * @since 1.0.5
	 * @var string
	 */
	protected $bic = '';

	/**
	 * Prefix for the transfer reference.
	 *
	 * @since 1.0.5
	 * @var string
	 */
	protected $reference_prefix = '';

	/**
	 * PNG data for the QR code embedded in the current instruction email.
	 *
	 * @since 1.0.5
	 * @var string
	 */
	private $mail_qr_png = '';


	/**
	 * Hook to show payment info.
	 * This is called by the MS_Factory
	 *
	 * @since  1.0.0
	 */
	public function after_load() {
		parent::after_load();

		$this->id 				= self::ID;
		$this->name 			= __( 'Manuelles Zahlungsgateway', 'membership2' );
		$this->description 		= __( '(Bankaufträge, Bargeld usw.)', 'membership2' );
		$this->group 			= __( 'Manuelle Zahlung', 'membership2' );
		$this->manual_payment 	= true; // Recurring billed/paid manually
		$this->pro_rate 		= true;
		/**
		 * No sandbox option for manual payment gateway
		 * The mode is always set to live
		 */
		$this->mode 			= 'live';

		if ( $this->active ) {
			$this->add_action(
				'ms_controller_gateway_purchase_info_content',
				'purchase_info_content'
			);
		}
	}

	/**
	 * Process a manual payment and send the transfer instructions.
	 *
	 * @since 1.0.5
	 * @param MS_Model_Relationship $subscription The related membership relationship.
	 * @return MS_Model_Invoice
	 */
	public function process_purchase( $subscription ) {
		$invoice = parent::process_purchase( $subscription );

		if ( 0 != $invoice->total ) {
			$invoice->status = MS_Model_Invoice::STATUS_BILLED;
			$invoice->save();
			$this->send_transfer_instructions( $subscription, $invoice );
		}

		return $invoice;
	}

	/**
	 * Show manual purchase/payment information.
	 *
	 * Returns a default messsage if gateway is not configured.
	 *
	 * @hook ms_controller_gateway_purchase_info_content
	 *
	 * @since  1.0.0
	 * @return string The payment info.
	 */
	public function purchase_info_content() {
		static $Processed = false;

		/**
		 * If some plugin calls `the_content()` multiple times then this
		 * function will also run multiple times.
		 * We want to process the details only once, so we have this condition!
		 */
		if ( ! $Processed ) {
			$Processed = true;

			do_action(
				'ms_gateway_manual_purchase_info_content_before',
				$this
			);

			if ( empty( $this->payment_info ) ) {
				$link = MS_Controller_Plugin::get_admin_url( 'settings' );
				ob_start();
				?>
					<?php _e( 'Dies ist nur ein Beispiel für Anweisungen zum manuellen Zahlungsgateway', 'membership2' ); ?>
					<br />
					<?php
					printf(
						__( 'Bearbeite es %shier%s', 'membership2' ),
						'<a href="' . $link . '">',
						'</a>'
					);
					?>
					<br /><br />
					<?php _e( 'Name: Beispielname.', 'membership2' ); ?>
					<br />
					<?php _e( 'Bank: Beispielbank.', 'membership2' ); ?>
					<br />
					<?php _e( 'Bankkonto: Beispielbankkonto 1234.', 'membership2' ); ?>
					<br />
				<?php
				$this->payment_info = ob_get_clean();
			}

			$this->payment_info = wpautop( $this->payment_info );

			if ( ! empty( $_POST['ms_relationship_id'] ) ) {
				$subscription 		= MS_Factory::load(
					'MS_Model_Relationship',
					$_POST['ms_relationship_id']
				);
				$invoice 			= $subscription->get_current_invoice();
				$this->payment_info .= $this->get_transfer_details( $invoice );

			}
		}

		return apply_filters(
			'ms_gateway_manual_purchase_info_content',
			$this->payment_info
		);
	}

	/**
	 * Verify required fields.
	 *
	 * @since  1.0.0
	 * @return boolean True if configured.
	 */
	public function is_configured() {
		$is_configured 	= true;
		$required 		= array( 'payment_info' );

		foreach ( $required as $field ) {
			if ( empty( $this->$field ) ) {
				$is_configured = false;
				break;
			}

			if ( ! $is_configured ) {
				$is_configured = ! empty( $this->account_name )
					&& $this->is_valid_iban( $this->normalize_iban( $this->iban ) );
			}
		}
		
		return apply_filters(
			'ms_gateway_manual_is_configured',
			$is_configured
		);
	}

	/**
	 * Validate specific property before set.
	 *
	 * @since  1.0.0
	 *
	 * @access public
	 * @param string $property The name of a property to associate.
	 * @param mixed $value The value of a property.
	 */
	public function __set( $property, $value ) {
		if ( property_exists( $this, $property ) ) {
			switch ( $property ) {
				case 'payment_info':
					$this->$property = wp_kses_post( $value );
					break;

				case 'account_name':
				case 'bank_name':
				case 'reference_prefix':
					$this->$property = sanitize_text_field( $value );
					break;

				case 'iban':
				case 'bic':
					$this->$property = strtoupper(
						preg_replace( '/\s+/', '', sanitize_text_field( $value ) )
					);
					break;

				default:
					parent::__set( $property, $value );
					break;
			}
		}

		do_action(
			'ms_gateway_manual__set_after',
			$property,
			$value,
			$this
		);
	}

	/**
	 * Render the structured transfer details and, where possible, a local EPC QR code.
	 *
	 * @since 1.0.5
	 * @param MS_Model_Invoice $invoice Invoice being paid.
	 * @return string
	 */
	private function get_transfer_details( $invoice, $context = 'web' ) {
		$iban = $this->normalize_iban( $this->iban );
		$bic = strtoupper( preg_replace( '/\s+/', '', $this->bic ) );
		$amount = MS_Helper_Billing::format_price( $invoice->total );
		$reference = $this->get_payment_reference( $invoice );
		$content = sprintf(
			'<div class="ms-manual-price">%s: <span class="ms-price">%s%s</span></div>',
			esc_html__( 'Gesamtbetrag', 'membership2' ),
			esc_html( $invoice->currency ),
			esc_html( $amount )
		);

		if ( ! $this->account_name && ! $iban && ! $this->bank_name && ! $bic ) {
			return $content;
		}

		$content .= '<div class="ms-manual-transfer-details"><h3>'
			. esc_html__( 'Überweisungsdaten', 'membership2' )
			. '</h3><dl>';
		$content .= $this->get_transfer_detail( __( 'Kontoinhaber', 'membership2' ), $this->account_name );
		$content .= $this->get_transfer_detail( __( 'Bank', 'membership2' ), $this->bank_name );
		$content .= $this->get_transfer_detail( __( 'IBAN', 'membership2' ), $iban, true );
		$content .= $this->get_transfer_detail( __( 'BIC', 'membership2' ), $bic, true );
		$content .= $this->get_transfer_detail( __( 'Betrag', 'membership2' ), $amount . ' ' . $invoice->currency );
		$content .= $this->get_transfer_detail( __( 'Verwendungszweck', 'membership2' ), $reference, true );
		$content .= '</dl>';

		if ( ! $this->account_name || ! $this->is_valid_iban( $iban ) ) {
			$content .= '<p class="ms-manual-transfer-missing-details">'
				. esc_html__( 'Die Bankverbindung ist noch nicht vollständig eingerichtet. Bitte kontaktiere den Veranstalter für die Überweisungsdaten.', 'membership2' )
				. '</p></div>';
			return $content;
		}

		if ( 'EUR' === strtoupper( $invoice->currency ) && $amount !== '' && $reference ) {
			$content .= $this->get_epc_qr_code(
				$this->get_epc_payload( $iban, $bic, $amount, $reference ),
				$context
			);
		}

		return $content . '</div>';
	}

	/**
	 * Render one transfer detail row.
	 *
	 * @since 1.0.5
	 * @param string $label Row label.
	 * @param string $value Row value.
	 * @param bool   $code Whether the value is technical data.
	 * @return string
	 */
	private function get_transfer_detail( $label, $value, $code = false ) {
		if ( '' === $value ) {
			return '';
		}

		$value = esc_html( $value );
		if ( $code ) {
			$value = '<code>' . $value . '</code>';
		}

		return '<dt>' . esc_html( $label ) . '</dt><dd>' . $value . '</dd>';
	}

	/**
	 * Build the local SEPA EPC QR code.
	 *
	 * @since 1.0.5
	 * @param string $payload EPC payload.
	 * @return string
	 */
	private function get_epc_qr_code( $payload, $context ) {
		$autoload = MS_PLUGIN_BASE_DIR . '/lib/vendor/autoload.php';
		if ( ! is_readable( $autoload ) ) {
			MS_Helper_Debug::debug_log( 'EPC-QR-Code konnte nicht erzeugt werden: Composer-Autoloader fehlt.' );
			return '<p class="ms-manual-transfer-qr-error">'
				. esc_html__( 'Der Überweisungs-QR-Code konnte nicht erzeugt werden. Bitte nutze die angezeigten Überweisungsdaten.', 'membership2' )
				. '</p>';
		}

		require_once $autoload;

		try {
			if ( 'mail' === $context ) {
				$png = $this->render_qr_png( $payload );
				if ( $png ) {
					$this->mail_qr_png = $png;

					return '<p class="ms-manual-transfer-qr"><img src="cid:ms-epc-qr" width="240" height="240" alt="'
						. esc_attr__( 'SEPA-Überweisungs-QR-Code', 'membership2' )
						. '" /><br /><span>'
						. esc_html__( 'Mit der Banking-App scannen, um Empfänger, Betrag und Verwendungszweck zu übernehmen.', 'membership2' )
						. '</span></p>';
				}

				throw new RuntimeException( 'PNG-QR-Code konnte nicht erzeugt werden.' );
			}

			$options = new \chillerlan\QRCode\QROptions(
				array(
					'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG,
					'imageBase64' => true,
					'scale' => 6,
					'eccLevel' => \chillerlan\QRCode\QRCode::ECC_M,
				)
			);
			$qr_code = ( new \chillerlan\QRCode\QRCode( $options ) )->render( $payload );
		} catch ( Throwable $exception ) {
			MS_Helper_Debug::debug_log( 'EPC-QR-Code konnte nicht erzeugt werden: ' . $exception->getMessage() );
			return '<p class="ms-manual-transfer-qr-error">'
				. esc_html__( 'Der Überweisungs-QR-Code konnte nicht erzeugt werden. Bitte nutze die angezeigten Überweisungsdaten.', 'membership2' )
				. '</p>';
		}

		return '<p class="ms-manual-transfer-qr"><img src="' . esc_attr( $qr_code )
			. '" width="240" height="240" alt="' . esc_attr__( 'SEPA-Überweisungs-QR-Code', 'membership2' )
			. '" /><br /><span>'
			. esc_html__( 'Mit der Banking-App scannen, um Empfänger, Betrag und Verwendungszweck zu übernehmen.', 'membership2' )
			. '</span></p>';
	}

		/**
		 * Render an EPC QR code as a self-contained PNG for email clients.
		 *
		 * @since 1.0.5
		 * @param string $payload EPC payload.
		 * @return string
		 */
		private function render_qr_png( $payload ) {
			if ( ! function_exists( 'gzcompress' ) ) {
				return '';
			}

			$matrix = ( new \chillerlan\QRCode\QRCode(
				new \chillerlan\QRCode\QROptions(
					array( 'eccLevel' => \chillerlan\QRCode\QRCode::ECC_M )
				)
			) )->getMatrix( $payload )->matrix( true );
			$modules = count( $matrix );
			$scale = 6;
			$quiet_zone = 4;
			$size = ( $modules + 2 * $quiet_zone ) * $scale;
			$raw = '';

			for ( $y = 0; $y < $size; $y++ ) {
				$module_y = intdiv( $y, $scale ) - $quiet_zone;
				$row = '';
				for ( $x = 0; $x < $size; $x++ ) {
					$module_x = intdiv( $x, $scale ) - $quiet_zone;
					$dark = $module_y >= 0
						&& $module_y < $modules
						&& $module_x >= 0
						&& $module_x < $modules
						&& ! empty( $matrix[ $module_y ][ $module_x ] );
					$row .= $dark ? "\x00" : "\xFF";
				}
				$raw .= "\x00" . $row;
			}

			$chunk = function ( $type, $data ) {
				return pack( 'N', strlen( $data ) ) . $type . $data . pack( 'N', crc32( $type . $data ) );
			};

			return "\x89PNG\r\n\x1a\n"
				. $chunk( 'IHDR', pack( 'NNCCCCC', $size, $size, 8, 0, 0, 0, 0 ) )
				. $chunk( 'IDAT', gzcompress( $raw, 9 ) )
				. $chunk( 'IEND', '' );
		}

		/**
		 * Send the transfer details immediately after the user selects this gateway.
		 *
		 * @since 1.0.5
		 * @param MS_Model_Relationship $subscription The related membership relationship.
		 * @param MS_Model_Invoice      $invoice Invoice being paid.
		 * @return bool
		 */
		private function send_transfer_instructions( $subscription, $invoice ) {
			$member = $subscription->get_member();
			if ( ! $member || ! is_email( $member->email ) ) {
				MS_Helper_Debug::debug_log( 'Überweisungsanweisungen konnten nicht gesendet werden: ungültige Mitglieds-E-Mail.' );
				return false;
			}

			$this->mail_qr_png = '';
			$details = $this->get_transfer_details( $invoice, 'mail' );
			$subject = sprintf(
				__( 'Deine Überweisungsdaten für %s', 'membership2' ),
				get_option( 'blogname' )
			);
			$message = '<!DOCTYPE html><html><body>' . sprintf(
				'<p>%s</p>',
				esc_html__( 'Deine Zahlung ist ausstehend. Bitte überweise den Betrag mit folgenden Daten:', 'membership2' )
			) . $details . '</body></html>';
			$qr = $this->mail_qr_png;
			$attach = function ( $phpmailer ) use ( $qr ) {
				if ( $qr ) {
					$phpmailer->addStringEmbeddedImage(
						$qr,
						'ms-epc-qr',
						'sepa-qr.png',
						'base64',
						'image/png'
					);
				}
			};

			add_action( 'phpmailer_init', $attach );
			$sent = wp_mail(
				$member->email,
				$subject,
				$message,
				array( 'Content-Type: text/html; charset=UTF-8' )
			);
			remove_action( 'phpmailer_init', $attach );

			if ( ! $sent ) {
				MS_Helper_Debug::debug_log( 'Überweisungsanweisungen konnten nicht gesendet werden.' );
			}

			return $sent;
		}

	/**
	 * Create the EPC QR payload for a credit transfer.
	 *
	 * @since 1.0.5
	 * @param string $iban Recipient IBAN.
	 * @param string $bic Recipient BIC.
	 * @param string $amount Transfer amount.
	 * @param string $reference Transfer reference.
	 * @return string
	 */
	private function get_epc_payload( $iban, $bic, $amount, $reference ) {
		return implode(
			"\n",
			array(
				'BCD',
				'001',
				'1',
				'SCT',
				preg_match( '/^[A-Z0-9]{8}([A-Z0-9]{3})?$/', $bic ) ? $bic : '',
				$this->truncate( $this->account_name, 70 ),
				$iban,
				'EUR' . $amount,
				'',
				'',
				$this->truncate( $reference, 140 ),
			)
		);
	}

	/**
	 * Return the unique payment reference for an invoice.
	 *
	 * @since 1.0.5
	 * @param MS_Model_Invoice $invoice Invoice being paid.
	 * @return string
	 */
	private function get_payment_reference( $invoice ) {
		$prefix = trim( $this->truncate( $this->reference_prefix, 100 ) );
		$reference = trim( $prefix . ' ' . $invoice->get_invoice_number() );

		return $this->truncate( $reference, 140 );
	}

	/**
	 * Normalize an IBAN before validation or display.
	 *
	 * @since 1.0.5
	 * @param string $iban IBAN to normalize.
	 * @return string
	 */
	private function normalize_iban( $iban ) {
		return strtoupper( preg_replace( '/\s+/', '', $iban ) );
	}

	/**
	 * Verify an IBAN with its ISO 13616 checksum.
	 *
	 * @since 1.0.5
	 * @param string $iban Normalized IBAN.
	 * @return bool
	 */
	private function is_valid_iban( $iban ) {
		if ( ! preg_match( '/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/', $iban ) ) {
			return false;
		}

		$check = substr( $iban, 4 ) . substr( $iban, 0, 4 );
		$check = preg_replace_callback(
			'/[A-Z]/',
			function ( $match ) {
				return (string) ( ord( $match[0] ) - 55 );
			},
			$check
		);
		$remainder = 0;
		foreach ( str_split( $check ) as $digit ) {
			$remainder = (int) ( ( $remainder . $digit ) % 97 );
		}

		return 1 === $remainder;
	}

	/**
	 * Truncate a UTF-8 string without splitting characters.
	 *
	 * @since 1.0.5
	 * @param string $value Value to truncate.
	 * @param int    $length Maximum length.
	 * @return string
	 */
	private function truncate( $value, $length ) {
		return function_exists( 'mb_substr' )
			? mb_substr( $value, 0, $length, 'UTF-8' )
			: substr( $value, 0, $length );
	}

}