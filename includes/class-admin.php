<?php

namespace LeadFlow;

final class Admin
{
    public function register_hooks(): void
    {
        \add_filter('manage_' . Post_Type::TYPE . '_posts_columns', array($this, 'columns'));
        \add_action('manage_' . Post_Type::TYPE . '_posts_custom_column', array($this, 'column_content'), 10, 2);
        \add_action('add_meta_boxes', array($this, 'add_details_box'));
    }

    public function columns(array $columns): array
    {
        $updated = array();

        foreach ($columns as $key => $label) {
            if ('date' === $key) {
                $updated['leadflow_service'] = 'Service';
                $updated['leadflow_email'] = 'Email';
            }

            $updated[$key] = $label;
        }

        return $updated;
    }

    public function column_content(string $column, int $post_id): void
    {
        if ('leadflow_service' === $column) {
            $service = \get_post_meta($post_id, '_leadflow_service', true);
            $services = Form::service_labels();
            echo \esc_html($services[$service] ?? $service);
        }

        if ('leadflow_email' === $column) {
            echo \esc_html(\get_post_meta($post_id, '_leadflow_email', true));
        }
    }

    public function add_details_box(): void
    {
        \add_meta_box(
            'leadflow-enquiry-details',
            'Enquiry Details',
            array($this, 'render_details'),
            Post_Type::TYPE,
            'normal',
            'high'
        );
    }
    public function render_details($post): void
    {
        $name = \get_post_meta($post->ID, '_leadflow_name', true);
        $email = \get_post_meta($post->ID, '_leadflow_email', true);
        $service = \get_post_meta($post->ID, '_leadflow_service', true);
        $message = \get_post_meta($post->ID, '_leadflow_message', true);
        $services = Form::service_labels();
        ?>
        <p><strong>Name</strong><br><?php echo \esc_html($name); ?></p>
        <p><strong>Email</strong><br><?php echo \esc_html($email); ?></p>
        <p><strong>Service</strong><br><?php echo \esc_html($services[$service] ?? $service); ?></p>
        <p><strong>Project message</strong><br><?php echo \nl2br(\esc_html($message)); ?></p>
        <?php
    }
}