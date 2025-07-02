@props([
    'name' => 'confirm',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmClass' => 'bg-red-600 hover:bg-red-700 text-white',
    'cancelClass' => 'bg-gray-300 hover:bg-gray-400 text-gray-700',
    'icon' => 'warning',
    'persistent' => true
])

<x-modal :name="$name" :persistent="$persistent" :show-header="false" max-width="md">
    <div x-data="confirmationModal()" class="p-6">
        <!-- Icon -->
        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full {{ $icon === 'warning' ? 'bg-yellow-100' : ($icon === 'danger' ? 'bg-red-100' : 'bg-blue-100') }}">
            @if($icon === 'warning')
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            @elseif($icon === 'danger')
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @else
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @endif
        </div>
        
        <!-- Title -->
        <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">{{ $title }}</h3>
        
        <!-- Message -->
        <p class="text-gray-600 text-center mb-6">{{ $message }}</p>
        
        <!-- Custom Content Slot -->
        @if(isset($content))
            <div class="mb-6">
                {{ $content }}
            </div>
        @endif
        
        <!-- Actions -->
        <div class="flex space-x-3 justify-center">
            <button 
                @click="$dispatch('close-modal', '{{ $name }}')"
                class="px-4 py-2 rounded-lg font-medium transition duration-150 {{ $cancelClass }}"
            >
                {{ $cancelText }}
            </button>
            
            <button 
                @click="confirm()"
                class="px-4 py-2 rounded-lg font-medium transition duration-150 {{ $confirmClass }}"
            >
                {{ $confirmText }}
            </button>
        </div>
    </div>
</x-modal>

<script>
function confirmationModal() {
    return {
        confirm() {
            // Dispatch confirmation event
            this.$dispatch('confirmed', { modal: '{{ $name }}' });
            this.$dispatch('close-modal', '{{ $name }}');
        }
    }
}

// Global confirmation function
window.showConfirmation = function(options = {}) {
    const defaults = {
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        icon: 'warning',
        onConfirm: null,
        onCancel: null
    };
    
    const config = { ...defaults, ...options };
    
    // Create modal HTML
    const modalId = 'dynamic-confirm-' + Date.now();
    const iconClass = config.icon === 'warning' ? 'bg-yellow-100' : (config.icon === 'danger' ? 'bg-red-100' : 'bg-blue-100');
    const iconColor = config.icon === 'warning' ? 'text-yellow-600' : (config.icon === 'danger' ? 'text-red-600' : 'text-blue-600');
    const confirmClass = config.icon === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white';
    
    let iconSvg = '';
    if (config.icon === 'warning') {
        iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>';
    } else if (config.icon === 'danger') {
        iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    } else {
        iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    }
    
    const modalHtml = `
        <div x-data="{
            show: false,
            focusables() {
                let selector = 'a, button, input:not([type=\\'hidden\\']), textarea, select, details, [tabindex]:not([tabindex=\\'-1\\'])'
                return [...$el.querySelectorAll(selector)].filter(el => ! el.hasAttribute('disabled'))
            },
            firstFocusable() { return this.focusables()[0] },
            lastFocusable() { return this.focusables().slice(-1)[0] },
            nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
            prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
            nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
            prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
        }"
        x-init="show = true; setTimeout(() => firstFocusable().focus(), 100)"
        x-on:keydown.escape.window="show = false"
        x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
        x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
        x-show="show"
        class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
            
            <div x-show="show" class="fixed inset-0 transform transition-all"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div x-show="show"
                class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.stop>
                
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full ${iconClass}">
                        <svg class="w-6 h-6 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${iconSvg}
                        </svg>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">${config.title}</h3>
                    <p class="text-gray-600 text-center mb-6">${config.message}</p>
                    
                    <div class="flex space-x-3 justify-center">
                        <button @click="show = false; setTimeout(() => $el.closest('.fixed').remove(), 300)"
                            class="px-4 py-2 rounded-lg font-medium transition duration-150 bg-gray-300 hover:bg-gray-400 text-gray-700">
                            ${config.cancelText}
                        </button>
                        
                        <button @click="show = false; setTimeout(() => { $el.closest('.fixed').remove(); }, 300)"
                            class="px-4 py-2 rounded-lg font-medium transition duration-150 ${confirmClass}"
                            x-init="$el.addEventListener('click', () => { if (typeof window.confirmCallback === 'function') window.confirmCallback(); })">
                            ${config.confirmText}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Set callback
    window.confirmCallback = config.onConfirm;
    
    // Add to DOM
    const container = document.createElement('div');
    container.innerHTML = modalHtml;
    document.body.appendChild(container.firstElementChild);
    
    return new Promise((resolve) => {
        const originalCallback = window.confirmCallback;
        window.confirmCallback = () => {
            if (originalCallback) originalCallback();
            resolve(true);
        };
        
        // Handle cancel/close
        setTimeout(() => {
            const modal = document.querySelector('.fixed.inset-0.z-50');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        if (config.onCancel) config.onCancel();
                        resolve(false);
                    }
                });
            }
        }, 100);
    });
};
</script>
