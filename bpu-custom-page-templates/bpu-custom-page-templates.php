<?php
/*
Plugin Name: BPU Custom Page Templates
Plugin URI: https://blackprofessionals.uk
Description: Adds custom ACF-ready page templates for the BPU Report and Safe Space pages, using the Navy & Red brand colors. Automatically registers required ACF fields.
Version: 2.0
Author: BPU
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class BPU_Page_Templates_Plugin {

    public function __construct() {
        add_filter( 'theme_page_templates', array( $this, 'add_templates' ) );
        add_filter( 'template_include', array( $this, 'view_template' ) );

        // Hook into ACF init to register local fields
        add_action( 'acf/init', array( $this, 'register_acf_fields' ) );

    }

    // Register the templates in the WP dropdown
    public function add_templates( $templates ) {
        $templates['bpu-report.php'] = 'BPU Report Template';
        $templates['bpu-safespace.php'] = 'BPU Safe Space Template';
        return $templates;
    }

    // Load our custom HTML instead of the theme's page.php when selected
    public function view_template( $template ) {
        global $post;
        if ( ! $post ) return $template;

        $current_template = get_post_meta( $post->ID, '_wp_page_template', true );

        if ( $current_template == 'bpu-report.php' ) {
            $this->render_report_template();
            exit;
        } elseif ( $current_template == 'bpu-safespace.php' ) {
            $this->render_safespace_template();
            exit;
        }

        return $template;
    }

    // Automatically register all ACF fields for these templates
    public function register_acf_fields() {
        if( !function_exists('acf_add_local_field_group') ) return;

        // ==========================================
        // 1. ACF FIELDS FOR BPU REPORT
        // ==========================================
        acf_add_local_field_group(array(
            'key' => 'group_bpu_report_settings',
            'title' => 'BPU Report Content Settings',
            'fields' => array(
                // --- HERO TAB ---
                array( 'key' => 'field_bpu_tab_hero', 'label' => 'Hero Section', 'type' => 'tab', 'placement' => 'top' ),
                array( 'key' => 'field_bpu_hero_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'hero_eyebrow', 'type' => 'text', 'default_value' => 'BPU Scotland · 2026 Report' ),
                array( 'key' => 'field_bpu_hero_h1', 'label' => 'Heading Line 1', 'name' => 'hero_heading_1', 'type' => 'text', 'default_value' => 'Harnessing Talent' ),
                array( 'key' => 'field_bpu_hero_h2', 'label' => 'Heading Line 2', 'name' => 'hero_heading_2', 'type' => 'text', 'default_value' => 'Unlocking Scotland\'s<br>Competitive Edge' ),
                array( 'key' => 'field_bpu_hero_h3', 'label' => 'Heading Line 3 (Small)', 'name' => 'hero_heading_3', 'type' => 'text', 'default_value' => 'Black Professionals UK · Big Survey' ),
                array( 'key' => 'field_bpu_hero_sub', 'label' => 'Subtext', 'name' => 'hero_subtext', 'type' => 'textarea', 'default_value' => 'A landmark evidence-based report on the professional realities...' ),
                array(
                    'key' => 'field_bpu_hero_nodes',
                    'label' => 'Floating Data Nodes',
                    'name' => 'hero_data_nodes',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'sub_fields' => array(
                        array( 'key' => 'field_bpu_hn_num', 'label' => 'Number', 'name' => 'number', 'type' => 'text' ),
                        array( 'key' => 'field_bpu_hn_lbl', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
                        array( 'key' => 'field_bpu_hn_acc', 'label' => 'Highlight (Red)', 'name' => 'is_accent', 'type' => 'true_false', 'ui' => 1 ),
                    ),
                ),

                // --- CONTEXT TAB ---
                array( 'key' => 'field_bpu_tab_context', 'label' => 'Context Section', 'type' => 'tab', 'placement' => 'top' ),
                array( 'key' => 'field_bpu_ctx_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'context_eyebrow', 'type' => 'text', 'default_value' => 'Context & Background' ),
                array( 'key' => 'field_bpu_ctx_title', 'label' => 'Section Title', 'name' => 'context_title', 'type' => 'text', 'default_value' => 'Scotland has a talent problem.<br>The talent isn\'t the problem.' ),
                array( 'key' => 'field_bpu_ctx_body', 'label' => 'Section Body', 'name' => 'context_body', 'type' => 'textarea' ),
                array(
                    'key' => 'field_bpu_ctx_blocks',
                    'label' => 'Context Blocks',
                    'name' => 'context_blocks',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array( 'key' => 'field_bpu_cb_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
                        array( 'key' => 'field_bpu_cb_desc', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea' ),
                    ),
                ),

                // --- FINDINGS TAB ---
                array( 'key' => 'field_bpu_tab_findings', 'label' => 'Findings Section', 'type' => 'tab', 'placement' => 'top' ),
                array( 'key' => 'field_bpu_fnd_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'findings_eyebrow', 'type' => 'text', 'default_value' => 'Key Findings' ),
                array( 'key' => 'field_bpu_fnd_title', 'label' => 'Section Title', 'name' => 'findings_title', 'type' => 'text', 'default_value' => 'The data tells a clear story' ),
                array( 'key' => 'field_bpu_fnd_body', 'label' => 'Section Body', 'name' => 'findings_body', 'type' => 'textarea' ),
                array(
                    'key' => 'field_bpu_fnd_cards',
                    'label' => 'Findings Cards',
                    'name' => 'findings_cards',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array( 'key' => 'field_bpu_fc_num', 'label' => 'Statistic / Number', 'name' => 'number', 'type' => 'text' ),
                        array( 'key' => 'field_bpu_fc_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
                        array( 'key' => 'field_bpu_fc_body', 'label' => 'Body Text', 'name' => 'body', 'type' => 'textarea' ),
                        array( 'key' => 'field_bpu_fc_acc', 'label' => 'Highlight (Red)', 'name' => 'is_accent', 'type' => 'true_false', 'ui' => 1 ),
                    ),
                ),

                // --- INSIGHTS TAB ---
                array( 'key' => 'field_bpu_tab_insights', 'label' => 'Insights Section', 'type' => 'tab', 'placement' => 'top' ),
                array( 'key' => 'field_bpu_ins_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'insights_eyebrow', 'type' => 'text', 'default_value' => 'Report Insights' ),
                array( 'key' => 'field_bpu_ins_title', 'label' => 'Section Title', 'name' => 'insights_title', 'type' => 'text', 'default_value' => 'What this report reveals' ),
                array( 'key' => 'field_bpu_ins_content', 'label' => 'Narrative Content', 'name' => 'insights_content', 'type' => 'wysiwyg', 'media_upload' => 0 ),
                array(
                    'key' => 'field_bpu_ins_themes',
                    'label' => 'Insight Themes',
                    'name' => 'insights_themes',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'sub_fields' => array(
                        array( 'key' => 'field_bpu_it_icon', 'label' => 'Icon / Emoji', 'name' => 'icon', 'type' => 'text' ),
                        array( 'key' => 'field_bpu_it_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
                        array( 'key' => 'field_bpu_it_desc', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea' ),
                    ),
                ),

                // --- POLICY TAB ---
                array( 'key' => 'field_bpu_tab_policy', 'label' => 'Policy Section', 'type' => 'tab', 'placement' => 'top' ),
                array( 'key' => 'field_bpu_pol_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'policy_eyebrow', 'type' => 'text', 'default_value' => 'For Policymakers & Employers' ),
                array( 'key' => 'field_bpu_pol_title', 'label' => 'Section Title', 'name' => 'policy_title', 'type' => 'text', 'default_value' => 'A call for coordinated action' ),
                array( 'key' => 'field_bpu_pol_body', 'label' => 'Section Body', 'name' => 'policy_body', 'type' => 'textarea' ),
                array(
                    'key' => 'field_bpu_pol_cols',
                    'label' => 'Policy Columns',
                    'name' => 'policy_columns',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array( 'key' => 'field_bpu_pc_title', 'label' => 'Column Title', 'name' => 'title', 'type' => 'text' ),
                        array( 'key' => 'field_bpu_pc_list', 'label' => 'Bullet List', 'name' => 'list_items', 'type' => 'wysiwyg', 'media_upload' => 0, 'instructions' => 'Use bullet points here.' ),
                    ),
                ),

                // --- DOWNLOAD TAB ---
                array( 'key' => 'field_bpu_tab_dl', 'label' => 'Download Section', 'type' => 'tab', 'placement' => 'top' ),
                array( 'key' => 'field_bpu_dl_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'download_eyebrow', 'type' => 'text', 'default_value' => '2026 Full Report' ),
                array( 'key' => 'field_bpu_dl_title', 'label' => 'Section Title', 'name' => 'download_title', 'type' => 'text', 'default_value' => 'Read the full findings.<br>Drive real change.' ),
                array( 'key' => 'field_bpu_dl_body', 'label' => 'Section Body', 'name' => 'download_body', 'type' => 'textarea' ),
                array( 'key' => 'field_bpu_dl_link', 'label' => 'Report PDF Link', 'name' => 'download_pdf_link', 'type' => 'url' ),
                array( 'key' => 'field_bpu_dl_btn_txt', 'label' => 'Download Button Text', 'name' => 'download_btn_text', 'type' => 'text', 'default_value' => 'Download PDF' ),
                array( 'key' => 'field_bpu_dl_cont_lnk', 'label' => 'Contact / Policy Link', 'name' => 'contact_link', 'type' => 'text', 'default_value' => 'mailto:info@blackprofessionals.uk' ),
                array( 'key' => 'field_bpu_dl_cont_txt', 'label' => 'Contact Button Text', 'name' => 'contact_btn_text', 'type' => 'text', 'default_value' => 'Engage with Us on Policy →' ),

            ),
            'location' => array(
                array(
                    array( 'param' => 'page_template', 'operator' => '==', 'value' => 'bpu-report.php' ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => array( 'the_content' ),
        ));

        // ==========================================
        // 2. ACF FIELDS FOR SAFE SPACE
        // ==========================================
        acf_add_local_field_group(array(
            'key' => 'group_bpu_safespace_settings',
            'title' => 'BPU Safe Space Settings',
            'fields' => array(
                // --- HERO TAB ---
                array( 'key' => 'field_ss_tab_hero', 'label' => 'Hero Section', 'type' => 'tab', 'placement' => 'top' ),
                array( 'key' => 'field_ss_hero_title', 'label' => 'Main Title', 'name' => 'ss_hero_title', 'type' => 'text', 'default_value' => 'Safe Space' ),
                array( 'key' => 'field_ss_hero_powered', 'label' => 'Powered By Text (Small Title)', 'name' => 'ss_hero_powered', 'type' => 'text', 'default_value' => 'Powered by Black Professionals UK' ),
                array( 'key' => 'field_ss_hero_sub', 'label' => 'Subtitle', 'name' => 'ss_hero_subtitle', 'type' => 'textarea', 'default_value' => 'A confidential support platform for Black professionals navigating stress, burnout, identity, and emotional wellbeing.' ),
                array( 'key' => 'field_ss_hero_desc', 'label' => 'Description', 'name' => 'ss_hero_description', 'type' => 'textarea', 'default_value' => 'You do not need to be in crisis to reach out. Sometimes, the right conversation is the first step. This is a space built for you.' ),
                array( 'key' => 'field_ss_hero_img', 'label' => 'Hero Image', 'name' => 'ss_hero_image', 'type' => 'image', 'return_format' => 'url', 'instructions' => 'Add an image to display next to the hero text.' ),

                // --- FEATURES TAB ---
                array( 'key' => 'field_ss_tab_features', 'label' => 'Offers & Boundaries', 'type' => 'tab', 'placement' => 'top' ),
                array( 'key' => 'field_ss_feat_img', 'label' => 'Feature Banner Image', 'name' => 'ss_features_image', 'type' => 'image', 'return_format' => 'url', 'instructions' => 'Add an image to display beautifully below the features section.' ),
                array(
                    'key' => 'field_ss_offers',
                    'label' => 'What Safe Space Offers',
                    'name' => 'ss_offers_list',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array( 'key' => 'field_ss_of_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
                        array( 'key' => 'field_ss_of_desc', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea' ),
                    ),
                ),
                array(
                    'key' => 'field_ss_nots',
                    'label' => 'What Safe Space Is Not',
                    'name' => 'ss_nots_list',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'sub_fields' => array(
                        array( 'key' => 'field_ss_not_text', 'label' => 'Boundary Text', 'name' => 'text', 'type' => 'text' ),
                    ),
                ),

                // --- FORM TAB ---
                array( 'key' => 'field_ss_tab_form', 'label' => 'Contact Form', 'type' => 'tab', 'placement' => 'top' ),
                array( 'key' => 'field_ss_form_img', 'label' => 'Side Image', 'name' => 'ss_form_image', 'type' => 'image', 'return_format' => 'url', 'instructions' => 'Add an image to display alongside the contact form.' ),
                array( 'key' => 'field_ss_form_title', 'label' => 'Form Title', 'name' => 'ss_form_title', 'type' => 'text', 'default_value' => 'Get in touch' ),
                array( 'key' => 'field_ss_form_desc', 'label' => 'Form Intro', 'name' => 'ss_form_desc', 'type' => 'textarea', 'default_value' => 'Tell us a little about what you\'re experiencing. There\'s no right or wrong way to answer. A few words is enough. We\'ll come back to you with guidance and next steps.' ),
                array( 'key' => 'field_ss_form_shortcode', 'label' => 'Form Shortcode', 'name' => 'ss_form_shortcode', 'type' => 'text', 'instructions' => 'Paste a shortcode here (e.g. from WPForms or Contact Form 7). If left blank, a static HTML form layout will be displayed (which requires custom backend handling).' ),

            ),
            'location' => array(
                array(
                    array( 'param' => 'page_template', 'operator' => '==', 'value' => 'bpu-safespace.php' ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => array( 'the_content' ),
        ));
    }


    // ==========================================
    // 3. RENDER REPORT TEMPLATE
    // ==========================================
    public function render_report_template() {
        get_header();
        ?>
        <style>
          :root {
            --navy: #001b69;
            --navy-light: rgba(0, 27, 105, 0.05);
            --red: #cc0000;
            --red-light: rgba(204, 0, 0, 0.05);
            --bg-main: #ffffff;
            --bg-alt: #f4f6f9;
            --text-main: #1a1a1a;
            --text-muted: #555555;
            --border: rgba(0, 27, 105, 0.15);
            --card-bg: #ffffff;
          }

          html { scroll-behavior: smooth; }
          body.page-template-bpu-report { overflow-x: hidden; }

          .bpu-report-wrapper {
            background: var(--bg-main);
            color: var(--text-main);
            font-family: inherit;
            line-height: 1.7;
            width: 100vw;
            position: relative;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
          }
          .bpu-report-wrapper *, .bpu-report-wrapper *::before, .bpu-report-wrapper *::after { box-sizing: border-box; }

          /* Tabs */
          .report-tabs {
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: center; gap: 2.5rem;
            padding: 1rem; background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(14px); border-bottom: 1px solid var(--border); box-shadow: 0 4px 20px rgba(0,0,0,0.03);
          }
          .report-tabs a {
            color: var(--text-muted); text-decoration: none; font-size: 0.9rem; font-weight: 600;
            letter-spacing: 0.05em; text-transform: uppercase; transition: color .2s;
          }
          .report-tabs a:hover { color: var(--navy); }
          .report-tabs .tab-cta { background: var(--red); color: #fff !important; padding: 0.5rem 1.2rem; border-radius: 4px; }
          .report-tabs .tab-cta:hover { background: var(--navy); }

          /* Hero */
          .hero { position: relative; display: flex; align-items: center; padding: 6rem 4rem 5rem; overflow: hidden; background: var(--bg-alt); }
          .hero-grid { position: absolute; inset: 0; background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px); background-size: 50px 50px; opacity: 0.5; }
          .data-node { position: absolute; background: var(--card-bg); border: 1px solid var(--border); border-radius: 6px; padding: 0.6rem 0.9rem; box-shadow: 0 4px 15px rgba(0,27,105,0.05); animation: float 6s ease-in-out infinite; pointer-events: none; z-index: 2; }
          .data-node .dn-num { font-size: 1.4rem; font-weight: 900; color: var(--navy); line-height: 1; }
          .data-node .dn-label { font-size: 0.65rem; color: var(--text-muted); letter-spacing: 0.05em; margin-top: 0.15rem; }
          .data-node.accent { border-color: rgba(204,0,0,0.3); background: var(--red-light); }
          .data-node.accent .dn-num { color: var(--red); }
          .dn1  { top: 15%; right: 6%; animation-delay: 0s; } .dn2  { top: 28%; right: 22%; animation-delay: 1.2s; }
          .dn3  { top: 55%; right: 8%; animation-delay: 2.5s; } .dn4  { top: 70%; right: 30%; animation-delay: 0.8s; }
          .dn5  { top: 18%; right: 40%; animation-delay: 1.8s; } .dn6  { bottom: 14%; right: 42%; animation-delay: 3s; }
          .dn7  { top: 12%; left: 38%; animation-delay: 2s; } .dn8  { bottom: 22%; right: 18%; animation-delay: 1.5s; }
          .hero-lines { position: absolute; inset: 0; pointer-events: none; opacity: 0.4; }
          @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }

          .hero-content { position: relative; z-index: 3; max-width: 620px; }
          .hero-eyebrow { display: inline-flex; align-items: center; gap: 0.6rem; background: var(--navy-light); border: 1px solid var(--border); color: var(--navy); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; padding: 0.4rem 1rem; border-radius: 4px; margin-bottom: 2.2rem; }
          .hero-eyebrow::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--red); display: inline-block; }
          .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.5rem); font-weight: 900; line-height: 1.06; margin-bottom: 0.4rem; color: var(--text-main); }
          .hero h1 .line2 { color: var(--navy); display: block; }
          .hero h1 .line3 { font-size: 0.45em; color: var(--text-muted); font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; display: block; margin-top: 0.5rem; }
          .hero-sub { font-size: 1.05rem; color: var(--text-muted); max-width: 500px; margin: 1.5rem 0 2.5rem; }
          .hero-actions { display: flex; gap: 1rem; flex-wrap: wrap; }

          .btn-primary { background: var(--navy); color: #fff; padding: 0.9rem 2.2rem; font-weight: 700; font-size: 0.95rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; transition: background .2s, transform .15s; }
          .btn-primary:hover { background: var(--red); color: #fff; transform: translateY(-2px); }
          .btn-ghost { border: 2px solid var(--border); color: var(--navy); padding: 0.9rem 2.2rem; font-weight: 600; font-size: 0.95rem; border-radius: 4px; text-decoration: none; display: inline-block; transition: border-color .2s, color .2s; }
          .btn-ghost:hover { border-color: var(--red); color: var(--red); }

          /* Main Structure */
          .bpu-report-wrapper main { max-width: 1200px; margin: 0 auto; padding: 0 4rem; }
          .section { padding: 6rem 0; }
          .section-eyebrow { color: var(--red); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.16em; margin-bottom: 0.8rem; }
          .section-title { font-size: clamp(1.8rem, 3vw, 2.6rem); font-weight: 900; line-height: 1.15; margin-bottom: 1.2rem; color: var(--navy); }
          .section-body { color: var(--text-muted); font-size: 1.05rem; max-width: 680px; margin-bottom: 1.2rem; }
          .divider { height: 1px; background: linear-gradient(90deg, var(--border), transparent); margin: 0; }

          /* Context Grid */
          .context-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 2.5rem; }
          .context-block h4 { font-size: 1.15rem; font-weight: 700; color: var(--navy); margin-bottom: 0.6rem; }
          .context-block p { color: var(--text-muted); font-size: 0.95rem; }

          /* Findings Grid */
          .findings-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 3rem; }
          .finding-card { background: var(--card-bg); border: 1px solid var(--border); padding: 1.8rem; border-radius: 8px; transition: border-color .3s, transform .3s, box-shadow .3s; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
          .finding-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--navy); transform: scaleX(0); transform-origin: left; transition: transform .4s; }
          .finding-card:hover { border-color: var(--navy); transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,27,105,0.08); }
          .finding-card:hover::after { transform: scaleX(1); }
          .finding-card.accent::after { background: var(--red); }
          .finding-card.accent:hover { border-color: var(--red); }
          .finding-num { font-size: 2.6rem; font-weight: 900; color: var(--navy); line-height: 1; margin-bottom: 0.5rem; }
          .finding-card.accent .finding-num { color: var(--red); }
          .finding-title { font-weight: 700; font-size: 0.95rem; margin-bottom: 0.5rem; color: var(--text-main); }
          .finding-body { color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; }

          /* Narrative */
          .narrative-inner { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 4rem; align-items: start; }
          .pull-quote { font-size: 1.4rem; font-style: italic; font-weight: 600; border-left: 4px solid var(--red); padding: 1.2rem 1.5rem; background: var(--red-light); border-radius: 0 6px 6px 0; color: var(--navy); line-height: 1.5; margin: 2rem 0; }
          .themes-stack { display: flex; flex-direction: column; gap: 1.2rem; }
          .theme-item { background: var(--card-bg); border: 1px solid var(--border); border-radius: 6px; padding: 1.3rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem; transition: border-color .2s; }
          .theme-item:hover { border-color: var(--navy); }
          .theme-icon { width: 40px; height: 40px; border-radius: 6px; background: var(--navy-light); color: var(--navy); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.2rem; font-weight: bold; }
          .theme-item h5 { font-size: 0.95rem; font-weight: 700; margin-bottom: 0.3rem; color: var(--text-main); }
          .theme-item p { font-size: 0.85rem; color: var(--text-muted); }

          /* Policy */
          .policy-section { background: var(--bg-alt); }
          .policy-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-top: 2rem; }
          .policy-card { padding: 2rem; border-radius: 8px; background: var(--card-bg); border: 1px solid var(--border); box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
          .policy-card h4 { color: var(--red); font-size: 0.85rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 1.2rem; }
          .policy-card ul { list-style: none; padding: 0; margin: 0; }
          .policy-card li { font-size: 0.9rem; color: var(--text-muted); padding: 0.6rem 0; border-bottom: 1px solid var(--border); display: flex; align-items: flex-start; gap: 0.6rem; }
          .policy-card li:last-child { border-bottom: none; }
          .policy-card li::before { content: '→'; color: var(--navy); font-weight: bold; flex-shrink: 0; }

          /* Download */
          .download-section { padding: 7rem 0 6rem; text-align: center; }
          .download-box { max-width: 740px; margin: 0 auto; background: var(--navy); color: #fff; border-radius: 14px; padding: 4rem 3rem; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,27,105,0.2); }
          .download-box .section-eyebrow { color: var(--red); }
          .download-box h2 { font-size: clamp(1.8rem, 3vw, 2.5rem); font-weight: 900; margin-bottom: 1rem; color: #fff; }
          .download-box p { color: rgba(255,255,255,0.8); font-size: 1rem; max-width: 500px; margin: 0 auto 2.5rem; }
          .download-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
          .btn-download { background: var(--red); color: #fff; padding: 1rem 2.5rem; font-weight: 700; font-size: 1rem; border: none; border-radius: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.6rem; transition: background .2s, transform .15s; }
          .btn-download:hover { background: #fff; color: var(--red); transform: translateY(-2px); }
          .btn-policy { border: 2px solid rgba(255,255,255,0.3); color: #fff; padding: 1rem 2.5rem; font-weight: 600; font-size: 1rem; border-radius: 4px; text-decoration: none; display: inline-block; transition: border-color .2s, background .2s; cursor: pointer; background: transparent; font-family: inherit; }
          .btn-policy:hover { border-color: #fff; background: rgba(255,255,255,0.1); color: #fff; }

          /* Engage Modal */
          .engage-modal-overlay { display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 2rem; }
          .engage-modal-overlay.is-open { display: flex; }
          .engage-modal { background: #fff; border-radius: 12px; width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 60px rgba(0,0,0,0.25); position: relative; animation: modal-in .25s ease; }
          @keyframes modal-in { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
          .engage-modal-header { background: var(--navy); color: #fff; padding: 2rem 2rem 1.5rem; border-radius: 12px 12px 0 0; }
          .engage-modal-header .eyebrow { color: var(--red); font-size: 0.7rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 0.5rem; }
          .engage-modal-header h3 { font-size: 1.5rem; font-weight: 900; margin: 0; color: #fff; }
          .engage-modal-header p { color: rgba(255,255,255,0.75); font-size: 0.9rem; margin: 0.5rem 0 0; }
          .engage-modal-close { position: absolute; top: 1.2rem; right: 1.2rem; background: rgba(255,255,255,0.15); border: none; color: #fff; width: 32px; height: 32px; border-radius: 50%; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background .2s; }
          .engage-modal-close:hover { background: rgba(255,255,255,0.3); }
          .engage-modal-body { padding: 2rem; }
          .em-group { margin-bottom: 1.4rem; }
          .em-group label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--navy); margin-bottom: 0.4rem; }
          .em-group input, .em-group select, .em-group textarea { width: 100%; padding: 0.75rem 1rem; border: 2px solid #e8ecf4; border-radius: 6px; font-size: 0.95rem; font-family: inherit; transition: border-color .2s; background: #fafbfd; }
          .em-group input:focus, .em-group select:focus, .em-group textarea:focus { outline: none; border-color: var(--navy); background: #fff; }
          .em-group textarea { resize: vertical; min-height: 100px; }
          .em-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
          .em-submit { width: 100%; background: var(--navy); color: #fff; border: none; padding: 1rem; font-size: 1rem; font-weight: 700; border-radius: 6px; cursor: pointer; transition: background .2s; font-family: inherit; margin-top: 0.5rem; }
          .em-submit:hover { background: var(--red); }
          .em-disclaimer { font-size: 0.78rem; color: var(--text-muted); margin-top: 1.2rem; line-height: 1.5; padding: 0.8rem; background: var(--bg-alt); border-radius: 6px; }
          .em-success { text-align: center; padding: 2.5rem 2rem; display: none; }
          .em-success .check { width: 56px; height: 56px; background: #e6f4ee; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 1.2rem; }
          .em-success h4 { font-size: 1.3rem; font-weight: 800; color: var(--navy); margin-bottom: 0.5rem; }
          .em-success p { color: var(--text-muted); font-size: 0.95rem; }

          @media (max-width: 900px) {
            .report-tabs { display: none; }
            .hero { padding: 4rem 1.5rem; }
            .data-node { display: none; }
            .bpu-report-wrapper main { padding: 0 1.5rem; }
            .context-grid, .findings-grid, .narrative-inner, .policy-grid { grid-template-columns: 1fr; }
            .em-row { grid-template-columns: 1fr; }
          }
        </style>

        <div class="bpu-report-wrapper">
            <!-- STICKY TABS -->
            <div class="report-tabs">
                <a href="#overview">Overview</a>
                <a href="#context">Context</a>
                <a href="#findings">Findings</a>
                <a href="#insights">Insights</a>
                <a href="#policy">Action Plan</a>
                <a href="#download" class="tab-cta">Download</a>
            </div>

            <!-- HERO SECTION -->
            <section class="hero" id="overview">
              <div class="hero-grid"></div>
              <?php if( function_exists('have_rows') && have_rows('hero_data_nodes') ): ?>
                  <?php $i = 1; while( have_rows('hero_data_nodes') ): the_row(); ?>
                      <div class="data-node dn<?php echo esc_attr($i); ?> <?php echo get_sub_field('is_accent') ? 'accent' : ''; ?>">
                        <div class="dn-num"><?php the_sub_field('number'); ?></div>
                        <div class="dn-label"><?php the_sub_field('label'); ?></div>
                      </div>
                  <?php $i++; endwhile; ?>
              <?php else: ?>
                  <div class="data-node dn1"><div class="dn-num">73.1%</div><div class="dn-label">Hold Master's degree</div></div>
                  <div class="data-node dn2 accent"><div class="dn-num">47.5%</div><div class="dn-label">Experienced racism</div></div>
                  <div class="data-node dn3"><div class="dn-num">10.3%</div><div class="dn-label">Ethnicity pay gap</div></div>
                  <div class="data-node dn4 accent"><div class="dn-num">12.9%</div><div class="dn-label">Minority ethnic — Scotland</div></div>
                  <div class="data-node dn5"><div class="dn-num">58%</div><div class="dn-label">First-gen immigrants</div></div>
                  <div class="data-node dn6 accent"><div class="dn-num">11.7pp</div><div class="dn-label">Employment rate gap</div></div>
                  <div class="data-node dn7"><div class="dn-num">28%</div><div class="dn-label">Black women in desired roles</div></div>
                  <div class="data-node dn8 accent"><div class="dn-num">16.1%</div><div class="dn-label">Children experienced racism</div></div>
              <?php endif; ?>

              <svg class="hero-lines" viewBox="0 0 1400 900" preserveAspectRatio="none">
                <line x1="900" y1="130" x2="1050" y2="250" stroke="var(--navy)" stroke-width="1"/>
                <line x1="1050" y1="250" x2="880" y2="490" stroke="var(--navy)" stroke-width="1"/>
                <line x1="580" y1="110" x2="900" y2="130" stroke="var(--red)" stroke-width="1"/>
                <circle cx="900" cy="130" r="4" fill="var(--navy)"/>
                <circle cx="1050" cy="250" r="4" fill="var(--navy)"/>
                <circle cx="880" cy="490" r="4" fill="var(--navy)"/>
                <circle cx="580" cy="110" r="4" fill="var(--red)"/>
              </svg>

              <div class="hero-content">
                <div class="hero-eyebrow"><?php echo esc_html( function_exists('get_field') && get_field('hero_eyebrow') ? get_field('hero_eyebrow') : 'BPU Scotland · 2026 Report' ); ?></div>
                <h1>
                  <?php echo wp_kses_post( function_exists('get_field') && get_field('hero_heading_1') ? get_field('hero_heading_1') : 'Harnessing Talent' ); ?>
                  <span class="line2"><?php echo wp_kses_post( function_exists('get_field') && get_field('hero_heading_2') ? get_field('hero_heading_2') : 'Unlocking Scotland\'s<br>Competitive Edge' ); ?></span>
                  <span class="line3"><?php echo esc_html( function_exists('get_field') && get_field('hero_heading_3') ? get_field('hero_heading_3') : 'Black Professionals UK · Big Survey' ); ?></span>
                </h1>
                <p class="hero-sub"><?php echo wp_kses_post( function_exists('get_field') && get_field('hero_subtext') ? get_field('hero_subtext') : 'A landmark evidence-based report on the professional realities...' ); ?></p>
                <div class="hero-actions">
                  <a href="#download" class="btn-primary">↓ Download the Report</a>
                  <a href="#findings" class="btn-ghost">Explore the Findings</a>
                </div>
              </div>
            </section>

            <main>
              <!-- CONTEXT -->
              <section class="section" id="context">
                <div class="section-eyebrow"><?php echo esc_html( function_exists('get_field') && get_field('context_eyebrow') ? get_field('context_eyebrow') : 'Context & Background' ); ?></div>
                <h2 class="section-title"><?php echo wp_kses_post( function_exists('get_field') && get_field('context_title') ? get_field('context_title') : 'Scotland has a talent problem.<br>The talent isn\'t the problem.' ); ?></h2>
                <p class="section-body"><?php echo wp_kses_post( function_exists('get_field') && get_field('context_body') ? get_field('context_body') : 'Scotland has set its sights on becoming a Fair Work Nation...' ); ?></p>

                <div class="context-grid">
                  <?php if( function_exists('have_rows') && have_rows('context_blocks') ): ?>
                      <?php while( have_rows('context_blocks') ): the_row(); ?>
                          <div class="context-block">
                            <h4><?php the_sub_field('title'); ?></h4>
                            <p><?php the_sub_field('description'); ?></p>
                          </div>
                      <?php endwhile; ?>
                  <?php else: ?>
                      <div class="context-block"><h4>A changing demographic reality</h4><p>Scotland's 2022 Census revealed that 12.9%...</p></div>
                  <?php endif; ?>
                </div>
              </section>

              <div class="divider"></div>

              <!-- FINDINGS -->
              <section class="section" id="findings">
                <div class="section-eyebrow"><?php echo esc_html( function_exists('get_field') && get_field('findings_eyebrow') ? get_field('findings_eyebrow') : 'Key Findings' ); ?></div>
                <h2 class="section-title"><?php echo wp_kses_post( function_exists('get_field') && get_field('findings_title') ? get_field('findings_title') : 'The data tells a clear story' ); ?></h2>
                <p class="section-body"><?php echo wp_kses_post( function_exists('get_field') && get_field('findings_body') ? get_field('findings_body') : 'Our members are among the most qualified professionals in Scotland...' ); ?></p>

                <div class="findings-grid">
                  <?php if( function_exists('have_rows') && have_rows('findings_cards') ): ?>
                      <?php while( have_rows('findings_cards') ): the_row(); ?>
                          <div class="finding-card <?php echo get_sub_field('is_accent') ? 'accent' : ''; ?>">
                            <div class="finding-num"><?php the_sub_field('number'); ?></div>
                            <div class="finding-title"><?php the_sub_field('title'); ?></div>
                            <div class="finding-body"><?php the_sub_field('body'); ?></div>
                          </div>
                      <?php endwhile; ?>
                  <?php else: ?>
                      <div class="finding-card"><div class="finding-num">73.1%</div><div class="finding-title">Highly Educated</div><div class="finding-body">Over 73% hold a Master's degree or higher.</div></div>
                  <?php endif; ?>
                </div>
              </section>

              <div class="divider"></div>

              <!-- INSIGHTS -->
              <section class="section" id="insights">
                <div class="narrative-inner">
                  <div class="narrative-body">
                    <div class="section-eyebrow"><?php echo esc_html( function_exists('get_field') && get_field('insights_eyebrow') ? get_field('insights_eyebrow') : 'Report Insights' ); ?></div>
                    <h2 class="section-title"><?php echo wp_kses_post( function_exists('get_field') && get_field('insights_title') ? get_field('insights_title') : 'What this report reveals' ); ?></h2>
                    <?php if( function_exists('get_field') && get_field('insights_content') ): ?>
                        <?php echo wp_kses_post( get_field('insights_content') ); ?>
                    <?php endif; ?>
                  </div>

                  <div class="themes-stack">
                    <?php if( function_exists('have_rows') && have_rows('insights_themes') ): ?>
                        <?php while( have_rows('insights_themes') ): the_row(); ?>
                            <div class="theme-item">
                              <div class="theme-icon"><?php the_sub_field('icon'); ?></div>
                              <div>
                                <h5><?php the_sub_field('title'); ?></h5>
                                <p><?php the_sub_field('description'); ?></p>
                              </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                  </div>
                </div>
              </section>
            </main>

            <!-- POLICY -->
            <section class="section policy-section" id="policy">
              <main>
                <div class="section-eyebrow"><?php echo esc_html( function_exists('get_field') && get_field('policy_eyebrow') ? get_field('policy_eyebrow') : 'For Policymakers & Employers' ); ?></div>
                <h2 class="section-title"><?php echo wp_kses_post( function_exists('get_field') && get_field('policy_title') ? get_field('policy_title') : 'A call for coordinated action' ); ?></h2>
                <p class="section-body"><?php echo wp_kses_post( function_exists('get_field') && get_field('policy_body') ? get_field('policy_body') : 'These findings are an invitation...' ); ?></p>

                <div class="policy-grid">
                  <?php if( function_exists('have_rows') && have_rows('policy_columns') ): ?>
                      <?php while( have_rows('policy_columns') ): the_row(); ?>
                          <div class="policy-card">
                            <h4><?php the_sub_field('title'); ?></h4>
                            <?php
                            $list_items = get_sub_field('list_items');
                            if( !empty($list_items) ) echo wp_kses_post( $list_items );
                            ?>
                          </div>
                      <?php endwhile; ?>
                  <?php endif; ?>
                </div>
              </main>
            </section>

            <!-- DOWNLOAD -->
            <section class="download-section" id="download">
              <main>
                <div class="download-box">
                  <div class="section-eyebrow" style="margin-bottom: 1.2rem;"><?php echo esc_html( function_exists('get_field') && get_field('download_eyebrow') ? get_field('download_eyebrow') : '2026 Full Report' ); ?></div>
                  <h2><?php echo wp_kses_post( function_exists('get_field') && get_field('download_title') ? get_field('download_title') : 'Read the full findings.<br>Drive real change.' ); ?></h2>
                  <p><?php echo wp_kses_post( function_exists('get_field') && get_field('download_body') ? get_field('download_body') : 'Download the complete BPU Harnessing Talent report...' ); ?></p>

                  <div class="download-actions">
                    <a href="<?php echo esc_url( function_exists('get_field') && get_field('download_pdf_link') ? get_field('download_pdf_link') : '#' ); ?>" target="_blank" class="btn-download">
                      <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16"/></svg>
                      <?php echo esc_html( function_exists('get_field') && get_field('download_btn_text') ? get_field('download_btn_text') : 'Download PDF' ); ?>
                    </a>
                    <button type="button" class="btn-policy" id="bpu-engage-btn">
                      <?php echo esc_html( function_exists('get_field') && get_field('contact_btn_text') ? get_field('contact_btn_text') : 'Engage with Us on Policy →' ); ?>
                    </button>
                  </div>
                </div>
              </main>
            </section>

            <!-- ENGAGE WITH US MODAL -->
            <div class="engage-modal-overlay" id="bpu-engage-modal" role="dialog" aria-modal="true" aria-labelledby="engage-modal-title">
              <div class="engage-modal">
                <div class="engage-modal-header">
                  <div class="eyebrow">Policy Engagement</div>
                  <h3 id="engage-modal-title">Engage with BPU on Policy</h3>
                  <p>Share your interest or enquiry and a member of the BPU team will be in touch.</p>
                  <button class="engage-modal-close" id="bpu-engage-close" aria-label="Close">&times;</button>
                </div>

                <div class="engage-modal-body">
                  <?php echo do_shortcode('[contact-form-7 id="f390997" title="BPU Harnessing Talent Report"]'); ?>
                </div>
              </div>
            </div>

        </div>

        <script>
        (function() {
          var overlay  = document.getElementById('bpu-engage-modal');
          var openBtn  = document.getElementById('bpu-engage-btn');
          var closeBtn = document.getElementById('bpu-engage-close');

          if (!overlay || !openBtn) return;

          function openModal() {
            overlay.classList.add('is-open');
            document.body.style.overflow = 'hidden';
            closeBtn && closeBtn.focus();
          }
          function closeModal() {
            overlay.classList.remove('is-open');
            document.body.style.overflow = '';
          }

          openBtn.addEventListener('click', openModal);
          closeBtn && closeBtn.addEventListener('click', closeModal);
          overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeModal();
          });
          document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
          });
        })();
        </script>
        <?php
        get_footer();
    }


    // ==========================================
    // 4. RENDER SAFE SPACE TEMPLATE
    // ==========================================
    public function render_safespace_template() {
        get_header();
        ?>
        <style>
          :root {
            --navy: #001b69;
            --navy-light: rgba(0, 27, 105, 0.05);
            --red: #cc0000;
            --red-dark: #a30000;
            --red-light: rgba(204, 0, 0, 0.05);
            --bg-main: #ffffff;
            --bg-alt: #f4f6f9;
            --text-main: #1a1a1a;
            --text-muted: #555555;
            --border: rgba(0, 27, 105, 0.15);
            --card-bg: #ffffff;
            --success: #0b7a42;
          }

          html { scroll-behavior: smooth; }
          body.page-template-bpu-safespace { overflow-x: hidden; background-color: var(--bg-main); }

          .safespace-wrapper {
            background: var(--bg-main);
            color: var(--text-main);
            font-family: inherit;
            line-height: 1.7;
            width: 100vw;
            position: relative;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
          }
          .safespace-wrapper *, .safespace-wrapper *::before, .safespace-wrapper *::after { box-sizing: border-box; }
          .safespace-wrapper main { max-width: 1100px; margin: 0 auto; padding: 0 2rem; }

          /* SS Hero */
          .ss-hero { padding: 6rem 0; background: var(--bg-alt); position: relative; }
          .ss-hero main { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 4rem; align-items: center; }
          .ss-hero-text { text-align: left; }
          .ss-hero h1 { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 900; color: var(--navy); margin-bottom: 1.5rem; line-height: 1.1; }
          .ss-hero h1 .powered-by { display: block; font-size: 0.45em; color: var(--text-muted); font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-top: 0.5rem; }
          .ss-hero .subtitle { font-size: 1.2rem; font-weight: 600; color: var(--text-main); margin-bottom: 1.5rem; line-height: 1.6; }
          .ss-hero .desc { font-size: 1.05rem; color: var(--text-muted); }
          .ss-hero-img { text-align: right; }
          .ss-hero-img img { width: 100%; max-height: 500px; object-fit: cover; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,27,105,0.1); }

          /* Features (Offers vs Not) */
          .ss-features { padding: 5rem 0; }
          .features-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: start; }
          .feature-image-banner { margin-top: 4rem; width: 100%; }
          .feature-image-banner img { width: 100%; height: 350px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); object-fit: cover; display: block; }
          .feature-column h3 { font-size: 1.6rem; font-weight: 800; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid var(--border); }
          .offers-col h3 { color: var(--navy); border-color: var(--navy); }
          .nots-col h3 { color: var(--red); border-color: var(--red); }

          .offer-item { display: flex; gap: 1rem; margin-bottom: 1.8rem; }
          .offer-icon { color: var(--success); font-size: 1.5rem; line-height: 1; flex-shrink: 0; }
          .offer-item h4 { font-size: 1.1rem; font-weight: 700; margin: 0 0 0.4rem; color: var(--navy); }
          .offer-item p { margin: 0; font-size: 0.95rem; color: var(--text-muted); }

          .not-item { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.2rem; background: var(--bg-alt); padding: 1rem 1.5rem; border-radius: 6px; border-left: 4px solid var(--red); }
          .not-icon { color: var(--red); font-weight: bold; font-size: 1.2rem; flex-shrink: 0; }
          .not-item p { margin: 0; font-weight: 600; color: var(--text-main); font-size: 1rem; }

          .emergency-notice { margin-top: 2rem; padding: 1.5rem; background: var(--red-light); color: var(--red-dark); border-radius: 6px; font-weight: 600; text-align: center; }

          /* Contact Form Area */
          .ss-form-section { padding: 6rem 0; }
          .form-layout { display: grid; grid-template-columns: 0.8fr 1.2fr; gap: 4rem; align-items: start; max-width: 1100px; margin: 0 auto; }
          .form-image { position: sticky; top: 120px; }
          .form-image img { width: 100%; aspect-ratio: 4/5; max-height: 700px; object-fit: cover; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,27,105,0.05); display: block; }
          .form-container { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 3rem; box-shadow: 0 15px 35px rgba(0,27,105,0.05); }
          .form-header { text-align: center; margin-bottom: 3rem; }
          .form-header h2 { font-size: 2.2rem; font-weight: 900; color: var(--navy); margin-bottom: 1rem; }
          .form-header p { font-size: 1.05rem; color: var(--text-muted); }

          /* Generic Form Styling (For fallback HTML) */
          .bpu-form-group { margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(0, 27, 105, 0.08); }
          .bpu-form-group:last-of-type { border-bottom: none; padding-bottom: 0; }
          .bpu-form-group label { display: block; font-weight: 700; color: var(--navy); margin: 0 0 0.2rem 0; font-size: 0.95rem; }
          .bpu-form-group .sub-label { display: block; font-size: 0.85rem; color: var(--text-muted); margin: 0 0 0.4rem 0; font-weight: normal; line-height: 1.4; }
          .bpu-form-group br { display: none; }
          .bpu-form-control { width: 100%; display: block; padding: 0.9rem 1rem; border: 2px solid var(--bg-alt); border-radius: 6px; font-size: 1rem; font-family: inherit; transition: border-color 0.2s; background: #fafafa; }
          .bpu-form-control:focus { outline: none; border-color: var(--navy); background: #fff; }
          textarea.bpu-form-control { resize: vertical; min-height: 120px; }
          .bpu-submit-btn { width: 100%; background: var(--navy); color: #fff; border: none; padding: 1.2rem; font-size: 1.1rem; font-weight: 700; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
          .bpu-submit-btn:hover { background: var(--red); }
          .form-disclaimer { margin-top: 2rem; font-size: 0.8rem; color: var(--text-muted); line-height: 1.5; padding: 1rem; background: var(--bg-alt); border-radius: 6px; }

          @media (max-width: 900px) {
            .ss-hero main { grid-template-columns: 1fr; text-align: center; }
            .ss-hero-text { text-align: center; }
            .features-grid { grid-template-columns: 1fr; gap: 3rem; }
            .form-layout { grid-template-columns: 1fr; }
            .form-image { display: none; }
            .feature-image-banner img { height: 250px; }
          }

          @media (max-width: 768px) {
            .ss-hero { padding: 4rem 0; }
            .form-container { padding: 2rem 1.5rem; }
          }
        </style>

        <div class="safespace-wrapper">

            <!-- HERO -->
            <section class="ss-hero">
                <main>
                    <div class="ss-hero-text">
                        <h1>
                            <?php echo wp_kses_post( function_exists('get_field') && get_field('ss_hero_title') ? get_field('ss_hero_title') : 'Safe Space' ); ?>
                            <span class="powered-by"><?php echo esc_html( function_exists('get_field') && get_field('ss_hero_powered') ? get_field('ss_hero_powered') : 'Powered by Black Professionals UK' ); ?></span>
                        </h1>
                        <p class="subtitle"><?php echo wp_kses_post( function_exists('get_field') && get_field('ss_hero_subtitle') ? get_field('ss_hero_subtitle') : 'A confidential support platform for Black professionals navigating stress, burnout, identity, and emotional wellbeing.' ); ?></p>
                        <p class="desc"><?php echo wp_kses_post( function_exists('get_field') && get_field('ss_hero_description') ? get_field('ss_hero_description') : 'You do not need to be in crisis to reach out. Sometimes, the right conversation is the first step. This is a space built for you.' ); ?></p>
                    </div>
                    <div class="ss-hero-img">
                        <?php
                        $hero_img = function_exists('get_field') ? get_field('ss_hero_image') : '';
                        if( empty($hero_img) ) $hero_img = 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80';
                        ?>
                        <img src="<?php echo esc_url($hero_img); ?>" alt="Safe Space Support">
                    </div>
                </main>
            </section>

            <!-- FEATURES: OFFERS VS NOT -->
            <section class="ss-features">
                <main>
                    <div class="features-grid">

                        <!-- Offers Column -->
                        <div class="feature-column offers-col">
                            <h3>What Safe Space By BPU Offers</h3>
                            <?php if( function_exists('have_rows') && have_rows('ss_offers_list') ): ?>
                                <?php while( have_rows('ss_offers_list') ): the_row(); ?>
                                    <div class="offer-item">
                                        <div class="offer-icon">✓</div>
                                        <div>
                                            <h4><?php the_sub_field('title'); ?></h4>
                                            <p><?php the_sub_field('description'); ?></p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="offer-item">
                                    <div class="offer-icon">✓</div>
                                    <div><h4>Culturally informed support</h4><p>We provide guidance that respects faith, family dynamics, identity, and lived experience.</p></div>
                                </div>
                                <div class="offer-item">
                                    <div class="offer-icon">✓</div>
                                    <div><h4>For Black professionals</h4><p>A dedicated space for Black professionals navigating work, well-being, and life transitions.</p></div>
                                </div>
                                <div class="offer-item">
                                    <div class="offer-icon">✓</div>
                                    <div><h4>Confidential and safe</h4><p>Your information is treated with care and only used to respond to your request or connect you with support.</p></div>
                                </div>
                                <div class="offer-item">
                                    <div class="offer-icon">✓</div>
                                    <div><h4>Guidance and signposting</h4><p>We do not provide therapy. Instead, we help you understand your options and connect you to appropriate support at the right time.</p></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Is Not Column -->
                        <div class="feature-column nots-col">
                            <h3>What Safe Space By BPU Is Not</h3>
                            <?php if( function_exists('have_rows') && have_rows('ss_nots_list') ): ?>
                                <?php while( have_rows('ss_nots_list') ): the_row(); ?>
                                    <div class="not-item">
                                        <div class="not-icon">✕</div>
                                        <p><?php the_sub_field('text'); ?></p>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="not-item"><div class="not-icon">✕</div><p>Not a crisis or emergency service</p></div>
                                <div class="not-item"><div class="not-icon">✕</div><p>Not clinical therapy or diagnosis</p></div>
                                <div class="not-item"><div class="not-icon">✕</div><p>Not a substitute for professional mental health care</p></div>
                            <?php endif; ?>

                            <div class="emergency-notice">
                                If you are in immediate distress, we will always direct you to urgent support services.
                            </div>
                        </div>

                    </div>

                    <!-- Feature Banner Image -->
                    <div class="feature-image-banner">
                        <?php
                        $feat_img = function_exists('get_field') ? get_field('ss_features_image') : '';
                        if( empty($feat_img) ) $feat_img = 'https://images.unsplash.com/photo-1543269865-cbf427effbad?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80';
                        ?>
                        <img src="<?php echo esc_url($feat_img); ?>" alt="Mental Health Boundaries">
                    </div>
                </main>
            </section>

            <!-- CONTACT FORM SECTION -->
            <section class="ss-form-section">
                <main>
                    <div class="form-layout">
                        <!-- Form Image Side -->
                        <div class="form-image">
                            <?php
                            $form_img = function_exists('get_field') ? get_field('ss_form_image') : '';
                            if( empty($form_img) ) $form_img = 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80';
                            ?>
                            <img src="<?php echo esc_url($form_img); ?>" alt="Contact Safe Space">
                        </div>

                        <div class="form-container">
                            <div class="form-header">
                                <h2><?php echo esc_html( function_exists('get_field') && get_field('ss_form_title') ? get_field('ss_form_title') : 'Get in touch' ); ?></h2>
                                <p><?php echo wp_kses_post( function_exists('get_field') && get_field('ss_form_desc') ? get_field('ss_form_desc') : "Tell us a little about what you're experiencing. There's no right or wrong way to answer. A few words is enough. We'll come back to you with guidance and next steps." ); ?></p>
                            </div>

                            <?php
                            $form_shortcode = function_exists('get_field') ? get_field('ss_form_shortcode') : '';

                            if( !empty($form_shortcode) ) {
                                echo do_shortcode( $form_shortcode );
                            } else {
                            ?>
                                <form action="#" method="POST" class="bpu-static-form">

                                    <div class="bpu-form-group">
                                        <label for="ss_name">Name</label>
                                        <input type="text" id="ss_name" name="ss_name" class="bpu-form-control" placeholder="First name or initials is fine" required>
                                    </div>

                                    <div class="bpu-form-group">
                                        <label for="ss_phone">Phone number (optional)</label>
                                        <input type="tel" id="ss_phone" name="ss_phone" class="bpu-form-control" placeholder="e.g. 07123 456789">
                                    </div>

                                    <div class="bpu-form-group">
                                        <label for="ss_email">Email address</label>
                                        <input type="email" id="ss_email" name="ss_email" class="bpu-form-control" placeholder="For us to respond to you" required>
                                    </div>

                                    <div class="bpu-form-group">
                                        <label for="ss_intent">What best describes what you're looking for right now?</label>
                                        <select id="ss_intent" name="ss_intent" class="bpu-form-control" required>
                                            <option value="" disabled selected>Please select...</option>
                                            <option value="General emotional support">General emotional support</option>
                                            <option value="Work-related stress or burnout">Work-related stress or burnout</option>
                                            <option value="Feeling overwhelmed or low">Feeling overwhelmed or low</option>
                                            <option value="Identity or belonging at work">Identity or belonging at work</option>
                                            <option value="Looking for information or resources">Looking for information or resources</option>
                                            <option value="Not sure. I just need to talk">Not sure. I just need to talk</option>
                                        </select>
                                    </div>

                                    <div class="bpu-form-group">
                                        <label for="ss_support_type">What kind of support would feel most helpful right now?</label>
                                        <select id="ss_support_type" name="ss_support_type" class="bpu-form-control" required>
                                            <option value="" disabled selected>Please select...</option>
                                            <option value="Someone to talk to">Someone to talk to</option>
                                            <option value="Practical resources">Practical resources</option>
                                            <option value="A referral or signposting">A referral or signposting</option>
                                            <option value="Not sure yet">Not sure yet</option>
                                        </select>
                                    </div>

                                    <div class="bpu-form-group">
                                        <label for="ss_experience">Is there anything you'd like us to know about what you're experiencing?</label>
                                        <span class="sub-label">A few words is enough, there's no right or wrong answer.</span>
                                        <textarea id="ss_experience" name="ss_experience" class="bpu-form-control" placeholder="Share as much or as little as you like..."></textarea>
                                    </div>

                                    <div class="bpu-form-group">
                                        <label for="ss_mindful">Is there anything we should be mindful of when responding to you?</label>
                                        <span class="sub-label">E.g. faith or spiritual considerations, language preferences, communication needs.</span>
                                        <textarea id="ss_mindful" name="ss_mindful" class="bpu-form-control" placeholder="Optional - Share anything that helps us respond thoughtfully"></textarea>
                                    </div>

                                    <button type="submit" class="bpu-submit-btn">Send Confidential Request</button>

                                    <div class="form-disclaimer">
                                        <strong>NOTE:</strong> This initiative provides guidance and signposting support. It is not an emergency or crisis service. All submissions are treated as confidential and are used solely to respond to your enquiry and provide appropriate support pathways. This service is not intended for clinical use, formal reporting, or record-keeping purposes.
                                    </div>

                                </form>
                            <?php } ?>

                        </div>
                    </div>
                </main>
            </section>

        </div>
        <?php
        get_footer();
    }
}

// Initialize the plugin class
new BPU_Page_Templates_Plugin();
