// HubSpot Newsletter Form Integration
// Submits newsletter signups to HubSpot Forms API v3

(function() {
    'use strict';

    // HubSpot Configuration
    const HUBSPOT_CONFIG = {
        portalId: '3071393',
        formId: '658c6a38-b3fe-4781-8250-cd5d701b4098',
        apiEndpoint: 'https://api.hsforms.com/submissions/v3/integration/submit'
    };

    // Initialize newsletter form when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initNewsletterForm();
    });

    function initNewsletterForm() {
        const form = document.getElementById('newsletter-form');
        const emailInput = document.getElementById('newsletter-email');
        const submitButton = document.getElementById('newsletter-submit');
        const messageDiv = document.getElementById('newsletter-message');

        if (!form || !emailInput || !submitButton || !messageDiv) {
            console.warn('Newsletter form elements not found');
            return;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitToHubSpot(emailInput, submitButton, messageDiv);
        });
    }

    function submitToHubSpot(emailInput, submitButton, messageDiv) {
        const email = emailInput.value.trim();

        // Validate email
        if (!email || !isValidEmail(email)) {
            showMessage(messageDiv, 'Please enter a valid email address.', 'error');
            return;
        }

        // Disable form during submission
        setFormState(emailInput, submitButton, true);
        hideMessage(messageDiv);

        // Build HubSpot submission payload
        const payload = {
            fields: [
                {
                    name: 'email',
                    value: email
                }
            ],
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
            // Success
            showMessage(messageDiv, 'Thank you for subscribing!', 'success');
            emailInput.value = ''; // Clear the form
            setFormState(emailInput, submitButton, false);
        })
        .catch(function(error) {
            // Error
            console.error('HubSpot submission error:', error);
            showMessage(messageDiv, 'Something went wrong. Please try again.', 'error');
            setFormState(emailInput, submitButton, false);
        });
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function setFormState(emailInput, submitButton, isSubmitting) {
        emailInput.disabled = isSubmitting;
        submitButton.disabled = isSubmitting;
        submitButton.textContent = isSubmitting ? 'Subscribing...' : 'Subscribe';
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
