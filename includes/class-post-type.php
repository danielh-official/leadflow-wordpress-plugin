<?php

namespace LeadFlow;

final class Post_Type
{
    public const TYPE = 'lf_lead';

    public function register_hooks(): void
    {
        \add_action('init', array($this, 'register'));
    }

    public function register(): void
    {
        \register_post_type(
            self::TYPE,
            array(
                'labels' => array(
                    'name' => 'Leads',
                    'singular_name' => 'Lead',
                    'menu_name' => 'Leads',
                    'all_items' => 'All Leads',
                    'edit_item' => 'Review Lead',
                    'view_item' => 'View Lead',
                    'search_items' => 'Search Leads',
                    'not_found' => 'No leads found.',
                ),
                'public' => false,
                'publicly_queryable' => false,
                'exclude_from_search' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_in_admin_bar' => false,
                'show_in_rest' => false,
                'has_archive' => false,
                'rewrite' => false,
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'supports' => array('title'),
            )
        );
    }
}