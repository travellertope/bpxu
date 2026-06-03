<?php
/**
 * Plugin Name: BPU WP Job Manager Importer
 * Plugin URI:  https://blackprofessionals.uk
 * Description: One-shot importer — copies existing WP Job Manager listings into the BPU custom job platform. Safe to re-run; already-imported jobs are skipped.
 * Version:     1.0.0
 * Author:      BPU Tech
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─────────────────────────────────────────────────────────────────
// CONSTANTS
// ─────────────────────────────────────────────────────────────────

define( 'BPU_WJM_SOURCE_CPT', 'job_listing' );    // WP Job Manager CPT
define( 'BPU_WJM_TARGET_CPT', 'bpu_job' );        // BPU job CPT
define( 'BPU_WJM_STAMP',      '_bpu_imported_wjm_id' ); // dedup meta key

// ─────────────────────────────────────────────────────────────────
// FIELD MAPPERS
// ─────────────────────────────────────────────────────────────────

/**
 * Map WP Job Manager job_listing_type term → BPU employment_type string.
 */
function bpu_wjm_map_employment_type( array $terms ): string {
    $map = [
        'full time'    => 'Full-time',
        'full-time'    => 'Full-time',
        'part time'    => 'Part-time',
        'part-time'    => 'Part-time',
        'freelance'    => 'Freelance',
        'contract'     => 'Contract',
        'temporary'    => 'Contract',
        'temp'         => 'Contract',
        'internship'   => 'Internship',
        'intern'       => 'Internship',
        'graduate'     => 'Full-time',
        'apprenticeship' => 'Internship',
    ];
    foreach ( $terms as $term ) {
        $key = strtolower( trim( $term ) );
        if ( isset( $map[ $key ] ) ) return $map[ $key ];
    }
    return 'Full-time'; // sensible default
}

/**
 * Map WP Job Manager job_listing_category terms → BPU industry string.
 */
function bpu_wjm_map_industry( array $terms ): string {
    $map = [
        'technology'     => 'Technology',
        'tech'           => 'Technology',
        'it'             => 'Technology',
        'software'       => 'Technology',
        'engineering'    => 'Engineering',
        'finance'        => 'Finance',
        'banking'        => 'Finance',
        'accounting'     => 'Finance',
        'legal'          => 'Legal',
        'law'            => 'Legal',
        'healthcare'     => 'Healthcare',
        'health'         => 'Healthcare',
        'medical'        => 'Healthcare',
        'nhs'            => 'Healthcare',
        'marketing'      => 'Marketing',
        'digital'        => 'Marketing',
        'media'          => 'Marketing',
        'education'      => 'Education',
        'teaching'       => 'Education',
        'academia'       => 'Education',
        'hr'             => 'Other',
        'human resources'=> 'Other',
        'retail'         => 'Other',
        'logistics'      => 'Other',
    ];
    foreach ( $terms as $term ) {
        $key = strtolower( trim( $term ) );
        foreach ( $map as $needle => $industry ) {
            if ( strpos( $key, $needle ) !== false ) return $industry;
        }
    }
    return 'Other';
}

/**
 * Try to extract a salary value from a WP Job Manager salary string.
 * Returns [ min, max ] or [ null, null ] if unparseable.
 */
function bpu_wjm_parse_salary( string $raw ): array {
    // Strip currency symbols, commas, spaces
    $cleaned = preg_replace( '/[£$€,\s]/', '', $raw );

    // Range: 30000-50000 or 30k-50k
    if ( preg_match( '/^(\d+\.?\d*)[kK]?[-–](\d+\.?\d*)[kK]?$/', $cleaned, $m ) ) {
        $min = floatval( $m[1] ); $max = floatval( $m[2] );
        if ( strpos( strtolower( $raw ), 'k' ) !== false ) { $min *= 1000; $max *= 1000; }
        return [ (int) $min, (int) $max ];
    }

    // Single value: 40000 or 40k
    if ( preg_match( '/^(\d+\.?\d*)[kK]?$/', $cleaned, $m ) ) {
        $val = floatval( $m[1] );
        if ( strpos( strtolower( $raw ), 'k' ) !== false ) $val *= 1000;
        return [ (int) $val, null ];
    }

    return [ null, null ];
}

