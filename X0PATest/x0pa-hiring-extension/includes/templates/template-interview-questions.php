<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    /**
     * SEO Meta Tags
     * Generated dynamically from seo-meta-generator.php
     * Includes: title, description, canonical, schema markup, Open Graph, Twitter Cards
     */
    echo generate_seo_meta($seo_config, $body_content);
    ?>

    <!-- Tailwind CSS with Forms Plugin -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>

    <!-- Tailwind Configuration -->
    <?php include plugin_dir_path(__FILE__) . '../config/tailwind-config.php'; ?>

    <!-- Custom Styles -->
    <?php include plugin_dir_path(__FILE__) . '../assets/css/styles.php'; ?>

    <!-- PRESERVE:START -->
    <?php include plugin_dir_path(__FILE__) . '../tracking/tracking-codes-head.php'; ?>
    <!-- PRESERVE:END -->
</head>

<body class="bg-white">

    <!-- PRESERVE:START -->
    <?php include plugin_dir_path(__FILE__) . '../tracking/tracking-codes-body-start.php'; ?>
    <!-- PRESERVE:END -->

    <!-- Main Page Container -->
    <div class="hiring">

        <?php
        /**
         * Hero Section
         * Displays page title, job title, last updated date, reading time
         */
        echo generate_hero_section($hero_config, $body_content);
        ?>

        <!-- Three-Column Layout: Left Sidebar | Main Content | Right Sidebar -->
        <div class="hiring__layout">

            <!-- Left Sidebar: Table of Contents / Jump Links -->
            <aside class="hiring__layout-sidebar-left">
                <?php
                /**
                 * Render jump links navigation
                 * Auto-generated from content sections
                 * First section defaults to 'questions-overview'
                 */
                echo render_jump_links($sections, 'questions-overview');
                ?>
            </aside>

            <!-- Main Content Area -->
            <main class="hiring__layout-content">

                <!-- Questions Overview Section - Dynamically Generated -->
                <section id="questions-overview" class="content-section">
                    <div class="content-section__title">
                        Interview Questions Overview
                    </div>
                    <div class="content-section__body">
                        <?php
                        /**
                         * Generated overview card showing:
                         * - Total question count
                         * - Categories breakdown
                         * - PDF download CTA
                         */
                        include plugin_dir_path(__FILE__) . '../components/cta__product-card-interview-questions.php';
                        ?>
                    </div>
                </section>

                <!-- Dynamic Question Sections -->
                <?php foreach ($question_sections as $section): ?>
                    <section id="<?php echo esc_attr($section['id']); ?>" class="content-section">

                        <!-- Section Title -->
                        <div class="content-section__title">
                            <?php echo esc_html($section['title']); ?>
                        </div>

                        <!-- Section Body -->
                        <div class="content-section__body">

                            <?php foreach ($section['questions'] as $question): ?>
                                <!-- Individual Question Article -->
                                <article class="content-item">

                                    <!-- Question Title -->
                                    <h3 class="content-item__title">
                                        <?php echo esc_html($question['question']); ?>
                                    </h3>

                                    <!-- What to Listen For Box -->
                                    <div class="content-item__box">
                                        <h4 class="content-item__box-title">
                                            What to Listen For:
                                        </h4>
                                        <ul class="content-item__box-list">
                                            <?php foreach ($question['what_to_listen_for'] as $point): ?>
                                                <li class="content-item__box-list-item">
                                                    <?php echo esc_html($point); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                </article>
                            <?php endforeach; ?>

                        </div>
                    </section>
                <?php endforeach; ?>

                <!-- Cross-Link CTA: Link to Job Description -->
                <?php
                $page_type = 'interview-questions';
                include plugin_dir_path(__FILE__) . 'partials/cta-cross-link.php';
                ?>

            </main>

            <!-- Right Sidebar: Author, Newsletter, Resources -->
            <aside class="hiring__layout-sidebar-right">

                <!-- Author Bio Card -->
                <?php include plugin_dir_path(__FILE__) . 'partials/sidebar-author.php'; ?>

                <!-- Newsletter Signup Form -->
                <?php include plugin_dir_path(__FILE__) . 'partials/sidebar-newsletter.php'; ?>

                <!-- Sticky Resources Section: Related Links + CTA -->
                <?php
                $page_type = 'interview-questions';
                include plugin_dir_path(__FILE__) . 'partials/sidebar-resources-sticky.php';
                ?>

            </aside>

        </div> <!-- End hiring__layout -->

    </div> <!-- End hiring -->

    <!-- Footer CTA: How X0PA Helps -->
    <?php include plugin_dir_path(__FILE__) . 'partials/footer-cta.php'; ?>

    <!-- PDF Download Gate Modal -->
    <!-- PRESERVE:START -->
    <?php include plugin_dir_path(__FILE__) . 'partials/pdf-download-modal.php'; ?>
    <!-- PRESERVE:END -->

    <!-- JavaScript -->
    <script src="<?php echo plugin_dir_url(__FILE__) . '../assets/js/hiring-script.js'; ?>"></script>
    <script>
        // Initialize scroll spy for jump links highlighting
        initScrollSpy('questions-overview');
    </script>
    <script src="<?php echo plugin_dir_url(__FILE__) . '../assets/js/hubspot-newsletter.js'; ?>"></script>
    <script src="<?php echo plugin_dir_url(__FILE__) . '../assets/js/hubspot-pdf-gate.js'; ?>"></script>

    <!-- PRESERVE:START -->
    <?php include plugin_dir_path(__FILE__) . '../tracking/tracking-codes-body-end.php'; ?>
    <!-- PRESERVE:END -->

</body>
</html>
