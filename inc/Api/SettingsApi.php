<?php
/**
 * #@package redberylit_plugin
 * */

namespace Inc\Api;
class SettingsApi
{
    public $admin_pages = [];
    public $admin_sub_pages = [];
    public $settings = [];
    public $fields = [];
    public $sections = [];

    public function register()
    {
        if (!empty ($this->admin_pages)) {
            add_action('admin_menu', [$this, 'addAdminMenu']);
        }
        if (!empty($this->settings)) {
            add_action('admin_init', [$this, 'registerCustomFields']);
        }
    }

    public function AddPages(array $pages)
    {
        $this->admin_pages = $pages;
        return $this;
    }

    public function withSubPage(string $title = null)
    {
        if (empty($this->admin_pages)) {
            return $this;
        }
        $admin_page = $this->admin_pages[0];
        $sub_page = [
            [
                'parent_slug' => $admin_page['menu_slug'],
                'page_title' => $admin_page['page_title'],
                'menu_title' => ($title) ? $title : $admin_page['menu_title'],
                'capability' => $admin_page['capability'],
                'menu_slug' => $admin_page['menu_slug'],
                'callback' => $admin_page['callback']
            ]
        ];

        $this->admin_sub_pages = $sub_page;
        return $this;
    }

    public function addSubPages(array $pages)
    {
        $this->admin_sub_pages = array_merge($this->admin_sub_pages, $pages);
        return $this;

    }


    public function addAdminMenu()
    {
        foreach ($this->admin_pages as $page) {
            add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']);
        }

        foreach ($this->admin_sub_pages as $page) {
            add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback']);
        }
    }

    public function registerCustomFields()
    {

        foreach ($this->settings as $setting) {
            // register setting
            register_setting($setting['option_group'], $setting['option_name'], isset($setting['callback']) ? $setting['callback'] : '');

        }

        // register setting
        foreach ($this->settings as $setting) {
            register_setting($setting['option_group'], $setting['option_name'], isset($setting['callback']) ? $setting['callback'] : '');
        }

        // add settings section
        foreach ($this->sections as $section) {
            add_settings_section($section['id'], $section['title'], isset($section['callback']) ? $section['callback'] : '', $section['page']);
        }


        // add settings fields
        foreach ($this->fields as $field) {
            add_settings_field($field['id'], $field['title'], isset($field['callback']) ? $field['callback'] : '', $field['page'], $field['section'], isset($field['args']) ? $field['args'] : '');
        }

    }

    public function setSettings(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    public function setSections(array $sections)
    {
        $this->sections = $sections;
        return $this;
    }

    public function setFields(array $field)
    {
        $this->fields = $field;
        return $this;
    }


}