// ─────────────────────────────────────────────────────────────────
// CORE IMPORT LOGIC
// ─────────────────────────────────────────────────────────────────

/**
 * Import a single WP Job Manager post → BPU job.
 * Returns [ 'status' => 'imported'|'skipped'|'error', 'msg' => string ]
 */
function bpu_wjm_import_single( WP_Post $wjm ): array {
    $id    = $wjm->ID;
    $title = $wjm->post_title;

    // ── Dedup check ──────────────────────────────────────────────
    $existing = get_posts( [
        'post_type'      => BPU_WJM_TARGET_CPT,
        'meta_key'       => BPU_WJM_STAMP,
        'meta_value'     => $id,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'suppress_filters' => true,
    ] );
    if ( $existing ) {
        return [ 'status' => 'skipped', 'msg' => "#{$id} "{$title}" — already imported (BPU #{$existing[0]})" ];
    }

    // ── Read WP Job Manager meta ─────────────────────────────────
    $get = fn( $key ) => get_post_meta( $id, $key, true );

    $location          = (string) $get( '_job_location' );
    $company           = (string) $get( '_company_name' );
    $application       = (string) $get( '_application' );
    $links_to          = (string) $get( '_links_to' );   // Page Links To plugin redirect URL
    $expires           = (string) $get( '_job_expires' );
    $salary_raw        = (string) ( $get( '_job_salary' ) ?: $get( '_salary' ) ?: '' );
    $salary_currency   = (string) ( $get( '_job_salary_currency' ) ?: 'GBP' );
    $filled            = (bool) $get( '_filled' );
    $remote            = (bool) $get( '_remote_position' );
    $featured          = (bool) $get( '_featured' );
    $company_tagline   = (string) $get( '_company_tagline' );
    $company_website   = (string) $get( '_company_website' );
    $company_twitter   = (string) $get( '_company_twitter' );
    $company_video     = (string) $get( '_company_video' );
    $company_about     = (string) $get( 'about_company' );  // ACF wysiwyg field
    $company_logo_id   = intval( get_post_thumbnail_id( $id ) );

    // ── Taxonomies ───────────────────────────────────────────────
    $type_terms = wp_get_post_terms( $id, 'job_listing_type',     [ 'fields' => 'names' ] );
    $cat_terms  = wp_get_post_terms( $id, 'job_listing_category', [ 'fields' => 'names' ] );
    if ( is_wp_error( $type_terms ) ) $type_terms = [];
    if ( is_wp_error( $cat_terms ) )  $cat_terms  = [];

    $employment_type = bpu_wjm_map_employment_type( $type_terms );
    $industry        = bpu_wjm_map_industry( $cat_terms );

    // ── Determine job_type (inbound / outbound) ──────────────────
    $is_url   = filter_var( $application, FILTER_VALIDATE_URL ) !== false;
    $is_email = ! $is_url && filter_var( $application, FILTER_VALIDATE_EMAIL ) !== false;

    // If _application has no usable URL/email, fall back to _links_to (Page Links To plugin).
    if ( ! $is_url && ! $is_email && filter_var( $links_to, FILTER_VALIDATE_URL ) !== false ) {
        $application = $links_to;
        $is_url      = true;
    }

    if ( $is_url ) {
        $job_type  = 'outbound';
        $apply_url = esc_url_raw( $application );
    } elseif ( $is_email ) {
        $job_type  = 'outbound';
        $apply_url = 'mailto:' . sanitize_email( $application );
    } else {
        $job_type  = 'outbound'; // no apply info → outbound with empty URL
        $apply_url = '';
    }

    // ── Parse salary ─────────────────────────────────────────────
    [ $sal_min, $sal_max ] = bpu_wjm_parse_salary( $salary_raw );

    // ── Resolve company logo URL from WJM post thumbnail ─────────
    $logo_url = '';
    if ( $company_logo_id ) {
        $img = wp_get_attachment_image_src( $company_logo_id, 'full' );
        if ( $img ) $logo_url = $img[0];
    }

    // ── Get or create bpu_employer taxonomy term ──────────────────
    $employer_name = $company ?: 'Unknown Company';
    $employer_term_id = null;
    if ( class_exists( 'BPU_Headless_Connector' ) ) {
        $employer_term_id = BPU_Headless_Connector::get_or_create_employer_term( $employer_name, [
            'logo_url'    => $logo_url,
            'website'     => $company_website,
            'tagline'     => $company_tagline,
            'twitter'     => $company_twitter,
            'video'       => $company_video,
            'description' => $company_about,
        ] );
    }

    // ── Decide post_status ───────────────────────────────────────
    $bpu_status = ( $wjm->post_status === 'pending' ) ? 'pending' : 'publish';

    // ── Create the BPU job post ───────────────────────────────────
    $bpu_id = wp_insert_post( [
        'post_type'    => BPU_WJM_TARGET_CPT,
        'post_title'   => $title,
        'post_content' => $wjm->post_content,
        'post_status'  => $bpu_status,
        'post_date'    => $wjm->post_date,
        'post_author'  => $wjm->post_author,
    ], true );

    if ( is_wp_error( $bpu_id ) ) {
        return [ 'status' => 'error', 'msg' => "#{$id} "{$title}" — " . $bpu_id->get_error_message() ];
    }

    // ── Attach employer taxonomy term ─────────────────────────────
    if ( $employer_term_id ) {
        wp_set_post_terms( $bpu_id, [ $employer_term_id ], 'bpu_employer' );
    }

    // ── Write BPU meta ────────────────────────────────────────────
    $meta = [
        '_bpu_company'            => $employer_name,
        '_bpu_location'           => $location   ?: 'United Kingdom',
        '_bpu_employment_type'    => $employment_type,
        '_bpu_industry'           => $industry,
        '_bpu_job_type'           => $job_type,
        '_bpu_apply_url'          => $apply_url,
        '_bpu_expires_date'       => $expires,
        '_bpu_salary_currency'    => $salary_currency,
        '_bpu_remote'             => $remote    ? '1' : '0',
        '_bpu_featured'           => $featured  ? '1' : '0',
        '_bpu_filled'             => $filled    ? '1' : '0',
        '_bpu_impressions'        => 0,
        '_bpu_clicks'             => 0,
        '_bpu_applications_count' => 0,
        BPU_WJM_STAMP             => $id,
    ];
    if ( $sal_min !== null ) $meta['_bpu_salary_min'] = $sal_min;
    if ( $sal_max !== null ) $meta['_bpu_salary_max'] = $sal_max;

    foreach ( $meta as $key => $value ) {
        update_post_meta( $bpu_id, $key, $value );
    }

    $flags = array_filter( [ $remote ? 'remote' : '', $featured ? 'featured' : '', $filled ? 'filled' : '' ] );
    $flags_str = $flags ? ' [' . implode( ', ', $flags ) . ']' : '';
    return [ 'status' => 'imported', 'msg' => "#{$id} "{$title}" → BPU #{$bpu_id} [{$job_type}, {$employment_type}, {$industry}]{$flags_str}" ];
}

