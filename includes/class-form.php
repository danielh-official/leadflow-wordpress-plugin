<?php

namespace LeadFlow;

final class Form {
	public function register_hooks(): void {
		\add_action( 'init', array( $this, 'register_shortcode' ) );
		\add_action( 'admin_post_leadflow_submit', array( $this, 'handle_submission' ) );
		\add_action( 'admin_post_nopriv_leadflow_submit', array( $this, 'handle_submission' ) );
	}

	public function register_shortcode(): void {
		\add_shortcode( 'leadflow_form', array( $this, 'render' ) );
	}

	public static function service_labels(): array {
		return array(
			'website-refresh'   => 'Website refresh',
			'plugin-development' => 'Custom plugin development',
			'maintenance'       => 'Ongoing maintenance',
		);
	}

	public function render(): string {
        $status = isset( $_GET['leadflow_status'] )
            ? \sanitize_text_field( \wp_unslash( $_GET['leadflow_status'] ) )
            : '';
		ob_start();
		?>
		<style>
			.leadflow-card { max-width: 720px; padding: 28px; border: 1px solid #dcdcde; border-radius: 12px; background: #fff; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06); }
			.leadflow-card h2 { margin-top: 0; }
			.leadflow-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
			.leadflow-field { margin-bottom: 18px; }
            			.leadflow-field label { display: block; margin-bottom: 6px; font-weight: 600; }
			.leadflow-field input, .leadflow-field select, .leadflow-field textarea { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #8c8f94; border-radius: 6px; }
			.leadflow-button { padding: 11px 18px; border: 0; border-radius: 6px; background: #2271b1; color: #fff; cursor: pointer; }
			.leadflow-notice { margin-bottom: 18px; padding: 12px 14px; border-radius: 6px; }
			.leadflow-notice--success { background: #edfaef; color: #135e1f; }
            .leadflow-notice--error { background: #fcf0f1; color: #8a2424; }
			.leadflow-honeypot { position: absolute; left: -9999px; }
			@media (max-width: 640px) { .leadflow-grid { grid-template-columns: 1fr; } }
		</style>

		<section class="leadflow-card">
			<h2>Request a project quote</h2>
			<p>Tell us what your business needs and we will follow up.</p>
            <?php if ( 'success' === $status ) : ?>
				<p class="leadflow-notice leadflow-notice--success">Thanks. Your enquiry has been recorded.</p>
			<?php elseif ( 'invalid' === $status ) : ?>
				<p class="leadflow-notice leadflow-notice--error">Please complete every field with a valid email address.</p>
			<?php elseif ( 'error' === $status ) : ?>
				<p class="leadflow-notice leadflow-notice--error">The enquiry could not be saved. Please try again.</p>
			<?php endif; ?>

			<form class="leadflow-form" action="<?php echo \esc_url( \admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="leadflow_submit">
                <?php echo \wp_nonce_field( 'leadflow_submit', 'leadflow_nonce', true, false ); ?>
                <div class="leadflow-honeypot" aria-hidden="true">
					<label for="leadflow_website">Website</label>
					<input id="leadflow_website" name="leadflow_website" type="text" tabindex="-1" autocomplete="off">
				</div>

				<div class="leadflow-grid">
					<div class="leadflow-field">
						<label for="leadflow_name">Name</label>
						<input id="leadflow_name" name="leadflow_name" type="text" required>
					</div>
					<div class="leadflow-field">
						<label for="leadflow_email">Email</label>
						<input id="leadflow_email" name="leadflow_email" type="email" required>
					</div>
				</div>

				<div class="leadflow-field">
					<label for="leadflow_service">Service</label>
					<select id="leadflow_service" name="leadflow_service" required>
						<option value="">Choose a service</option>
						<?php foreach ( self::service_labels() as $value => $label ) : ?>
							<option value="<?php echo \esc_html( $value ); ?>"><?php echo \esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="leadflow-field">
					<label for="leadflow_message">Project message</label>
					<textarea id="leadflow_message" name="leadflow_message" rows="6" required></textarea>
				</div>

				<button class="leadflow-button" type="submit">Send enquiry</button>
			</form>
		</section>
		<?php
		return (string) ob_get_clean();
	}

    public function handle_submission(): void {
		if ( ! isset( $_POST['leadflow_nonce'] ) ) {
			\wp_die( 'The form token is missing.', 'Invalid request', 403 );
		}

		$nonce = \sanitize_text_field( \wp_unslash( $_POST['leadflow_nonce'] ) );

		if ( ! \wp_verify_nonce( $nonce, 'leadflow_submit' ) ) {
			\wp_die( 'The form token is invalid.', 'Invalid request', 403 );
		}

        $input     = \wp_unslash( $_POST );
		$honeypot  = isset( $input['leadflow_website'] ) ? \sanitize_text_field( $input['leadflow_website'] ) : '';
		$name      = isset( $input['leadflow_name'] ) ? \sanitize_text_field( $input['leadflow_name'] ) : '';
		$email     = isset( $input['leadflow_email'] ) ? \sanitize_email( $input['leadflow_email'] ) : '';
		$service   = isset( $input['leadflow_service'] ) ? \sanitize_text_field( $input['leadflow_service'] ) : '';
		$message   = isset( $input['leadflow_message'] ) ? \sanitize_textarea_field( $input['leadflow_message'] ) : '';
		$services  = self::service_labels();

        if ( '' !== $honeypot ) {
			$this->redirect( 'success' );
		}

		if ( '' === $name || ! \is_email( $email ) || ! isset( $services[ $service ] ) || '' === $message ) {
			$this->redirect( 'invalid' );
		}

		$post_id = \wp_insert_post(
			array(
				'post_title'  => \wp_strip_all_tags( \sprintf( '%s - %s', $name, $services[ $service ] ) ),
				'post_type'   => Post_Type::TYPE,
				'post_status' => 'private',
				'meta_input'  => array(
					'_leadflow_name'    => $name,
					'_leadflow_email'   => $email,
					'_leadflow_service' => $service,
					'_leadflow_message' => $message,
				),
			),
			true
		);

		if ( \is_wp_error( $post_id ) ) {
			$this->redirect( 'error' );
		}

		$this->redirect( 'success' );
	}

    private function redirect( string $status ): void {
		$url = \wp_get_referer();

		if ( ! $url ) {
			$url = \home_url( '/' );
		}

		\wp_safe_redirect( \add_query_arg( 'leadflow_status', $status, $url ) );
		exit;
	}
}