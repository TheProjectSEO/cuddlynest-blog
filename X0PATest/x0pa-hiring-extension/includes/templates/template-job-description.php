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
                 * First section typically 'objectives' for job descriptions
                 */
                echo render_jump_links($sections, 'objectives');
                ?>
            </aside>

            <!-- Main Content Area -->
            <main class="hiring__layout-content">

                <!-- Section 1: Objectives of this Role -->
                <section id="objectives" class="content-section">
                    <div class="content-section__title">
                        Objectives of this Role
                    </div>
                    <div class="content-section__body">
                        <article class="content-item">
                            <div class="job-description-section">
                                <div class="job-description-section__content">
                                    <ul class="content-item__box-list">
                                        <?php foreach ($job_data['objectives'] as $objective): ?>
                                            <li class="content-item__box-list-item">
                                                <?php echo esc_html($objective); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- Section 2: Key Responsibilities -->
                <section id="responsibilities" class="content-section">
                    <div class="content-section__title">
                        Key Responsibilities
                    </div>
                    <div class="content-section__body">
                        <article class="content-item">
                            <div class="job-description-section">
                                <div class="job-description-section__content">
                                    <ul class="content-item__box-list">
                                        <?php foreach ($job_data['responsibilities'] as $responsibility): ?>
                                            <li class="content-item__box-list-item">
                                                <?php echo esc_html($responsibility); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- Section 3: Required Skills & Qualifications -->
                <section id="required-skills" class="content-section">
                    <div class="content-section__title">
                        Required Skills & Qualifications
                    </div>
                    <div class="content-section__body">
                        <article class="content-item">
                            <div class="job-description-section">
                                <div class="job-description-section__content">
                                    <ul class="content-item__box-list">
                                        <?php foreach ($job_data['required_skills'] as $skill): ?>
                                            <li class="content-item__box-list-item">
                                                <?php echo esc_html($skill); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- Section 4: Preferred Skills & Qualifications -->
                <section id="preferred-skills" class="content-section">
                    <div class="content-section__title">
                        Preferred Skills & Qualifications
                    </div>
                    <div class="content-section__body">
                        <article class="content-item">
                            <div class="job-description-section">
                                <div class="job-description-section__content">
                                    <ul class="content-item__box-list">
                                        <?php foreach ($job_data['preferred_skills'] as $skill): ?>
                                            <li class="content-item__box-list-item">
                                                <?php echo esc_html($skill); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- Product Card CTA -->
                <?php include plugin_dir_path(__FILE__) . '../components/cta__product-card-job-description.php'; ?>

                <!-- Section 5: What Does a [Job Title] Do? -->
                <section id="what-<?php echo esc_attr(strtolower(str_replace(' ', '-', $job_title))); ?>-does" class="content-section">
                    <div class="content-section__title">
                        What Does <?php echo esc_html($job_data['article_prefix']); ?> <?php echo esc_html($job_title); ?> Do?
                    </div>
                    <div class="content-section__body">
                        <article class="content-item">

                            <?php foreach ($job_data['what_they_do']['paragraphs'] as $paragraph): ?>
                                <p class="content-item__box-list-item">
                                    <?php echo esc_html($paragraph); ?>
                                </p>
                            <?php endforeach; ?>

                            <div class="content-item__box">
                                <h4 class="content-item__box-title">
                                    What Skills and Qualifications Should You Look For?
                                </h4>
                                <ul class="content-item__box-list">
                                    <?php foreach ($job_data['what_they_do']['key_skills'] as $skill): ?>
                                        <li class="content-item__box-list-item">
                                            <?php echo esc_html($skill); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                        </article>
                    </div>
                </section>

                <!-- Section 6: What Skills to Look For When Hiring -->
                <section id="skills-to-look-for" class="content-section">
                    <div class="content-section__title">
                        What Skills to Look For When Hiring <?php echo esc_html($job_data['article_prefix']); ?> <?php echo esc_html($job_title); ?>
                    </div>

                    <div class="content-section__body">
                        <article class="content-item">

                            <!-- Beyond the Resume -->
                            <h3 class="content-item__title">
                                Beyond the Resume: Key Indicators of Success
                            </h3>
                            <p class="content-item__box-list-item">
                                <?php echo esc_html($job_data['skills_to_look_for']['intro']); ?>
                            </p>

                            <div class="content-item__box">
                                <h4 class="content-item__box-title">
                                    Technical Competencies to Assess:
                                </h4>
                                <ul class="content-item__box-list">
                                    <?php foreach ($job_data['skills_to_look_for']['technical_competencies'] as $competency): ?>
                                        <li class="content-item__box-list-item">
                                            <?php echo esc_html($competency); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- Soft Skills That Matter -->
                            <h3 class="content-item__title">
                                Soft Skills That Matter
                            </h3>
                            <p class="content-item__box-list-item">
                                <?php echo esc_html($job_data['skills_to_look_for']['soft_skills_intro']); ?>
                            </p>

                            <div class="content-item__box">
                                <h4 class="content-item__box-title">
                                    Behavioral Indicators to Watch For:
                                </h4>
                                <ul class="content-item__box-list">
                                    <?php foreach ($job_data['skills_to_look_for']['behavioral_indicators'] as $indicator): ?>
                                        <li class="content-item__box-list-item">
                                            <?php echo esc_html($indicator); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- Red Flags to Watch Out For -->
                            <h3 class="content-item__title">
                                Red Flags to Watch Out For
                            </h3>
                            <p class="content-item__box-list-item">
                                <?php echo esc_html($job_data['skills_to_look_for']['red_flags_intro']); ?>
                            </p>

                            <div class="content-item__box">
                                <h4 class="content-item__box-title">
                                    Warning Signs:
                                </h4>
                                <ul class="content-item__box-list">
                                    <?php foreach ($job_data['skills_to_look_for']['warning_signs'] as $warning): ?>
                                        <li class="content-item__box-list-item">
                                            <?php echo esc_html($warning); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                        </article>
                    </div>
                </section>

                <!-- Cross-Link CTA: Link to Interview Questions -->
                <?php
                $page_type = 'job-description';
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
                $page_type = 'job-description';
                include plugin_dir_path(__FILE__) . 'partials/sidebar-resources-sticky.php';
                ?>

            </aside>

        </div> <!-- End hiring__layout -->

    </div> <!-- End hiring -->

    <!-- Footer CTA: How X0PA Helps -->
    <?php include plugin_dir_path(__FILE__) . 'partials/footer-cta.php'; ?>

    <!-- JavaScript -->
    <script src="<?php echo plugin_dir_url(__FILE__) . '../assets/js/hiring-script.js'; ?>"></script>
    <script>
        // Initialize scroll spy for jump links highlighting
        initScrollSpy('objectives');
    </script>
    <script src="<?php echo plugin_dir_url(__FILE__) . '../assets/js/hubspot-newsletter.js'; ?>"></script>

    <!-- PRESERVE:START -->
    <?php include plugin_dir_path(__FILE__) . '../tracking/tracking-codes-body-end.php'; ?>
    <!-- PRESERVE:END -->

</body>
</html>
