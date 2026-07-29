<?php
/**
 * ACF field group registration for Black Professionals Ireland.
 *
 * Mirrors the local-field-group pattern used by the BPU Custom Page
 * Templates plugin elsewhere in this org: fields are registered in code
 * (not the ACF UI) so they ship with the theme and every field has a
 * sensible default_value, so pages render reasonably before an editor
 * fills them in.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'acf/init', 'bpu_ie_register_acf_fields' );

function bpu_ie_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    /* ==========================================================
     * THEME SETTINGS (Options Page)
     * ========================================================== */
    if ( function_exists( 'acf_add_options_page' ) ) {
        acf_add_options_page( array(
            'page_title' => 'Theme Settings',
            'menu_title' => 'Theme Settings',
            'menu_slug'  => 'bpu-ie-theme-settings',
            'capability' => 'edit_theme_options',
            'icon_url'   => 'dashicons-admin-generic',
        ) );
    }

    acf_add_local_field_group( array(
        'key'    => 'group_bpu_ie_theme_settings',
        'title'  => 'Site-Wide Settings',
        'fields' => array(
            array( 'key' => 'field_bpuie_org_legal_name', 'label' => 'Legal Entity Name', 'name' => 'org_legal_name', 'type' => 'text', 'instructions' => 'Shown in the site footer and on the Imprint page.', 'default_value' => 'Black Professionals Europe e. V.' ),
            array( 'key' => 'field_bpuie_contact_email', 'label' => 'Contact Email', 'name' => 'contact_email', 'type' => 'email', 'default_value' => 'blackprofessionalseurope@gmail.com' ),
            array( 'key' => 'field_bpuie_contact_phone', 'label' => 'Contact Phone', 'name' => 'contact_phone', 'type' => 'text' ),
            array( 'key' => 'field_bpuie_contact_address', 'label' => 'Address', 'name' => 'contact_address', 'type' => 'textarea', 'rows' => 2, 'default_value' => "Am Fährweg 114\n41468 Neuss" ),
            array( 'key' => 'field_bpuie_social_facebook', 'label' => 'Facebook URL', 'name' => 'social_facebook', 'type' => 'url' ),
            array( 'key' => 'field_bpuie_social_instagram', 'label' => 'Instagram URL', 'name' => 'social_instagram', 'type' => 'url', 'default_value' => 'https://www.instagram.com/blackprofessionalseurope/' ),
            array( 'key' => 'field_bpuie_social_linkedin', 'label' => 'LinkedIn URL', 'name' => 'social_linkedin', 'type' => 'url', 'default_value' => 'https://www.linkedin.com/company/94292023' ),
            array( 'key' => 'field_bpuie_social_twitter', 'label' => 'X / Twitter URL', 'name' => 'social_twitter', 'type' => 'url' ),
            array( 'key' => 'field_bpuie_tab_forms', 'label' => 'Forms', 'type' => 'tab', 'placement' => 'top' ),
            array( 'key' => 'field_bpuie_privacy_link', 'label' => 'Privacy Statement & Consent Page', 'name' => 'privacy_policy_link', 'type' => 'url', 'instructions' => 'Linked from the membership form\'s consent checkbox. Falls back to the site\'s Privacy Policy page (Settings → Privacy) if left blank.' ),
            array( 'key' => 'field_bpuie_hcaptcha_site_key', 'label' => 'hCaptcha Site Key', 'name' => 'hcaptcha_site_key', 'type' => 'text', 'instructions' => 'Leave both hCaptcha fields blank to skip the captcha entirely.' ),
            array( 'key' => 'field_bpuie_hcaptcha_secret_key', 'label' => 'hCaptcha Secret Key', 'name' => 'hcaptcha_secret_key', 'type' => 'text' ),
        ),
        'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'bpu-ie-theme-settings' ) ) ),
    ) );

    /* ==========================================================
     * HOME
     * (content ported from the live blackprofessionals.eu homepage,
     * Europe → Ireland swapped in the org-name copy)
     * ========================================================== */
    acf_add_local_field_group( array(
        'key'    => 'group_bpu_ie_home',
        'title'  => 'Home Page Content',
        'fields' => array(
            array( 'key' => 'field_bpuie_home_tab_hero', 'label' => 'Hero', 'type' => 'tab', 'placement' => 'top' ),
            array( 'key' => 'field_bpuie_home_heading', 'label' => 'Heading', 'name' => 'home_hero_heading', 'type' => 'text', 'default_value' => 'Black Professionals Ireland' ),
            array( 'key' => 'field_bpuie_home_text', 'label' => 'Intro Text', 'name' => 'home_hero_text', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'We are the fastest-growing network of Black professionals and students across Ireland and we exist to support our members and the business community.' ),
            array( 'key' => 'field_bpuie_home_cta_text', 'label' => 'Button Text', 'name' => 'home_hero_cta_text', 'type' => 'text', 'default_value' => 'Sign Up' ),
            array( 'key' => 'field_bpuie_home_cta_link', 'label' => 'Button Link', 'name' => 'home_hero_cta_link', 'type' => 'url' ),

            array( 'key' => 'field_bpuie_home_tab_members', 'label' => 'For Our Members', 'type' => 'tab', 'placement' => 'top' ),
            array( 'key' => 'field_bpuie_members_heading', 'label' => 'Section Heading', 'name' => 'members_heading', 'type' => 'text', 'default_value' => 'For Our Members' ),
            array(
                'key' => 'field_bpuie_members_cards', 'label' => 'Member Cards', 'name' => 'members_cards', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add Card',
                'sub_fields' => array(
                    array( 'key' => 'field_bpuie_mc_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
                    array( 'key' => 'field_bpuie_mc_desc', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'rows' => 2 ),
                    array( 'key' => 'field_bpuie_mc_link', 'label' => 'Link (optional)', 'name' => 'link', 'type' => 'url' ),
                ),
                'default_value' => array(
                    array( 'title' => 'General Support for Members', 'description' => 'A safe space for our members to share their experiences, seek advice, and connect with like-minded individuals who understand the unique challenges faced by Black professionals.' ),
                    array( 'title' => 'Professional Development and Networking Events', 'description' => 'Continuous growth is essential for your career advancement. Attend our engaging workshops, seminars, and networking events to acquire new skills and expand your professional connections.' ),
                    array( 'title' => 'Job Board from DEI-Centric Organisations', 'description' => 'Find exclusive job opportunities from organisations that prioritize diversity, equity, and inclusion. Our job board connects you with employers who actively seek diverse talent.' ),
                    array( 'title' => 'CV Clinic', 'description' => 'Your CV is a critical tool in your job search. Get personalised feedback and guidance from our professionals to create a standout resume that highlights your skills and accomplishments.' ),
                    array( 'title' => 'Mentorship Program', 'description' => 'Gain valuable insights and guidance from experienced professionals through our mentorship program. Connect with mentors who are eager to support your growth and development.' ),
                    array( 'title' => 'Interview Prep', 'description' => 'Be equipped with the tools and knowledge to excel in interviews and stand out as a top candidate in the competitive job market. Our members are offered a personalised approach to cater to your specific needs and career aspirations.' ),
                ),
            ),

            array( 'key' => 'field_bpuie_home_tab_partners', 'label' => 'For Our Partners', 'type' => 'tab', 'placement' => 'top' ),
            array( 'key' => 'field_bpuie_partners_intro_heading', 'label' => 'Intro Heading', 'name' => 'partners_intro_heading', 'type' => 'text', 'default_value' => 'Partnership with Black Professionals Ireland' ),
            array( 'key' => 'field_bpuie_partners_intro_text', 'label' => 'Intro Text', 'name' => 'partners_intro_text', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'We are committed to fostering meaningful partnerships that drive diversity, equity, and inclusion in the professional setting. As our partner, you contribute to a stronger and more inclusive community and gain access to a range of exclusive benefits that contribute to your success stories.' ),
            array( 'key' => 'field_bpuie_partners_heading', 'label' => 'Section Heading', 'name' => 'partners_heading', 'type' => 'text', 'default_value' => 'For Our Partners' ),
            array(
                'key' => 'field_bpuie_partners_cards', 'label' => 'Partner Cards', 'name' => 'partners_cards', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add Card',
                'sub_fields' => array(
                    array( 'key' => 'field_bpuie_pc_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
                    array( 'key' => 'field_bpuie_pc_desc', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'rows' => 2 ),
                ),
                'default_value' => array(
                    array( 'title' => 'Access to Diverse Talent Pool', 'description' => 'Our partners gain exclusive access to a diverse talent pool through our job board, internship and skilled placement programs. Connect with exceptional Black professionals to enhance your workforce.' ),
                    array( 'title' => 'Organisation Representation', 'description' => 'Our partners are displayed and well-represented on our platform as diverse and inclusive organisations, demonstrating their unwavering commitment to diversity and social responsibility.' ),
                    array( 'title' => 'Increased Visibility and Brand Awareness', 'description' => 'Our partnership increases your brand visibility and reach through our extensive network. Gain exposure through our website, social media, and events to amplify your brand\'s reach.' ),
                    array( 'title' => 'Collaborative Events & Networking Opportunities', 'description' => 'Connect and discover business opportunities through purposeful networking with potential clients, partners, industry leaders, and talent at our exclusive events.' ),
                    array( 'title' => 'Social Impact', 'description' => 'Your partnership with Black Professionals Ireland fosters social impact and meaningful change. This showcases your organisation\'s commitment to the community, and making a positive difference.' ),
                    array( 'title' => 'Collaborative Projects & Initiatives', 'description' => 'Collaborate on projects and initiatives that address relevant challenges and drive positive change. Together, we can create a more equitable future and foster a diverse and prosperous community.' ),
                ),
            ),
            array( 'key' => 'field_bpuie_partners_cta_text', 'label' => 'Button Text', 'name' => 'partners_cta_text', 'type' => 'text', 'default_value' => 'Become a Partner' ),
            array( 'key' => 'field_bpuie_partners_cta_link', 'label' => 'Button Link', 'name' => 'partners_cta_link', 'type' => 'url' ),

            array( 'key' => 'field_bpuie_home_tab_ambassador', 'label' => 'Ambassadorship', 'type' => 'tab', 'placement' => 'top' ),
            array( 'key' => 'field_bpuie_ambassador_heading', 'label' => 'Heading', 'name' => 'ambassador_heading', 'type' => 'text', 'default_value' => 'Ambassadorship' ),
            array( 'key' => 'field_bpuie_ambassador_subheading', 'label' => 'Subheading', 'name' => 'ambassador_subheading', 'type' => 'text', 'default_value' => 'For members who are passionate about empowering others and supporting the growth of our community.' ),
            array( 'key' => 'field_bpuie_ambassador_text', 'label' => 'Text', 'name' => 'ambassador_text', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'By becoming an ambassador, you will be an integral part of our community\'s growth and success. Your dedication to empowering others will foster an environment of collective progress and shared achievements.' ),
            array( 'key' => 'field_bpuie_ambassador_cta_text', 'label' => 'Button Text', 'name' => 'ambassador_cta_text', 'type' => 'text', 'default_value' => 'Become an Ambassador' ),
            array( 'key' => 'field_bpuie_ambassador_cta_link', 'label' => 'Button Link', 'name' => 'ambassador_cta_link', 'type' => 'url' ),

            array( 'key' => 'field_bpuie_home_tab_info', 'label' => 'Info Teasers', 'type' => 'tab', 'placement' => 'top' ),
            array( 'key' => 'field_bpuie_info_members_heading', 'label' => 'Members Teaser Heading', 'name' => 'info_members_heading', 'type' => 'text', 'default_value' => 'Information for Members' ),
            array( 'key' => 'field_bpuie_info_members_text', 'label' => 'Members Teaser Text', 'name' => 'info_members_text', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Discover a community that understands your unique journey as a Black student or professional in Ireland.' ),
            array( 'key' => 'field_bpuie_info_members_link', 'label' => 'Members Teaser Link', 'name' => 'info_members_link', 'type' => 'url' ),
            array( 'key' => 'field_bpuie_info_partners_heading', 'label' => 'Partners Teaser Heading', 'name' => 'info_partners_heading', 'type' => 'text', 'default_value' => 'Information for Partners' ),
            array( 'key' => 'field_bpuie_info_partners_text', 'label' => 'Partners Teaser Text', 'name' => 'info_partners_text', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'We are committed to fostering meaningful partnerships that drive diversity, equity, and inclusion in the professional setting.' ),
            array( 'key' => 'field_bpuie_info_partners_link', 'label' => 'Partners Teaser Link', 'name' => 'info_partners_link', 'type' => 'url' ),
            array( 'key' => 'field_bpuie_home_closing_image', 'label' => 'Closing Image (optional)', 'name' => 'home_closing_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'large' ),
        ),
        'location' => array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) ),
    ) );

    /* ==========================================================
     * ABOUT
     * ========================================================== */
    acf_add_local_field_group( array(
        'key'    => 'group_bpu_ie_about',
        'title'  => 'About Page Content',
        'fields' => array(
            array( 'key' => 'field_bpuie_about_tab_intro', 'label' => 'Intro', 'type' => 'tab', 'placement' => 'top' ),
            array( 'key' => 'field_bpuie_about_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'about_hero_eyebrow', 'type' => 'text', 'default_value' => 'About Us' ),
            array( 'key' => 'field_bpuie_about_intro', 'label' => 'Intro Text', 'name' => 'about_intro_text', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Black Professionals Europe is an organisation committed to empowering and supporting Black professionals to grow their careers, helping Black students start their careers, and supporting Europe-based organisations to access Black talent.' ),

            array( 'key' => 'field_bpuie_about_tab_purpose', 'label' => 'Purpose & Vision', 'type' => 'tab', 'placement' => 'top' ),
            array( 'key' => 'field_bpuie_purpose_heading', 'label' => 'Purpose Heading', 'name' => 'purpose_heading', 'type' => 'text', 'default_value' => 'Purpose' ),
            array( 'key' => 'field_bpuie_purpose_text', 'label' => 'Purpose Text', 'name' => 'purpose_text', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Our mission is to work with corporate partners to create an inclusive, equitable, and thriving professional landscape for Black professionals, and we are here to share our vision and seek your support.' ),
            array( 'key' => 'field_bpuie_vision_heading', 'label' => 'Vision Heading', 'name' => 'vision_heading', 'type' => 'text', 'default_value' => 'Vision' ),
            array( 'key' => 'field_bpuie_vision_text', 'label' => 'Vision Text', 'name' => 'vision_text', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Our vision is a Europe where Black professionals are not only well-represented but thrive, contributing their diverse talents to drive innovation and prosperity.' ),
            array( 'key' => 'field_bpuie_about_image_1', 'label' => 'Image', 'name' => 'about_image_1', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'large' ),

            array( 'key' => 'field_bpuie_about_tab_why', 'label' => 'Why & How', 'type' => 'tab', 'placement' => 'top' ),
            array( 'key' => 'field_bpuie_why_how_heading', 'label' => 'Section Heading', 'name' => 'why_how_heading', 'type' => 'text', 'default_value' => 'Why and How?' ),
            array( 'key' => 'field_bpuie_problem_heading', 'label' => 'Problem Heading', 'name' => 'problem_heading', 'type' => 'text', 'default_value' => 'Problem:' ),
            array(
                'key' => 'field_bpuie_problem_points', 'label' => 'Problem Points', 'name' => 'problem_points', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add Point',
                'sub_fields' => array(
                    array( 'key' => 'field_bpuie_problem_point_text', 'label' => 'Point', 'name' => 'text', 'type' => 'wysiwyg', 'toolbar' => 'basic', 'media_upload' => 0 ),
                ),
                'default_value' => array(
                    array( 'text' => 'Black professionals still face numerous disparities, including limited access to opportunities, unconscious bias, and unequal representation at leadership levels.' ),
                    array( 'text' => 'This underrepresentation not only hinders diversity but also deprives organisations of innovative perspectives.' ),
                ),
            ),
            array( 'key' => 'field_bpuie_solution_heading', 'label' => 'Solution Heading', 'name' => 'solution_heading', 'type' => 'text', 'default_value' => 'Solution:' ),
            array( 'key' => 'field_bpuie_solution_text', 'label' => 'Solution Text', 'name' => 'solution_text', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'BPE addresses this problem head-on through a comprehensive range of programs, including mentorship, skills development workshops, career-enhancing networking events, recruiting events, establishing key partnerships, and more.' ),
            array( 'key' => 'field_bpuie_about_image_2', 'label' => 'Image', 'name' => 'about_image_2', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'large' ),

            array( 'key' => 'field_bpuie_about_tab_impact', 'label' => 'Impact', 'type' => 'tab', 'placement' => 'top' ),
            array( 'key' => 'field_bpuie_impact_heading', 'label' => 'Section Heading', 'name' => 'impact_heading', 'type' => 'text', 'default_value' => 'Impact: Driving Positive Change' ),
            array( 'key' => 'field_bpuie_traction_heading', 'label' => 'Traction Heading', 'name' => 'traction_heading', 'type' => 'text', 'default_value' => 'Traction:' ),
            array(
                'key' => 'field_bpuie_traction_points', 'label' => 'Traction Points', 'name' => 'traction_points', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add Point',
                'sub_fields' => array(
                    array( 'key' => 'field_bpuie_traction_point_text', 'label' => 'Point', 'name' => 'text', 'type' => 'wysiwyg', 'toolbar' => 'basic', 'media_upload' => 0, 'instructions' => 'Use the link button to link any part of the text, e.g. "Black Professionals finding opportunities".' ),
                ),
                'default_value' => array(
                    array( 'text' => 'Our journey began in Scotland when Enoch founded BPE. To date, BPE has achieved significant milestones, with 900+ members benefiting from various programs and 20+ partners who share our commitment to this vital cause, resulting in Black Professionals finding opportunities.' ),
                    array( 'text' => 'BPE held a successful launch on 29th July 2023 at the Mannheim Business School. We want to replicate what has been achieved in Scotland across mainland Europe.' ),
                ),
            ),
            array( 'key' => 'field_bpuie_about_image_3', 'label' => 'Image', 'name' => 'about_image_3', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'large' ),
        ),
        'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/template-about.php' ) ) ),
    ) );

    /* ==========================================================
     * OUR STORY
     * ========================================================== */
    acf_add_local_field_group( array(
        'key'    => 'group_bpu_ie_our_story',
        'title'  => 'Our Story Page Content',
        'fields' => array(
            array( 'key' => 'field_bpuie_story_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'story_hero_eyebrow', 'type' => 'text', 'default_value' => 'Our Story' ),
            array( 'key' => 'field_bpuie_story_heading', 'label' => 'Heading', 'name' => 'story_hero_heading', 'type' => 'text', 'default_value' => 'How BPU Ireland came to be' ),
            array( 'key' => 'field_bpuie_story_subtext', 'label' => 'Subtext', 'name' => 'story_hero_subtext', 'type' => 'textarea', 'rows' => 2 ),
            array( 'key' => 'field_bpuie_story_quote', 'label' => 'Pull Quote', 'name' => 'story_quote', 'type' => 'text' ),
            array(
                'key' => 'field_bpuie_story_timeline', 'label' => 'Timeline', 'name' => 'story_timeline', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add Milestone',
                'sub_fields' => array(
                    array( 'key' => 'field_bpuie_tl_year', 'label' => 'Year', 'name' => 'year', 'type' => 'text' ),
                    array( 'key' => 'field_bpuie_tl_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
                    array( 'key' => 'field_bpuie_tl_desc', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'rows' => 2 ),
                ),
            ),
        ),
        'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/template-our-story.php' ) ) ),
    ) );

    /* ==========================================================
     * OUR TEAM
     * ========================================================== */
    acf_add_local_field_group( array(
        'key'    => 'group_bpu_ie_our_team',
        'title'  => 'Our Team Page Content',
        'fields' => array(
            array( 'key' => 'field_bpuie_team_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'team_hero_eyebrow', 'type' => 'text', 'default_value' => 'Our Team' ),
            array( 'key' => 'field_bpuie_team_heading', 'label' => 'Heading', 'name' => 'team_hero_heading', 'type' => 'text', 'default_value' => 'Board Members' ),
            array( 'key' => 'field_bpuie_team_subtext', 'label' => 'Subtext', 'name' => 'team_hero_subtext', 'type' => 'textarea', 'rows' => 2 ),
            array(
                'key' => 'field_bpuie_team_members', 'label' => 'Team Members', 'name' => 'team_members', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add Team Member',
                'sub_fields' => array(
                    array( 'key' => 'field_bpuie_tm_photo', 'label' => 'Photo', 'name' => 'photo', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ),
                    array( 'key' => 'field_bpuie_tm_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
                    array( 'key' => 'field_bpuie_tm_role', 'label' => 'Role', 'name' => 'role', 'type' => 'text' ),
                    array( 'key' => 'field_bpuie_tm_bio', 'label' => 'Short Bio', 'name' => 'bio', 'type' => 'textarea', 'rows' => 2 ),
                ),
                'default_value' => array(
                    array( 'name' => 'Enoch Adeyemi', 'role' => 'Chairperson' ),
                    array( 'name' => 'Dorris Kirui', 'role' => 'Deputy Chairperson' ),
                    array( 'name' => 'DeLeon Rich', 'role' => 'Finance' ),
                    array( 'name' => 'Sharon Robinette', 'role' => 'Legal Affairs' ),
                    array( 'name' => 'Rukayyat Kolawole', 'role' => 'Partnerships & Cooperations' ),
                    array( 'name' => 'Sven Stromann', 'role' => 'Partnerships & Cooperations' ),
                    array( 'name' => 'Asmaa Hechenberger', 'role' => 'Press & Public Relations' ),
                    array( 'name' => 'Esther Bornefeld', 'role' => 'Operations & Programs' ),
                ),
            ),
        ),
        'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/template-our-team.php' ) ) ),
    ) );

    /* ==========================================================
     * EVENTS (page hero + per-event meta)
     * ========================================================== */
    acf_add_local_field_group( array(
        'key'    => 'group_bpu_ie_events_page',
        'title'  => 'Events Page Content',
        'fields' => array(
            array( 'key' => 'field_bpuie_events_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'events_hero_eyebrow', 'type' => 'text', 'default_value' => 'Events' ),
            array( 'key' => 'field_bpuie_events_heading', 'label' => 'Heading', 'name' => 'events_hero_heading', 'type' => 'text', 'default_value' => 'Upcoming events & workshops' ),
            array( 'key' => 'field_bpuie_events_subtext', 'label' => 'Subtext', 'name' => 'events_hero_subtext', 'type' => 'textarea', 'rows' => 2 ),
        ),
        'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/template-events.php' ) ) ),
    ) );

    acf_add_local_field_group( array(
        'key'    => 'group_bpu_ie_event_meta',
        'title'  => 'Event Details',
        'fields' => array(
            array( 'key' => 'field_bpuie_event_date', 'label' => 'Event Date', 'name' => 'event_date', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Ymd', 'required' => 1 ),
            array( 'key' => 'field_bpuie_event_time', 'label' => 'Event Time', 'name' => 'event_time', 'type' => 'text', 'placeholder' => 'e.g. 6:00pm – 8:00pm' ),
            array( 'key' => 'field_bpuie_event_location', 'label' => 'Location', 'name' => 'event_location', 'type' => 'text', 'placeholder' => 'e.g. Dublin / Online' ),
            array( 'key' => 'field_bpuie_event_link', 'label' => 'Registration Link', 'name' => 'event_registration_link', 'type' => 'url' ),
        ),
        'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'bpu_event' ) ) ),
    ) );

    /* ==========================================================
     * CONTACT
     * ========================================================== */
    acf_add_local_field_group( array(
        'key'    => 'group_bpu_ie_contact',
        'title'  => 'Contact Page Content',
        'fields' => array(
            array( 'key' => 'field_bpuie_contact_eyebrow', 'label' => 'Eyebrow Text', 'name' => 'contact_hero_eyebrow', 'type' => 'text' ),
            array( 'key' => 'field_bpuie_contact_heading', 'label' => 'Heading', 'name' => 'contact_hero_heading', 'type' => 'text', 'default_value' => 'Contact' ),
            array( 'key' => 'field_bpuie_contact_subtext', 'label' => 'Subtext', 'name' => 'contact_hero_subtext', 'type' => 'textarea', 'rows' => 2 ),
            array( 'key' => 'field_bpuie_contact_address_heading', 'label' => 'Address Section Heading', 'name' => 'contact_address_heading', 'type' => 'text', 'default_value' => 'Address' ),
            array( 'key' => 'field_bpuie_contact_image', 'label' => 'Image (optional)', 'name' => 'contact_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'large' ),
        ),
        'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/template-contact.php' ) ) ),
    ) );

    /* ==========================================================
     * MEMBERSHIP / PARTNERSHIP / AMBASSADORSHIP
     * (shared shape: hero + tier repeater + closing CTA)
     * ========================================================== */
    $engagement_pages = array(
        'membership'     => array( 'label' => 'Membership', 'template' => 'page-templates/template-membership.php', 'eyebrow' => 'Membership', 'heading' => 'Join Black Professionals Ireland' ),
        'partnership'    => array( 'label' => 'Partnership', 'template' => 'page-templates/template-partnership.php', 'eyebrow' => 'Partnership', 'heading' => 'Partner with Black Professionals Ireland' ),
        'ambassadorship' => array( 'label' => 'Ambassadorship', 'template' => 'page-templates/template-ambassadorship.php', 'eyebrow' => 'Ambassadorship', 'heading' => 'Become a Black Professionals Ireland Ambassador' ),
    );

    foreach ( $engagement_pages as $slug => $cfg ) {
        acf_add_local_field_group( array(
            'key'    => "group_bpu_ie_{$slug}",
            'title'  => "{$cfg['label']} Page Content",
            'fields' => array(
                array( 'key' => "field_bpuie_{$slug}_eyebrow", 'label' => 'Eyebrow Text', 'name' => "{$slug}_hero_eyebrow", 'type' => 'text', 'default_value' => $cfg['eyebrow'] ),
                array( 'key' => "field_bpuie_{$slug}_heading", 'label' => 'Heading', 'name' => "{$slug}_hero_heading", 'type' => 'text', 'default_value' => $cfg['heading'] ),
                array( 'key' => "field_bpuie_{$slug}_subtext", 'label' => 'Subtext', 'name' => "{$slug}_hero_subtext", 'type' => 'textarea', 'rows' => 2 ),
                array(
                    'key' => "field_bpuie_{$slug}_tiers", 'label' => 'Tiers / Options', 'name' => "{$slug}_tiers", 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add Tier',
                    'sub_fields' => array(
                        array( 'key' => "field_bpuie_{$slug}_tier_name", 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
                        array( 'key' => "field_bpuie_{$slug}_tier_price", 'label' => 'Price (optional)', 'name' => 'price', 'type' => 'text', 'placeholder' => 'e.g. €50/year, or leave blank' ),
                        array( 'key' => "field_bpuie_{$slug}_tier_featured", 'label' => 'Highlight as Recommended', 'name' => 'is_featured', 'type' => 'true_false', 'ui' => 1 ),
                        array( 'key' => "field_bpuie_{$slug}_tier_benefits", 'label' => 'Benefits', 'name' => 'benefits', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add Benefit',
                            'sub_fields' => array(
                                array( 'key' => "field_bpuie_{$slug}_benefit_text", 'label' => 'Benefit', 'name' => 'text', 'type' => 'text' ),
                            ),
                        ),
                        array( 'key' => "field_bpuie_{$slug}_tier_cta_text", 'label' => 'Button Text', 'name' => 'cta_text', 'type' => 'text', 'default_value' => 'Apply Now' ),
                        array( 'key' => "field_bpuie_{$slug}_tier_cta_link", 'label' => 'Button Link', 'name' => 'cta_link', 'type' => 'url' ),
                    ),
                ),
                array( 'key' => "field_bpuie_{$slug}_cta_heading", 'label' => 'Closing CTA Heading', 'name' => "{$slug}_cta_heading", 'type' => 'text', 'default_value' => 'Ready to take the next step?' ),
                array( 'key' => "field_bpuie_{$slug}_cta_text", 'label' => 'Closing CTA Text', 'name' => "{$slug}_cta_text", 'type' => 'textarea', 'rows' => 2 ),
                array( 'key' => "field_bpuie_{$slug}_cta_btn_text", 'label' => 'Closing CTA Button Text', 'name' => "{$slug}_cta_button_text", 'type' => 'text', 'default_value' => 'Get in Touch' ),
                array( 'key' => "field_bpuie_{$slug}_cta_btn_link", 'label' => 'Closing CTA Button Link', 'name' => "{$slug}_cta_button_link", 'type' => 'url' ),
            ),
            'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => $cfg['template'] ) ) ),
        ) );
    }

    /* ==========================================================
     * IMPRINT
     * Legal entity name, address, and contact email are read from
     * Theme Settings (org_legal_name / contact_address / contact_email)
     * so this page and the footer never fall out of sync.
     * ========================================================== */
    acf_add_local_field_group( array(
        'key'    => 'group_bpu_ie_imprint',
        'title'  => 'Imprint Page Content',
        'fields' => array(
            array( 'key' => 'field_bpuie_imprint_heading', 'label' => 'Heading', 'name' => 'imprint_heading', 'type' => 'text', 'default_value' => 'Imprint' ),
            array( 'key' => 'field_bpuie_imprint_provider_heading', 'label' => 'Provider Heading', 'name' => 'imprint_provider_heading', 'type' => 'text', 'default_value' => 'The provider of this website is:' ),
            array( 'key' => 'field_bpuie_imprint_board_heading', 'label' => 'Board Heading', 'name' => 'imprint_board_heading', 'type' => 'text', 'default_value' => 'Authorized representative board:' ),
            array(
                'key' => 'field_bpuie_imprint_board_members', 'label' => 'Board Members', 'name' => 'imprint_board_members', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add Board Member',
                'sub_fields' => array(
                    array( 'key' => 'field_bpuie_imprint_bm_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
                ),
                'default_value' => array(
                    array( 'name' => 'Enoch Olasunkanmi Adeyemi' ),
                    array( 'name' => 'Dorris Temuko Kirui' ),
                    array( 'name' => 'Rukayyat Modupeola Kolawole' ),
                    array( 'name' => 'DeLeon Andre Rich' ),
                    array( 'name' => 'Sharon Wambui Robinette' ),
                    array( 'name' => 'Sven Stromann' ),
                    array( 'name' => 'Esther Wairimu Bornefeld' ),
                    array( 'name' => 'Asmaa Hechenberger' ),
                ),
            ),
            array( 'key' => 'field_bpuie_imprint_registration_text', 'label' => 'Registration Text', 'name' => 'imprint_registration_text', 'type' => 'text', 'default_value' => 'Registered in the association register at the District Court of Neuss under VR 3245' ),
            array( 'key' => 'field_bpuie_imprint_vat_id', 'label' => 'VAT ID', 'name' => 'imprint_vat_id', 'type' => 'text', 'default_value' => 'DE (to follow)' ),
            array( 'key' => 'field_bpuie_imprint_image', 'label' => 'Image (optional)', 'name' => 'imprint_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
        ),
        'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/template-imprint.php' ) ) ),
    ) );
}
