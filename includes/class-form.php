<?php

namespace LeadFlow;

final class Form {
	public function register_hooks(): void {
		\add_action( 'init', array( $this, 'register_shortcode' ) );
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
			@media (max-width: 640px) { .leadflow-grid { grid-template-columns: 1fr; } }
		</style>

		<section class="leadflow-card">
			<h2>Request a project quote</h2>
			<p>Tell us what your business needs and we will follow up.</p>

			<form class="leadflow-form" action="<?php echo \esc_url( \admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="leadflow_submit">

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
}