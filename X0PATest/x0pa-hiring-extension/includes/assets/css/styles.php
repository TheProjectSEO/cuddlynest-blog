<style type="text/tailwindcss">
    @layer components { 

/* Hiring Page Block */
 .hiring {
     @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
 }

 /* Hiring
 Page Header */
 .hiring__header {
     @apply mb-12 text-center relative;
 }

 /* Hero Section - Full Viewport Width
 Background */
 .hiring__hero {
     @apply bg-blue-dark py-16 overflow-x-hidden;
     margin-left: calc(-50vw + 50%);
     margin-right: calc(-50vw + 50%);
     padding-left: calc(50vw - 50%);
     padding-right: calc(50vw - 50%);
 }

 .hiring__hero-wrapper {
     @apply max-w-7xl mx-auto px-8 flex flex-col lg:flex-row gap-8 items-start justify-between;
 }

 .hiring__hero-content {
     @apply flex-1 text-center;
 }

 .hiring__hero-tag {
     @apply inline-block mb-6;
 }

 .hiring__hero-tag-text {
     @apply bg-white bg-opacity-20 text-white px-4 py-2 rounded-md text-sm font-medium;
 }

 .hiring__hero-title {
     @apply text-4xl md:text-5xl font-bold text-white mb-6 leading-tight;
 }

 .hiring__hero-description {
     @apply text-white text-lg max-w-2xl mx-auto leading-relaxed;
 }

 .hiring__meta {
     @apply text-sm text-white mb-6 flex flex-wrap items-center justify-center gap-3 mt-4;
 }

 .hiring__meta-item {
     @apply flex items-center gap-1;
 }

 .hiring__meta-link {
     @apply text-white hover:no-underline;
 }

 .hiring__meta-divider {
     @apply text-white opacity-70;
 }

 /* Hiring Page Layout - Three Column Grid */
 .hiring__layout {
     @apply grid grid-cols-1 lg:grid-cols-[250px_1fr_260px] gap-8;
 }

 /* Hiring Page Content */
 .hiring__layout-content {
     @apply flex flex-col gap-8;
 }

 /* Hiring Page Sidebar - Left */
 .hiring__layout-sidebar-left {
     @apply block;
 }

 /* Hiring Page Author
 Section - Right */
 .hiring__layout-sidebar-right {
     @apply block;
 }

 /* Content Section Block */
 .content-section {
     @apply bg-white rounded-lg;
 }

 .content-section__title {
     @apply text-xl text-dark mb-6 pb-3 border-b-2 border-primary;
 }

 .content-section__body {
     @apply flex flex-col gap-8;
 }

 /* Content Item */
 .content-item {
     @apply pl-0;
 }

 .content-item__title {
     @apply text-2xl font-bold text-dark mb-3;
 }

 .content-item__box {
     @apply bg-white rounded-md p-4 border border-gray;
 }

 .content-item__box-title {
     @apply text-sm font-semibold text-navy mb-2;
 }

 .content-item__box-list {
     @apply list-disc list-inside flex flex-col gap-1 text-dark;
 }

 .content-item__box-list-item {
     @apply leading-relaxed;
 }

 /* Template Box */
 .job-description-section {
     @apply bg-gray bg-opacity-10 border border-gray rounded-md p-6 mb-4 font-mono text-sm;
 }

 .job-description-section__content {
     @apply text-dark leading-relaxed whitespace-pre-line;
 }

 /* Placeholder Text */
 .highlighted-text {
     @apply text-primary font-semibold;
 }

 /* Interview Questions List */

 .interview-questions-list {
     @apply list-decimal list-inside flex flex-col gap-1 text-dark;
 }

 .interview-questions-list-item {
     @apply leading-relaxed text-xl;
 }

 .interview-questions-list-item h2 {
     @apply inline text-xl leading-relaxed m-0 p-0 font-normal;
 }

 .interview-questions-list__title {
     @apply text-2xl font-bold text-dark mb-6 pb-3 border-b-2 border-primary;
 }

 /* Jump Links Navigation */
 .jump-links {
     @apply lg:sticky top-8 bg-white p-4 pr-0;
     @apply border border-gray rounded-lg;
     @apply lg:border-0 lg:border-r lg:border-gray lg:rounded-none;
 }

 .jump-links__toggle {
     @apply w-full flex items-center justify-between text-left cursor-pointer;
     @apply lg:cursor-default lg:pointer-events-none;
     @apply bg-transparent border-0 p-0;
 }

 .jump-links__title {
     @apply text-base font-bold text-dark mb-0 lg:mb-3;
 }

 .jump-links__toggle-icon {
     @apply text-dark transition-transform duration-200;
     @apply lg:hidden;
 }

 .jump-links__toggle[aria-expanded="true"] .jump-links__toggle-icon {
     @apply rotate-180;
 }

 .jump-links__list {
     @apply flex flex-col gap-0 mt-3;
     @apply hidden lg:flex;
     @apply overflow-hidden transition-all duration-300;
 }

 .jump-links__list--expanded {
     @apply flex;
 }

 .jump-links__item {
     @apply list-none;
 }

 .jump-links__link {
     @apply text-xs text-gray hover:text-primary transition-colors duration-200 block py-2 pr-4;
 }

 .jump-links__link--active {
     @apply bg-blue-50 text-primary font-medium;
 }


 /* Smooth scroll behavior */
 html {
     scroll-behavior: smooth;
 }
 }
</style>