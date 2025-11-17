// HubSpot PDF Download Gate
// Handles modal display, form submission to HubSpot, and secure PDF download

(function() {
    'use strict';

    // HubSpot Configuration for PDF Download Gate
    const HUBSPOT_CONFIG = {
        portalId: '3071393',
        formId: 'b970520d-56c2-430c-b99f-3e490c051f5d',
        apiEndpoint: 'https://api.hsforms.com/submissions/v3/integration/submit'
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initPDFDownloadGate();
    });

    function initPDFDownloadGate() {
        const modal = document.getElementById('pdf-download-modal');
        const downloadBtn = document.getElementById('pdf-download-btn');
        const downloadBtnSidebar = document.getElementById('pdf-download-btn-sidebar');
        const closeBtn = document.getElementById('pdf-modal-close');
        const form = document.getElementById('pdf-download-form');

        // Debug logging
        console.log('Modal:', modal);
        console.log('Download Button (Main):', downloadBtn);
        console.log('Download Button (Sidebar):', downloadBtnSidebar);
        console.log('Close Button:', closeBtn);
        console.log('Form:', form);

        if (!modal || (!downloadBtn && !downloadBtnSidebar) || !closeBtn || !form) {
            console.error('PDF download gate elements not found:');
            console.error('Missing modal?', !modal);
            console.error('Missing buttons?', !downloadBtn && !downloadBtnSidebar);
            console.error('Missing close?', !closeBtn);
            console.error('Missing form?', !form);
            return;
        }

        // Open modal when main download button is clicked
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(modal);
            });
        }

        // Open modal when sidebar download button is clicked
        if (downloadBtnSidebar) {
            downloadBtnSidebar.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(modal);
            });
        }

        // Close modal when close button is clicked
        closeBtn.addEventListener('click', function() {
            closeModal(modal);
        });

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal(modal);
            }
        });

        // Handle form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(form, modal);
        });
    }

    function openModal(modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeModal(modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }

    function handleFormSubmit(form, modal) {
        const emailInput = document.getElementById('pdf-email');
        const firstnameInput = document.getElementById('pdf-firstname');
        const lastnameInput = document.getElementById('pdf-lastname');
        const companyInput = document.getElementById('pdf-company');
        const submitBtn = document.getElementById('pdf-submit-btn');
        const messageDiv = document.getElementById('pdf-form-message');

        const email = emailInput.value.trim();

        // Validate email
        if (!email || !isValidEmail(email)) {
            showMessage(messageDiv, 'Please enter a valid email address.', 'error');
            return;
        }

        // Disable form during submission
        setFormState(form, submitBtn, true);
        hideMessage(messageDiv);

        // Build HubSpot submission payload
        const fields = [
            {
                name: 'email',
                value: email
            }
        ];

        // Add optional fields if provided
        if (firstnameInput.value.trim()) {
            fields.push({
                name: 'firstname',
                value: firstnameInput.value.trim()
            });
        }

        if (lastnameInput.value.trim()) {
            fields.push({
                name: 'lastname',
                value: lastnameInput.value.trim()
            });
        }

        if (companyInput.value.trim()) {
            fields.push({
                name: 'company',
                value: companyInput.value.trim()
            });
        }

        const payload = {
            fields: fields,
            context: {
                pageUri: window.location.href,
                pageName: document.title
            }
        };

        // Add HubSpot tracking cookie if available
        const hubspotCookie = getHubSpotTrackingCookie();
        if (hubspotCookie) {
            payload.context.hutk = hubspotCookie;
        }

        // Build API endpoint URL
        const apiUrl = `${HUBSPOT_CONFIG.apiEndpoint}/${HUBSPOT_CONFIG.portalId}/${HUBSPOT_CONFIG.formId}`;

        // Submit to HubSpot
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(function(response) {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error('Submission failed');
            }
        })
        .then(function(data) {
            // HubSpot submission successful - now generate download token
            generateDownloadToken(modal, messageDiv, form, submitBtn);
        })
        .catch(function(error) {
            // Error submitting to HubSpot
            console.error('HubSpot submission error:', error);
            showMessage(messageDiv, 'Something went wrong. Please try again.', 'error');
            setFormState(form, submitBtn, false);
        });
    }

    function generateDownloadToken(modal, messageDiv, form, submitBtn) {
        // Call backend to generate secure token
        fetch('includes/api/generate-download-token.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(function(response) {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error('Token generation failed');
            }
        })
        .then(function(data) {
            if (data.success && data.token) {
                // Show success message briefly
                showMessage(messageDiv, 'Success! Starting download...', 'success');

                // Close modal after brief delay
                setTimeout(function() {
                    closeModal(modal);

                    // Redirect to download with token
                    window.location.href = 'includes/api/download-interview-questions.php?token=' + data.token;

                    // Reset form
                    form.reset();
                    setFormState(form, submitBtn, false);
                    hideMessage(messageDiv);
                }, 1000);
            } else {
                throw new Error('Invalid token response');
            }
        })
        .catch(function(error) {
            console.error('Token generation error:', error);
            showMessage(messageDiv, 'Download failed. Please try again.', 'error');
            setFormState(form, submitBtn, false);
        });
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function setFormState(form, submitBtn, isSubmitting) {
        const inputs = form.querySelectorAll('input');
        inputs.forEach(function(input) {
            input.disabled = isSubmitting;
        });

        submitBtn.disabled = isSubmitting;

        if (isSubmitting) {
            submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span>';
        } else {
            submitBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg><span>Download PDF</span>';
        }
    }

    function showMessage(messageDiv, text, type) {
        messageDiv.textContent = text;
        messageDiv.classList.remove('hidden', 'text-green-600', 'text-red-600');

        if (type === 'success') {
            messageDiv.classList.add('text-green-600');
        } else if (type === 'error') {
            messageDiv.classList.add('text-red-600');
        }
    }

    function hideMessage(messageDiv) {
        messageDiv.classList.add('hidden');
    }

    function getHubSpotTrackingCookie() {
        // Get HubSpot tracking cookie (hubspotutk) if it exists
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();
            if (cookie.startsWith('hubspotutk=')) {
                return cookie.substring('hubspotutk='.length);
            }
        }
        return null;
    }

})();
