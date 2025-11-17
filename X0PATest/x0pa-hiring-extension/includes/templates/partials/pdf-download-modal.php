<!-- PDF Download Gate Modal -->
<section class="contents">
<div id="pdf-download-modal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50" style="display: none;">
<div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4 relative">
<!-- Close Button -->
<button
id="pdf-modal-close"
class="absolute top-4 right-4 text-gray hover:text-dark transition-colors"
aria-label="Close modal">
<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
</svg>
</button>

<!-- Modal Content -->
<div class="p-6">
<div class="mb-4">
<div class="flex items-center gap-3 mb-3">
<div class="flex items-center justify-center w-12 h-12 bg-blue-50 rounded-full flex-shrink-0">
<svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20">
<path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
</svg>
</div>
<h2 class="text-xl font-bold text-dark">Download Interview Questions PDF</h2>
</div>
<p class="text-gray text-sm">
Enter your email to receive instant access to the complete interview guide.
</p>
</div>

<!-- HubSpot Form -->
<form id="pdf-download-form" class="flex flex-col gap-3">
<div>
<label for="pdf-email" class="block text-sm font-semibold text-dark mb-2">
Email Address <span class="text-red-500">*</span>
</label>
<input
type="email"
id="pdf-email"
name="email"
placeholder="your@email.com"
class="w-full px-4 py-2 border border-gray rounded-md text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary focus:ring-opacity-20"
required>
</div>

<div>
<label for="pdf-firstname" class="block text-sm font-semibold text-dark mb-2">
First Name
</label>
<input
type="text"
id="pdf-firstname"
name="firstname"
placeholder="Your First Name"
class="w-full px-4 py-2 border border-gray rounded-md text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary focus:ring-opacity-20">
</div>

<div>
<label for="pdf-lastname" class="block text-sm font-semibold text-dark mb-2">
Last Name
</label>
<input
type="text"
id="pdf-lastname"
name="lastname"
placeholder="Your Last Name"
class="w-full px-4 py-2 border border-gray rounded-md text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary focus:ring-opacity-20">
</div>

<div>
<label for="pdf-company" class="block text-sm font-semibold text-dark mb-2">
Company
</label>
<input
type="text"
id="pdf-company"
name="company"
placeholder="Your Company Name"
class="w-full px-4 py-2 border border-gray rounded-md text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary focus:ring-opacity-20">
</div>

<!-- Status Message -->
<div id="pdf-form-message" class="text-sm text-center hidden"></div>

<!-- Submit Button -->
<button
type="submit"
id="pdf-submit-btn"
class="w-full bg-primary text-white px-6 py-2.5 rounded-md font-semibold hover:bg-navy transition-colors duration-200 flex items-center justify-center gap-2">
<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
<path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
</svg>
<span>Download PDF</span>
</button>

<p class="text-xs text-gray text-center">
By downloading, you agree to receive occasional emails from x0pa. Unsubscribe anytime.
</p>
</form>
</div>
</div>
</div>
</section>