// ─────────────────────────────────────────────────────────────────
// AJAX HANDLERS
// ─────────────────────────────────────────────────────────────────

add_action( 'wp_ajax_bpu_wjm_scan', 'bpu_wjm_ajax_scan' );
function bpu_wjm_ajax_scan() {
    check_ajax_referer( 'bpu_wjm_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );

    $total = wp_count_posts( BPU_WJM_SOURCE_CPT );
    $publish  = isset( $total->publish )  ? (int) $total->publish  : 0;
    $expired  = isset( $total->expired )  ? (int) $total->expired  : 0;
    $pending  = isset( $total->pending )  ? (int) $total->pending  : 0;

    // Already imported
    $already = (int) ( new WP_Query( [
        'post_type'      => BPU_WJM_TARGET_CPT,
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [ [ 'key' => BPU_WJM_STAMP, 'compare' => 'EXISTS' ] ],
        'suppress_filters' => true,
    ] ) )->found_posts;

    // Preview (first 15 jobs)
    $preview_posts = get_posts( [
        'post_type'        => BPU_WJM_SOURCE_CPT,
        'post_status'      => [ 'publish', 'expired', 'pending' ],
        'posts_per_page'   => 15,
        'orderby'          => 'date',
        'order'            => 'DESC',
        'suppress_filters' => true,
    ] );

    $preview = [];
    foreach ( $preview_posts as $p ) {
        $type_terms = wp_get_post_terms( $p->ID, 'job_listing_type', [ 'fields' => 'names' ] );
        if ( is_wp_error( $type_terms ) ) $type_terms = [];
        $preview[] = [
            'id'      => $p->ID,
            'title'   => $p->post_title,
            'company' => get_post_meta( $p->ID, '_company_name', true ) ?: '—',
            'loc'     => get_post_meta( $p->ID, '_job_location', true ) ?: '—',
            'type'    => implode( ', ', $type_terms ) ?: '—',
            'status'  => $p->post_status,
            'date'    => get_the_date( 'd M Y', $p ),
        ];
    }

    wp_send_json_success( [
        'publish'  => $publish,
        'expired'  => $expired,
        'pending'  => $pending,
        'total'    => $publish + $expired + $pending,
        'already'  => $already,
        'to_import'=> ( $publish + $expired + $pending ) - $already,
        'preview'  => $preview,
    ] );
}

add_action( 'wp_ajax_bpu_wjm_import_batch', 'bpu_wjm_ajax_import_batch' );
function bpu_wjm_ajax_import_batch() {
    check_ajax_referer( 'bpu_wjm_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized' );

    $offset   = max( 0, intval( $_POST['offset'] ?? 0 ) );
    $per_page = 20;

    $jobs = get_posts( [
        'post_type'        => BPU_WJM_SOURCE_CPT,
        'post_status'      => [ 'publish', 'expired', 'pending' ],
        'posts_per_page'   => $per_page,
        'offset'           => $offset,
        'orderby'          => 'ID',
        'order'            => 'ASC',
        'suppress_filters' => true,
    ] );

    $imported = 0; $skipped = 0; $errors = 0;
    $log      = [];

    foreach ( $jobs as $job ) {
        $r = bpu_wjm_import_single( $job );
        $log[] = $r['msg'];
        if ( $r['status'] === 'imported' ) $imported++;
        elseif ( $r['status'] === 'skipped' ) $skipped++;
        else $errors++;
    }

    wp_send_json_success( [
        'processed' => count( $jobs ),
        'imported'  => $imported,
        'skipped'   => $skipped,
        'errors'    => $errors,
        'has_more'  => count( $jobs ) === $per_page,
        'log'       => $log,
    ] );
}

// ─────────────────────────────────────────────────────────────────
// ADMIN PAGE
// ─────────────────────────────────────────────────────────────────

add_action( 'admin_menu', function () {
    add_management_page(
        'WP Job Manager → BPU Importer',
        'WPM → BPU Jobs',
        'manage_options',
        'bpu-wjm-importer',
        'bpu_wjm_admin_page'
    );
} );

function bpu_wjm_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Not allowed.' );
    ?>
    <div class="wrap" id="bpu-wjm-wrap">
        <h1>WP Job Manager → BPU Job Platform Importer</h1>
        <p>Reads all <code>job_listing</code> posts from WP Job Manager and creates equivalent <code>bpu_job</code> posts.
           Already-imported jobs are skipped automatically — safe to re-run.</p>

        <h2>Field mapping</h2>
        <table class="widefat fixed striped" style="max-width:780px;margin-bottom:20px;">
            <thead><tr><th style="width:40%">WP Job Manager source</th><th>BPU destination</th></tr></thead>
            <tbody>
                <tr><td><code>post_title</code></td><td>Job title</td></tr>
                <tr><td><code>post_content</code></td><td>Job description</td></tr>
                <tr><td><code>_job_location</code></td><td><code>_bpu_location</code></td></tr>
                <tr><td><code>_application</code> (URL)</td><td><code>_bpu_apply_url</code> → outbound job</td></tr>
                <tr><td><code>_application</code> (email)</td><td><code>_bpu_apply_url</code> as mailto: → outbound</td></tr>
                <tr><td><code>_links_to</code> (Page Links To)</td><td>Fallback apply URL when <code>_application</code> is empty</td></tr>
                <tr><td><code>_job_expires</code></td><td><code>_bpu_expires_date</code></td></tr>
                <tr><td><code>_job_salary</code></td><td><code>_bpu_salary_min</code> / <code>_bpu_salary_max</code> (parsed)</td></tr>
                <tr><td><code>_job_salary_currency</code></td><td><code>_bpu_salary_currency</code></td></tr>
                <tr><td><code>_remote_position</code></td><td><code>_bpu_remote</code></td></tr>
                <tr><td><code>_featured</code></td><td><code>_bpu_featured</code></td></tr>
                <tr><td><code>_filled</code></td><td><code>_bpu_filled</code></td></tr>
                <tr><td><code>job_listing_type</code> taxonomy</td><td><code>_bpu_employment_type</code> (Full-time, Part-time…)</td></tr>
                <tr><td><code>job_listing_category</code> taxonomy</td><td><code>_bpu_industry</code> (Technology, Finance…)</td></tr>
                <tr><td><code>publish</code> / <code>expired</code> status</td><td>Published in BPU</td></tr>
                <tr><td><code>pending</code> status</td><td>Pending review in BPU</td></tr>
                <tr><th colspan="2" style="background:#f6f7f7;padding-top:10px;">Employer term (creates/updates <code>bpu_employer</code> taxonomy)</th></tr>
                <tr><td><code>_company_name</code></td><td>Employer term name</td></tr>
                <tr><td><code>_thumbnail_id</code> (post featured image)</td><td>Employer term <code>logo_url</code></td></tr>
                <tr><td><code>_company_tagline</code></td><td>Employer term <code>tagline</code></td></tr>
                <tr><td><code>_company_website</code></td><td>Employer term <code>website</code></td></tr>
                <tr><td><code>_company_twitter</code></td><td>Employer term <code>twitter</code></td></tr>
                <tr><td><code>_company_video</code></td><td>Employer term <code>video</code></td></tr>
                <tr><td><code>about_company</code> (ACF wysiwyg)</td><td>Employer term <code>description</code></td></tr>
            </tbody>
        </table>

        <div id="bpu-scan-section">
            <button id="bpu-scan-btn" class="button button-primary button-large">
                Scan WP Job Manager
            </button>
        </div>

        <div id="bpu-scan-results" style="display:none;margin-top:20px;">
            <div class="notice notice-info" style="padding:12px 16px;" id="bpu-scan-summary"></div>

            <div id="bpu-preview-wrap" style="margin-top:16px;"></div>

            <p style="margin-top:16px;">
                <button id="bpu-import-btn" class="button button-primary button-large">
                    Import All Jobs
                </button>
            </p>
        </div>

        <div id="bpu-progress-wrap" style="display:none;margin-top:20px;">
            <p id="bpu-progress-label">Importing…</p>
            <div style="background:#e2e2e2;border-radius:4px;height:20px;width:100%;max-width:600px;overflow:hidden;">
                <div id="bpu-progress-bar" style="height:100%;background:#0073aa;width:0%;transition:width .3s ease;"></div>
            </div>
        </div>

        <div id="bpu-results-wrap" style="display:none;margin-top:20px;"></div>

        <div id="bpu-log-wrap" style="display:none;margin-top:20px;">
            <h3>Import log</h3>
            <pre id="bpu-log" style="background:#1e1e1e;color:#d4d4d4;padding:16px;overflow:auto;max-height:400px;font-size:12px;border-radius:4px;"></pre>
        </div>
    </div>

    <script>
    (function($) {
        const nonce   = '<?php echo esc_js( wp_create_nonce( 'bpu_wjm_nonce' ) ); ?>';
        let   totals  = { imported: 0, skipped: 0, errors: 0 };
        let   logLines= [];
        let   total   = 0;

        // ── Scan ──────────────────────────────────────────────────
        $('#bpu-scan-btn').on('click', function() {
            $(this).prop('disabled', true).text('Scanning…');
            $.post(ajaxurl, { action: 'bpu_wjm_scan', nonce }, function(r) {
                if (!r.success) { alert('Scan failed: ' + (r.data || 'unknown error')); return; }
                const d = r.data;
                total = d.total;

                $('#bpu-scan-summary').html(
                    '<strong>' + d.total + '</strong> WP Job Manager jobs found — ' +
                    d.publish  + ' published, ' +
                    d.expired  + ' expired, ' +
                    d.pending  + ' pending.<br>' +
                    '<strong>' + d.already  + '</strong> already imported into BPU. ' +
                    '<strong>' + d.to_import + '</strong> will be processed.'
                );

                if (d.preview && d.preview.length) {
                    let html = '<h3>Preview (first ' + d.preview.length + ' jobs)</h3>' +
                               '<table class="widefat striped"><thead><tr>' +
                               '<th>#</th><th>Title</th><th>Company</th><th>Location</th><th>Type</th><th>Status</th><th>Posted</th>' +
                               '</tr></thead><tbody>';
                    d.preview.forEach(function(j) {
                        const badge = j.status === 'publish' ? '#46b450' : j.status === 'expired' ? '#888' : '#ffb900';
                        html += '<tr>' +
                            '<td>' + j.id + '</td>' +
                            '<td>' + j.title + '</td>' +
                            '<td>' + j.company + '</td>' +
                            '<td>' + j.loc + '</td>' +
                            '<td>' + j.type + '</td>' +
                            '<td><span style="color:' + badge + ';font-weight:600;">' + j.status + '</span></td>' +
                            '<td>' + j.date + '</td>' +
                            '</tr>';
                    });
                    html += '</tbody></table>';
                    $('#bpu-preview-wrap').html(html);
                }

                $('#bpu-scan-results').show();
                if (d.to_import === 0) {
                    $('#bpu-import-btn').prop('disabled', true).text('Nothing new to import');
                }
            });
        });

        // ── Import ────────────────────────────────────────────────
        $('#bpu-import-btn').on('click', function() {
            if (!confirm('Import ' + total + ' WP Job Manager jobs into the BPU platform? Already-imported jobs will be skipped.')) return;

            $(this).prop('disabled', true);
            $('#bpu-scan-results').hide();
            $('#bpu-progress-wrap').show();

            runBatch(0);
        });

        function runBatch(offset) {
            $.post(ajaxurl, { action: 'bpu_wjm_import_batch', nonce, offset }, function(r) {
                if (!r.success) {
                    showResults(true);
                    return;
                }
                const d = r.data;
                totals.imported += d.imported;
                totals.skipped  += d.skipped;
                totals.errors   += d.errors;
                logLines = logLines.concat(d.log);

                const done = offset + d.processed;
                const pct  = total > 0 ? Math.min(100, Math.round(done / total * 100)) : 100;
                $('#bpu-progress-bar').css('width', pct + '%');
                $('#bpu-progress-label').text('Importing… ' + done + ' / ' + total + ' jobs processed');

                if (d.has_more) {
                    runBatch(offset + d.processed);
                } else {
                    showResults(false);
                }
            }).fail(function() {
                showResults(true);
            });
        }

        function showResults(hadNetworkError) {
            $('#bpu-progress-wrap').hide();
            const colour = totals.errors > 0 ? 'notice-warning' : 'notice-success';
            let html = '<div class="notice ' + colour + '" style="padding:12px 16px;">' +
                '<p><strong>Import complete' + (hadNetworkError ? ' (network error — check log)' : '') + '.</strong></p>' +
                '<p>✅ Imported: <strong>' + totals.imported + '</strong> &nbsp;|&nbsp; ' +
                '⏭ Skipped (already exist): <strong>' + totals.skipped + '</strong> &nbsp;|&nbsp; ' +
                '❌ Errors: <strong>' + totals.errors + '</strong></p>';
            if (totals.imported > 0) {
                html += '<p><a href="' + '<?php echo esc_js( admin_url( "edit.php?post_type=bpu_job" ) ); ?>" class="button">View BPU Jobs →</a></p>';
            }
            html += '</div>';
            $('#bpu-results-wrap').html(html).show();

            if (logLines.length) {
                $('#bpu-log').text(logLines.join('\n'));
                $('#bpu-log-wrap').show();
            }

            if (totals.imported > 0) {
                html += '<hr><p><em>Once verified, you can deactivate and delete this plugin.</em></p>';
            }
        }
    })(jQuery);
    </script>
    <?php
}
