/**
 * Common Datepicker Initialization Script
 * Works with existing HTML without modifications
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize datepickers with a slight delay to ensure DOM is fully loaded
    setTimeout(() => {
        initializeDatepickers();
        setupUniversalClickHandlers();
        enhanceExistingDateInputs();
    }, 100);
});

/**
 * Enhance all existing date inputs
 */
function enhanceExistingDateInputs() {
    // Find all date inputs in the document
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Skip if already enhanced
        if (input.hasAttribute('data-enhanced-datepicker')) return;
        
        // Mark as enhanced
        input.setAttribute('data-enhanced-datepicker', 'true');
        
        // Add custom class
        input.classList.add('enhanced-date-input');
        
        // Ensure input has an ID for label association
        if (!input.id) {
            input.id = 'date-' + Math.random().toString(36).substr(2, 9);
        }
        
        // Add wrapper if not already wrapped
        if (!input.parentElement.classList.contains('date-field-container')) {
            wrapDateInput(input);
        }
        
        // Add calendar icon
        addCalendarIcon(input);
    });
    
    // Also enhance date inputs added dynamically
    setupMutationObserver();
}

/**
 * Wrap date input in a container for better click handling
 */
function wrapDateInput(input) {
    const wrapper = document.createElement('div');
    wrapper.className = 'date-field-container';
    wrapper.style.position = 'relative';
    wrapper.style.display = 'inline-block';
    wrapper.style.width = '100%';
    
    // Insert wrapper and move input into it
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    
    // Add data attribute for identification
    wrapper.setAttribute('data-date-wrapper', 'true');
}

/**
 * Add calendar icon to date inputs
 */
function addCalendarIcon(input) {
    // Check if icon already exists
    if (input.parentElement.querySelector('.calendar-click-icon')) return;
    
    // Create icon
    const icon = document.createElement('span');
    icon.className = 'calendar-click-icon';
    icon.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
        </svg>
    `;
    
    // Style the icon
    icon.style.cssText = `
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
        pointer-events: auto;
        background: white;
        padding: 5px;
        border-radius: 3px;
    `;
    
    input.parentElement.appendChild(icon);
    
    // Make input have space for icon
    input.style.paddingRight = '35px';
    input.style.cursor = 'pointer';
    input.style.backgroundColor = 'white';
}

/**
 * Setup click handlers that work with existing HTML
 */
function setupUniversalClickHandlers() {
    // Click handler for calendar icons
    document.addEventListener('click', function(e) {
        // Handle clicks on calendar icons
        if (e.target.closest('.calendar-click-icon')) {
            e.preventDefault();
            e.stopPropagation();
            
            const icon = e.target.closest('.calendar-click-icon');
            const input = icon.parentElement.querySelector('input[type="date"]');
            
            if (input) {
                openDatePicker(input);
            }
        }
        
        // Handle clicks on date input containers
        const dateWrapper = e.target.closest('.date-field-container');
        if (dateWrapper && !e.target.matches('input[type="date"]')) {
            e.preventDefault();
            e.stopPropagation();
            
            const input = dateWrapper.querySelector('input[type="date"]');
            if (input) {
                openDatePicker(input);
            }
        }
        
        // Handle clicks on labels that might be associated with date inputs
        if (e.target.matches('label') || e.target.closest('label')) {
            const label = e.target.matches('label') ? e.target : e.target.closest('label');
            const labelFor = label.getAttribute('for');
            
            if (labelFor) {
                // Find the input this label is for
                const input = document.getElementById(labelFor);
                if (input && input.type === 'date') {
                    e.preventDefault();
                    e.stopPropagation();
                    openDatePicker(input);
                }
            }
            
            // Alternative: Find nearest date input
            const nearestDateInput = label.closest('.form-group, .mb-3, .input-group, .col-md-6')?.querySelector('input[type="date"]');
            if (nearestDateInput) {
                e.preventDefault();
                e.stopPropagation();
                openDatePicker(nearestDateInput);
            }
        }
        
        // Handle clicks on text near date inputs (common in forms)
        const clickedText = e.target;
        if (clickedText.nodeType === 3 || clickedText.tagName === 'SPAN' || clickedText.tagName === 'DIV') {
            // Check if near a date input
            const parentContainer = clickedText.closest('.form-control, .input-group, .mb-3, .col-md-6');
            if (parentContainer) {
                const dateInput = parentContainer.querySelector('input[type="date"]');
                if (dateInput && !dateInput.contains(e.target)) {
                    e.preventDefault();
                    e.stopPropagation();
                    openDatePicker(dateInput);
                }
            }
        }
    });
    
    // Also handle double-click on the date field area
    document.addEventListener('dblclick', function(e) {
        const dateWrapper = e.target.closest('.date-field-container');
        if (dateWrapper) {
            e.preventDefault();
            e.stopPropagation();
            const input = dateWrapper.querySelector('input[type="date"]');
            if (input) openDatePicker(input);
        }
    });
}

/**
 * Open the datepicker
 */
function openDatePicker(input) {
    if (!input) return;
    
    // Focus first
    input.focus();
    
    // Try native showPicker method
    if (typeof input.showPicker === 'function') {
        input.showPicker();
    } else {
        // Fallback: trigger click or create a custom datepicker
        input.click();
        
        // For browsers that don't support showPicker
        setTimeout(() => {
            if (document.activeElement !== input) {
                input.click();
            }
        }, 50);
    }
}

/**
 * Initialize datepickers
 */
function initializeDatepickers() {
    // Hide default calendar picker icon (showing ours instead)
    const style = document.createElement('style');
    style.textContent = `
        .enhanced-date-input::-webkit-calendar-picker-indicator {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            cursor: pointer;
            z-index: 5;
        }
        
        .enhanced-date-input {
            cursor: pointer !important;
            background-color: white !important;
        }
        
        /* Make form labels clickable */
        .form-label {
            cursor: pointer;
        }
        
        /* Date field hover effect */
        .date-field-container:hover {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    `;
    document.head.appendChild(style);
}

/**
 * Observe DOM for dynamically added date inputs
 */
function setupMutationObserver() {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        const dateInputs = node.querySelectorAll ? 
                            node.querySelectorAll('input[type="date"]') : [];
                        
                        if (node.matches && node.matches('input[type="date"]')) {
                            dateInputs.push(node);
                        }
                        
                        dateInputs.forEach(input => {
                            if (!input.hasAttribute('data-enhanced-datepicker')) {
                                enhanceExistingDateInputs();
                            }
                        });
                    }
                });
            }
        });
    });
    
    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

/**
 * Make function available globally for manual triggers
 */
window.enhanceAllDatePickers = function() {
    enhanceExistingDateInputs();
    setupUniversalClickHandlers();
};

// Initialize on window load as well
window.addEventListener('load', function() {
    setTimeout(enhanceExistingDateInputs, 200);
});