@props([
    'action' => '',
    'method' => 'POST',
    'autoSave' => false,
    'autoSaveInterval' => 30000,
    'validateOnBlur' => true,
    'showProgress' => false
])

<div x-data="enhancedForm({
    action: '{{ $action }}',
    method: '{{ $method }}',
    autoSave: {{ $autoSave ? 'true' : 'false' }},
    autoSaveInterval: {{ $autoSaveInterval }},
    validateOnBlur: {{ $validateOnBlur ? 'true' : 'false' }},
    showProgress: {{ $showProgress ? 'true' : 'false' }}
})" x-init="init()">
    
    <!-- Progress Bar -->
    <div x-show="showProgress && totalFields > 0" class="mb-6">
        <div class="flex justify-between text-sm text-gray-600 mb-2">
            <span>Form Progress</span>
            <span x-text="`${Math.round(progress)}% Complete`"></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div 
                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                :style="`width: ${progress}%`"
            ></div>
        </div>
    </div>
    
    <!-- Auto-save Indicator -->
    <div x-show="autoSave" class="mb-4">
        <div class="flex items-center text-sm">
            <div x-show="saving" class="flex items-center text-blue-600">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Saving...
            </div>
            <div x-show="!saving && lastSaved" class="text-gray-500">
                Last saved: <span x-text="lastSaved"></span>
            </div>
            <div x-show="!saving && !lastSaved && hasChanges" class="text-yellow-600">
                Unsaved changes
            </div>
        </div>
    </div>
    
    <!-- Form -->
    <form 
        @submit="handleSubmit"
        @input="handleInput"
        @change="handleChange"
        :action="action"
        :method="method === 'GET' ? 'GET' : 'POST'"
        class="space-y-6"
    >
        @if($method !== 'GET' && $method !== 'POST')
            @method($method)
        @endif
        @csrf
        
        <!-- Form Fields Slot -->
        {{ $slot }}
        
        <!-- Validation Summary -->
        <div x-show="Object.keys(errors).length > 0" class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <template x-for="(error, field) in errors" :key="field">
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function enhancedForm(config) {
    return {
        action: config.action,
        method: config.method,
        autoSave: config.autoSave,
        autoSaveInterval: config.autoSaveInterval,
        validateOnBlur: config.validateOnBlur,
        showProgress: config.showProgress,
        
        // State
        formData: {},
        errors: {},
        saving: false,
        hasChanges: false,
        lastSaved: null,
        totalFields: 0,
        completedFields: 0,
        autoSaveTimer: null,
        
        get progress() {
            return this.totalFields > 0 ? (this.completedFields / this.totalFields) * 100 : 0;
        },
        
        init() {
            this.initializeForm();
            this.loadSavedData();
            
            if (this.autoSave) {
                this.startAutoSave();
            }
            
            // Handle page unload
            window.addEventListener('beforeunload', (e) => {
                if (this.hasChanges && !this.saving) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                }
            });
        },
        
        initializeForm() {
            // Count total form fields
            const form = this.$el.querySelector('form');
            const inputs = form.querySelectorAll('input, select, textarea');
            this.totalFields = inputs.length;
            
            // Initialize form data
            inputs.forEach(input => {
                if (input.name) {
                    this.formData[input.name] = input.value || '';
                }
            });
            
            this.updateProgress();
        },
        
        handleInput(event) {
            const { name, value } = event.target;
            if (name) {
                this.formData[name] = value;
                this.hasChanges = true;
                this.updateProgress();
                
                // Clear field error on input
                if (this.errors[name]) {
                    delete this.errors[name];
                }
            }
        },
        
        handleChange(event) {
            if (this.validateOnBlur) {
                this.validateField(event.target);
            }
        },
        
        handleSubmit(event) {
            event.preventDefault();
            
            if (this.validateForm()) {
                this.submitForm();
            }
        },
        
        validateField(field) {
            const { name, value, required, type, pattern, min, max } = field;
            
            if (!name) return true;
            
            let isValid = true;
            let errorMessage = '';
            
            // Required validation
            if (required && !value.trim()) {
                isValid = false;
                errorMessage = `${this.getFieldLabel(field)} is required.`;
            }
            
            // Type validation
            if (value && type === 'email' && !this.isValidEmail(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address.';
            }
            
            // Pattern validation
            if (value && pattern && !new RegExp(pattern).test(value)) {
                isValid = false;
                errorMessage = `${this.getFieldLabel(field)} format is invalid.`;
            }
            
            // Length validation
            if (value && min && value.length < parseInt(min)) {
                isValid = false;
                errorMessage = `${this.getFieldLabel(field)} must be at least ${min} characters.`;
            }
            
            if (value && max && value.length > parseInt(max)) {
                isValid = false;
                errorMessage = `${this.getFieldLabel(field)} must not exceed ${max} characters.`;
            }
            
            if (isValid) {
                delete this.errors[name];
            } else {
                this.errors[name] = errorMessage;
            }
            
            return isValid;
        },
        
        validateForm() {
            const form = this.$el.querySelector('form');
            const inputs = form.querySelectorAll('input, select, textarea');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!this.validateField(input)) {
                    isValid = false;
                }
            });
            
            return isValid;
        },
        
        async submitForm() {
            const form = this.$el.querySelector('form');
            const formData = new FormData(form);
            
            try {
                this.saving = true;
                
                const response = await fetch(this.action, {
                    method: this.method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                if (response.ok) {
                    this.hasChanges = false;
                    this.clearSavedData();
                    window.showNotification('Form submitted successfully!', 'success');
                    
                    // Handle redirect or success response
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    } else {
                        // If not JSON, assume redirect
                        window.location.href = response.url;
                    }
                } else {
                    const errorData = await response.json();
                    this.errors = errorData.errors || {};
                    window.showNotification('Please correct the errors and try again.', 'error');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                window.showNotification('An error occurred. Please try again.', 'error');
            } finally {
                this.saving = false;
            }
        },
        
        async autoSaveForm() {
            if (!this.hasChanges || this.saving) return;
            
            try {
                this.saving = true;
                
                const response = await fetch(`${this.action}/auto-save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(this.formData)
                });
                
                if (response.ok) {
                    this.lastSaved = new Date().toLocaleTimeString();
                    this.saveDataLocally();
                }
            } catch (error) {
                console.error('Auto-save error:', error);
            } finally {
                this.saving = false;
            }
        },
        
        startAutoSave() {
            this.autoSaveTimer = setInterval(() => {
                this.autoSaveForm();
            }, this.autoSaveInterval);
        },
        
        updateProgress() {
            const form = this.$el.querySelector('form');
            const inputs = form.querySelectorAll('input, select, textarea');
            let completed = 0;
            
            inputs.forEach(input => {
                if (input.value && input.value.trim()) {
                    completed++;
                }
            });
            
            this.completedFields = completed;
        },
        
        saveDataLocally() {
            const key = `form_data_${window.location.pathname}`;
            localStorage.setItem(key, JSON.stringify(this.formData));
        },
        
        loadSavedData() {
            const key = `form_data_${window.location.pathname}`;
            const saved = localStorage.getItem(key);
            
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    Object.keys(data).forEach(name => {
                        const input = this.$el.querySelector(`[name="${name}"]`);
                        if (input && !input.value) {
                            input.value = data[name];
                            this.formData[name] = data[name];
                        }
                    });
                    this.updateProgress();
                } catch (error) {
                    console.error('Error loading saved data:', error);
                }
            }
        },
        
        clearSavedData() {
            const key = `form_data_${window.location.pathname}`;
            localStorage.removeItem(key);
        },
        
        getFieldLabel(field) {
            const label = field.closest('.form-group')?.querySelector('label');
            return label ? label.textContent.replace('*', '').trim() : field.name;
        },
        
        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
    }
}
</script>
