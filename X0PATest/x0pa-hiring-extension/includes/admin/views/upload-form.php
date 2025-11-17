<?php
/**
 * Upload Form View
 *
 * @package X0PA_Hiring_Extension
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
$upload_result = null;
if (isset($_POST['submit_upload']) && isset($_POST['x0pa_upload_nonce'])) {
    require_once X0PA_HIRING_PLUGIN_DIR . 'includes/admin/class-csv-uploader.php';
    $upload_result = X0PA_Hiring_CSV_Uploader::handle_upload();
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php
    // Display upload result message
    if ($upload_result !== null) {
        echo X0PA_Hiring_CSV_Uploader::get_status_message($upload_result);
    }
    ?>

    <div class="x0pa-upload-form">
        <h2><?php _e('Upload CSV Files', 'x0pa-hiring'); ?></h2>

        <form method="post" enctype="multipart/form-data" action="">
            <?php wp_nonce_field('x0pa_upload_csv', 'x0pa_upload_nonce'); ?>

            <!-- Interview Questions Section -->
            <div class="x0pa-upload-section">
                <h3><?php _e('Interview Questions CSV', 'x0pa-hiring'); ?></h3>
                <p class="description">
                    <?php _e('Upload a CSV file containing interview questions for various job positions.', 'x0pa-hiring'); ?>
                </p>

                <div class="x0pa-file-input">
                    <label for="interview_questions_csv">
                        <strong><?php _e('Select Interview Questions CSV:', 'x0pa-hiring'); ?></strong>
                    </label>
                    <br>
                    <input type="file"
                           name="interview_questions_csv"
                           id="interview_questions_csv"
                           accept=".csv,text/csv,application/csv">
                </div>

                <div class="x0pa-instructions">
                    <h4><?php _e('CSV Format Requirements:', 'x0pa-hiring'); ?></h4>
                    <ul>
                        <li><strong><?php _e('Required columns:', 'x0pa-hiring'); ?></strong> job_title, last_updated</li>
                        <li><strong><?php _e('Section columns:', 'x0pa-hiring'); ?></strong> section_1_id, section_1_title, section_1_questions_json, section_2_id, etc.</li>
                        <li><?php _e('Questions should be in JSON format within the questions_json column', 'x0pa-hiring'); ?></li>
                        <li><?php _e('Example: {"q1": "What is your experience?", "q2": "Why this role?"}', 'x0pa-hiring'); ?></li>
                    </ul>
                </div>
            </div>

            <!-- Job Description Section -->
            <div class="x0pa-upload-section">
                <h3><?php _e('Job Description CSV', 'x0pa-hiring'); ?></h3>
                <p class="description">
                    <?php _e('Upload a CSV file containing job descriptions for various positions.', 'x0pa-hiring'); ?>
                </p>

                <div class="x0pa-file-input">
                    <label for="job_description_csv">
                        <strong><?php _e('Select Job Description CSV:', 'x0pa-hiring'); ?></strong>
                    </label>
                    <br>
                    <input type="file"
                           name="job_description_csv"
                           id="job_description_csv"
                           accept=".csv,text/csv,application/csv">
                </div>

                <div class="x0pa-instructions">
                    <h4><?php _e('CSV Format Requirements:', 'x0pa-hiring'); ?></h4>
                    <ul>
                        <li><strong><?php _e('Required column:', 'x0pa-hiring'); ?></strong> job_title</li>
                        <li><strong><?php _e('Optional columns:', 'x0pa-hiring'); ?></strong> last_updated, objectives, responsibilities, required_skills, preferred_skills, what_role_does, skills_to_look_for</li>
                        <li><?php _e('All text fields can contain multi-line content', 'x0pa-hiring'); ?></li>
                    </ul>
                </div>
            </div>

            <!-- General Instructions -->
            <div class="x0pa-instructions">
                <h3><?php _e('Important Notes:', 'x0pa-hiring'); ?></h3>
                <ul>
                    <li><?php _e('You can upload one or both CSV files at the same time', 'x0pa-hiring'); ?></li>
                    <li><?php _e('If a page for a job title already exists, it will be updated', 'x0pa-hiring'); ?></li>
                    <li><?php _e('Maximum file size: 10MB', 'x0pa-hiring'); ?></li>
                    <li><?php _e('Supported formats: .csv files only', 'x0pa-hiring'); ?></li>
                    <li><?php _e('The hub page at /hiring/ will be automatically updated after upload', 'x0pa-hiring'); ?></li>
                </ul>
            </div>

            <!-- Submit Button -->
            <p class="submit">
                <?php submit_button(__('Upload CSV Files', 'x0pa-hiring'), 'primary', 'submit_upload', false); ?>
            </p>
        </form>
    </div>

    <!-- Sample CSV Format Section -->
    <div class="x0pa-upload-form" style="margin-top: 30px;">
        <h2><?php _e('Sample CSV Formats', 'x0pa-hiring'); ?></h2>

        <h3><?php _e('Interview Questions CSV Example:', 'x0pa-hiring'); ?></h3>
        <pre style="background: #f5f5f5; padding: 15px; overflow-x: auto; border: 1px solid #ddd;">
job_title,last_updated,section_1_id,section_1_title,section_1_questions_json,section_2_id,section_2_title,section_2_questions_json
Accountant,2025-01-15,general,General Questions,"{""q1"":""Tell us about yourself"",""q2"":""Why accounting?""}",technical,Technical Skills,"{""q1"":""Experience with tax software?"",""q2"":""Explain GAAP principles""}"
Software Engineer,2025-01-15,general,General Questions,"{""q1"":""Tell us about yourself"",""q2"":""Why software engineering?""}",technical,Technical Skills,"{""q1"":""Programming languages?"",""q2"":""Explain OOP concepts""}"
        </pre>

        <h3><?php _e('Job Description CSV Example:', 'x0pa-hiring'); ?></h3>
        <pre style="background: #f5f5f5; padding: 15px; overflow-x: auto; border: 1px solid #ddd;">
job_title,last_updated,objectives,responsibilities,required_skills,preferred_skills,what_role_does,skills_to_look_for
Accountant,2025-01-15,"Manage financial records and reporting","Prepare financial statements; Manage accounts payable/receivable","Bachelor's in Accounting; 3+ years experience","CPA certification; SAP experience","Ensures accurate financial reporting","Attention to detail; Analytical thinking"
Software Engineer,2025-01-15,"Develop software solutions","Write clean code; Participate in code reviews","Bachelor's in CS; 2+ years experience","AWS certification; Agile experience","Builds scalable applications","Problem solving; Communication"
        </pre>
    </div>
</div>
