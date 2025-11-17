<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hiring Resources - Interview Questions & Job Description Templates | X0PA</title>
    <meta name="description" content="Comprehensive hiring resources including interview questions and job description templates for various roles. Download free templates and streamline your hiring process with X0PA.">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="Hiring Resources - Interview Questions & Job Descriptions">
    <meta property="og:description" content="Free interview questions and job description templates for HR professionals and hiring managers.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url(home_url('/hiring-resources/')); ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Hiring Resources - Interview Questions & Job Descriptions">
    <meta name="twitter:description" content="Free interview questions and job description templates for HR professionals and hiring managers.">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Configuration -->
    <?php include plugin_dir_path(__FILE__) . '../config/tailwind-config.php'; ?>

    <!-- Custom Styles -->
    <?php include plugin_dir_path(__FILE__) . '../assets/css/styles.php'; ?>

    <!-- PRESERVE:START -->
    <?php include plugin_dir_path(__FILE__) . '../tracking/tracking-codes-head.php'; ?>
    <!-- PRESERVE:END -->
</head>

<body class="bg-gray-50">

    <!-- PRESERVE:START -->
    <?php include plugin_dir_path(__FILE__) . '../tracking/tracking-codes-body-start.php'; ?>
    <!-- PRESERVE:END -->

    <?php
    /**
     * Query all hiring pages and organize them by job title
     *
     * This section queries all x0pa_hiring_page custom post types,
     * groups them by job title, and builds the $job_titles array
     * for display in the hub page.
     */

    // Initialize the job titles array
    $job_titles = array();

    // Query all hiring pages
    $hiring_pages_query = new WP_Query(array(
        'post_type'      => 'x0pa_hiring_page',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array(
            array(
                'key'     => '_x0pa_job_title',
                'compare' => 'EXISTS'
            ),
            array(
                'key'     => '_x0pa_page_type',
                'compare' => 'EXISTS'
            )
        )
    ));

    // Process the query results
    if ($hiring_pages_query->have_posts()) {
        while ($hiring_pages_query->have_posts()) {
            $hiring_pages_query->the_post();

            // Get the job title and page type from post meta
            $job_title = get_post_meta(get_the_ID(), '_x0pa_job_title', true);
            $page_type = get_post_meta(get_the_ID(), '_x0pa_page_type', true);
            $job_category = get_post_meta(get_the_ID(), '_x0pa_job_category', true);

            // Validate required fields
            if (empty($job_title) || empty($page_type)) {
                continue;
            }

            // Initialize job title entry if it doesn't exist
            if (!isset($job_titles[$job_title])) {
                $job_titles[$job_title] = array();

                // Determine category (from meta or extract from job title)
                if (!empty($job_category)) {
                    $job_titles[$job_title]['category'] = sanitize_title($job_category);
                } else {
                    // Auto-categorize based on job title keywords
                    $job_titles[$job_title]['category'] = x0pa_auto_categorize_job($job_title);
                }
            }

            // Add the page URL based on page type
            if ($page_type === 'interview-questions' || $page_type === 'job-description') {
                $job_titles[$job_title][$page_type] = get_permalink();
            }
        }

        // Reset post data
        wp_reset_postdata();
    }

    /**
     * Auto-categorize job titles based on keywords
     *
     * @param string $job_title The job title to categorize
     * @return string The category slug
     */
    function x0pa_auto_categorize_job($job_title) {
        $title_lower = strtolower($job_title);

        // Category mapping based on keywords
        $categories = array(
            'accounting' => array('accountant', 'accounting', 'finance', 'cfo', 'controller', 'bookkeeper', 'auditor'),
            'engineering' => array('engineer', 'engineering', 'developer', 'architect', 'technical', 'software', 'hardware'),
            'marketing' => array('marketing', 'brand', 'content', 'seo', 'digital', 'social media', 'copywriter'),
            'sales' => array('sales', 'business development', 'account executive', 'sales rep', 'revenue'),
            'hr' => array('hr', 'human resources', 'recruiter', 'talent', 'people', 'recruitment'),
            'it' => array('it', 'information technology', 'system administrator', 'network', 'security', 'devops', 'cloud'),
            'operations' => array('operations', 'logistics', 'supply chain', 'project manager', 'program manager'),
            'design' => array('designer', 'design', 'ui', 'ux', 'graphic', 'creative'),
            'customer-service' => array('customer service', 'support', 'customer success', 'client relations'),
            'legal' => array('legal', 'attorney', 'lawyer', 'counsel', 'paralegal'),
            'healthcare' => array('nurse', 'doctor', 'medical', 'healthcare', 'clinical', 'physician'),
            'education' => array('teacher', 'professor', 'instructor', 'educator', 'trainer')
        );

        // Check each category for keyword matches
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($title_lower, $keyword) !== false) {
                    return $category;
                }
            }
        }

        // Default category if no match found
        return 'general';
    }

    // Sort job titles alphabetically by key
    ksort($job_titles);
    ?>

    <!-- Hub Page Container -->
    <div class="min-h-screen">

        <!-- Header Section -->
        <header class="bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 py-12 lg:py-16">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-4">
                        Hiring Resources
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 mb-6">
                        Interview Questions & Job Description Templates
                    </p>
                    <p class="text-lg text-gray-500 max-w-3xl mx-auto">
                        Comprehensive hiring guides for HR professionals and hiring managers. Access expertly crafted interview questions and job descriptions for various roles.
                    </p>
                </div>
            </div>
        </header>

        <!-- Filter/Search Section -->
        <section class="bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 py-8">
                <div class="max-w-4xl mx-auto">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Search Input -->
                        <div class="flex-1">
                            <label for="search-jobs" class="sr-only">Search job titles</label>
                            <input
                                type="text"
                                id="search-jobs"
                                placeholder="Search job titles..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                aria-label="Search job titles"
                            >
                        </div>
                        <!-- Category Filter -->
                        <div class="md:w-64">
                            <label for="filter-category" class="sr-only">Filter by category</label>
                            <select
                                id="filter-category"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                aria-label="Filter by category"
                            >
                                <option value="">All Categories</option>
                                <option value="accounting">Accounting & Finance</option>
                                <option value="engineering">Engineering</option>
                                <option value="marketing">Marketing</option>
                                <option value="sales">Sales</option>
                                <option value="hr">Human Resources</option>
                                <option value="it">Information Technology</option>
                                <option value="operations">Operations</option>
                                <option value="design">Design</option>
                                <option value="customer-service">Customer Service</option>
                                <option value="legal">Legal</option>
                                <option value="healthcare">Healthcare</option>
                                <option value="education">Education</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content: Job Title Cards Grid -->
        <main class="container mx-auto px-4 py-12">
            <div class="max-w-7xl mx-auto">

                <?php if (!empty($job_titles)): ?>
                    <!-- Results Count -->
                    <div class="mb-8">
                        <p class="text-gray-600">
                            Showing <span id="results-count"><?php echo count($job_titles); ?></span> job titles
                        </p>
                    </div>

                    <!-- Job Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="job-cards-grid">

                        <?php foreach ($job_titles as $job_title => $pages): ?>
                            <article
                                class="job-card bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-200"
                                data-category="<?php echo esc_attr($pages['category'] ?? 'general'); ?>"
                                data-title="<?php echo esc_attr(strtolower($job_title)); ?>"
                            >
                                <!-- Card Header -->
                                <div class="p-6 border-b border-gray-100">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                                        <?php echo esc_html($job_title); ?>
                                    </h2>
                                    <?php if (isset($pages['category'])): ?>
                                        <span class="inline-block px-3 py-1 text-sm font-medium text-blue-700 bg-blue-50 rounded-full">
                                            <?php echo esc_html(ucwords(str_replace('-', ' ', $pages['category']))); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Card Body: Resource Links -->
                                <div class="p-6 space-y-3">

                                    <?php if (isset($pages['interview-questions'])): ?>
                                        <a
                                            href="<?php echo esc_url($pages['interview-questions']); ?>"
                                            class="block w-full px-4 py-3 bg-blue-600 text-white text-center font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                            aria-label="View <?php echo esc_attr($job_title); ?> interview questions"
                                        >
                                            <svg class="inline-block w-5 h-5 mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Interview Questions
                                        </a>
                                    <?php endif; ?>

                                    <?php if (isset($pages['job-description'])): ?>
                                        <a
                                            href="<?php echo esc_url($pages['job-description']); ?>"
                                            class="block w-full px-4 py-3 bg-green-600 text-white text-center font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                            aria-label="View <?php echo esc_attr($job_title); ?> job description"
                                        >
                                            <svg class="inline-block w-5 h-5 mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Job Description
                                        </a>
                                    <?php endif; ?>

                                </div>

                                <!-- Card Footer: Resource Count -->
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-lg">
                                    <p class="text-sm text-gray-600">
                                        <?php
                                        $resource_count = 0;
                                        if (isset($pages['interview-questions'])) $resource_count++;
                                        if (isset($pages['job-description'])) $resource_count++;
                                        echo $resource_count . ' resource' . ($resource_count !== 1 ? 's' : '') . ' available';
                                        ?>
                                    </p>
                                </div>

                            </article>
                        <?php endforeach; ?>

                    </div>

                    <!-- No Results Message (hidden by default, shown by JS when filtering) -->
                    <div id="no-results" class="hidden text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-4 text-xl font-medium text-gray-900">No results found</h3>
                        <p class="mt-2 text-gray-600">Try adjusting your search or filter criteria</p>
                    </div>

                <?php else: ?>
                    <!-- Empty State: No hiring pages published yet -->
                    <div class="text-center py-16">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">No Hiring Resources Available Yet</h2>
                        <p class="text-gray-600 max-w-md mx-auto">
                            We're currently building our library of interview questions and job descriptions. Check back soon!
                        </p>
                    </div>
                <?php endif; ?>

            </div>
        </main>

        <!-- Footer CTA Section -->
        <section class="bg-blue-600 py-16">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center text-white">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">
                        Streamline Your Hiring Process with X0PA
                    </h2>
                    <p class="text-xl mb-8 text-blue-100">
                        Use AI-powered tools to find the perfect candidates faster
                    </p>
                    <a
                        href="https://x0pa.com/request-demo/"
                        class="inline-block px-8 py-4 bg-white text-blue-600 font-bold rounded-lg hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600"
                    >
                        Request a Demo
                    </a>
                </div>
            </div>
        </section>

    </div>

    <!-- Filter/Search JavaScript -->
    <?php if (!empty($job_titles)): ?>
    <script>
        // Search and filter functionality
        (function() {
            const searchInput = document.getElementById('search-jobs');
            const categoryFilter = document.getElementById('filter-category');
            const jobCards = document.querySelectorAll('.job-card');
            const resultsCount = document.getElementById('results-count');
            const noResults = document.getElementById('no-results');

            function filterJobs() {
                const searchTerm = searchInput.value.toLowerCase();
                const categoryValue = categoryFilter.value.toLowerCase();
                let visibleCount = 0;

                jobCards.forEach(card => {
                    const title = card.getAttribute('data-title');
                    const category = card.getAttribute('data-category');

                    const matchesSearch = !searchTerm || title.includes(searchTerm);
                    const matchesCategory = !categoryValue || category === categoryValue;

                    if (matchesSearch && matchesCategory) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Update results count
                resultsCount.textContent = visibleCount;

                // Show/hide no results message
                if (visibleCount === 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }

            // Event listeners
            searchInput.addEventListener('input', filterJobs);
            categoryFilter.addEventListener('change', filterJobs);
        })();
    </script>
    <?php endif; ?>

    <!-- PRESERVE:START -->
    <?php include plugin_dir_path(__FILE__) . '../tracking/tracking-codes-body-end.php'; ?>
    <!-- PRESERVE:END -->

</body>
</html>
