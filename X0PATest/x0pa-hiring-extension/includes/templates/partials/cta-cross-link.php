<!-- Cross-Link CTA Section -->
<!-- Links between Interview Questions <-> Job Description pages -->
<section class="cross-link-cta" role="complementary" aria-label="Related hiring resource">
    <div class="cross-link-cta__container">

        <?php if ($page_type === 'interview-questions'): ?>
            <!-- From Interview Questions to Job Description -->
            <div class="cross-link-cta__card">
                <div class="cross-link-cta__icon cross-link-cta__icon--job-description">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>

                <div class="cross-link-cta__content">
                    <h3 class="cross-link-cta__title">
                        Need a Job Description Template?
                    </h3>
                    <p class="cross-link-cta__description">
                        Create a comprehensive job posting for this role with our detailed job description template including responsibilities, qualifications, and key skills.
                    </p>

                    <a
                        href="<?php echo esc_url($cross_link_url ?? '#'); ?>"
                        class="cross-link-cta__button"
                        aria-label="View <?php echo esc_attr($job_title ?? 'job'); ?> description template"
                    >
                        <span class="cross-link-cta__button-text">
                            View Job Description Template
                        </span>
                        <svg class="cross-link-cta__button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>

                <div class="cross-link-cta__badge">
                    <span class="cross-link-cta__badge-text">Free Template</span>
                </div>
            </div>

        <?php elseif ($page_type === 'job-description'): ?>
            <!-- From Job Description to Interview Questions -->
            <div class="cross-link-cta__card">
                <div class="cross-link-cta__icon cross-link-cta__icon--interview-questions">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <div class="cross-link-cta__content">
                    <h3 class="cross-link-cta__title">
                        Ready to Interview Candidates?
                    </h3>
                    <p class="cross-link-cta__description">
                        Get our comprehensive interview questions guide with expert tips on what to listen for in candidate responses. Perfect for evaluating skills and cultural fit.
                    </p>

                    <a
                        href="<?php echo esc_url($cross_link_url ?? '#'); ?>"
                        class="cross-link-cta__button"
                        aria-label="View <?php echo esc_attr($job_title ?? 'job'); ?> interview questions"
                    >
                        <span class="cross-link-cta__button-text">
                            View Interview Questions
                        </span>
                        <svg class="cross-link-cta__button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>

                <div class="cross-link-cta__badge">
                    <span class="cross-link-cta__badge-text">Free Guide</span>
                </div>
            </div>

        <?php endif; ?>

        <!-- Additional Resources -->
        <div class="cross-link-cta__additional">
            <p class="cross-link-cta__additional-text">
                <strong>Looking for more resources?</strong>
                <a href="/hiring-resources/" class="cross-link-cta__additional-link">
                    Browse our complete library of hiring guides
                    <svg class="cross-link-cta__additional-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </p>
        </div>

    </div>
</section>
