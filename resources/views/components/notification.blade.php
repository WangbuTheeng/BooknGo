@props([
    'type' => 'info',
    'title' => '',
    'message' => '',
    'duration' => 5000,
    'position' => 'top-right'
])

<div x-data="notification({
    type: '{{ $type }}',
    title: '{{ $title }}',
    message: '{{ $message }}',
    duration: {{ $duration }},
    position: '{{ $position }}'
})" x-init="init()">
    <!-- Notification Container -->
    <div 
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-x-full"
        :class="positionClasses"
        class="fixed z-50 max-w-sm w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden"
        style="display: none;"
    >
        <div :class="typeClasses" class="p-4">
            <div class="flex items-start">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <!-- Success Icon -->
                    <svg x-show="type === 'success'" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    
                    <!-- Error Icon -->
                    <svg x-show="type === 'error'" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    
                    <!-- Warning Icon -->
                    <svg x-show="type === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    
                    <!-- Info Icon -->
                    <svg x-show="type === 'info'" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <!-- Content -->
                <div class="ml-3 w-0 flex-1">
                    <p x-show="title" x-text="title" class="text-sm font-medium"></p>
                    <p x-text="message" :class="title ? 'mt-1 text-sm' : 'text-sm font-medium'"></p>
                </div>
                
                <!-- Close Button -->
                <div class="ml-4 flex-shrink-0 flex">
                    <button 
                        @click="hide()"
                        class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div x-show="duration > 0" class="mt-3">
                <div class="bg-black bg-opacity-20 rounded-full h-1">
                    <div 
                        class="bg-white rounded-full h-1 transition-all ease-linear"
                        :style="`width: ${progress}%; transition-duration: ${duration}ms;`"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function notification(config) {
    return {
        show: false,
        type: config.type,
        title: config.title,
        message: config.message,
        duration: config.duration,
        position: config.position,
        progress: 100,
        timer: null,
        
        get positionClasses() {
            const positions = {
                'top-right': 'top-4 right-4',
                'top-left': 'top-4 left-4',
                'top-center': 'top-4 left-1/2 transform -translate-x-1/2',
                'bottom-right': 'bottom-4 right-4',
                'bottom-left': 'bottom-4 left-4',
                'bottom-center': 'bottom-4 left-1/2 transform -translate-x-1/2'
            };
            return positions[this.position] || positions['top-right'];
        },
        
        get typeClasses() {
            const types = {
                'success': 'bg-green-500 text-white',
                'error': 'bg-red-500 text-white',
                'warning': 'bg-yellow-500 text-white',
                'info': 'bg-blue-500 text-white'
            };
            return types[this.type] || types['info'];
        },
        
        init() {
            this.show = true;
            
            if (this.duration > 0) {
                this.startTimer();
            }
        },
        
        startTimer() {
            this.progress = 100;
            
            // Start progress animation
            this.$nextTick(() => {
                this.progress = 0;
            });
            
            // Auto hide after duration
            this.timer = setTimeout(() => {
                this.hide();
            }, this.duration);
        },
        
        hide() {
            if (this.timer) {
                clearTimeout(this.timer);
            }
            this.show = false;
            
            // Remove element after transition
            setTimeout(() => {
                this.$el.remove();
            }, 300);
        }
    }
}

// Global notification function
window.showNotification = function(message, type = 'info', title = '', duration = 5000, position = 'top-right') {
    const notificationHtml = `
        <div x-data="notification({
            type: '${type}',
            title: '${title}',
            message: '${message}',
            duration: ${duration},
            position: '${position}'
        })" x-init="init()">
            <div 
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-full"
                :class="positionClasses"
                class="fixed z-50 max-w-sm w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden"
                style="display: none;"
            >
                <div :class="typeClasses" class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg x-show="type === 'success'" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <svg x-show="type === 'error'" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <svg x-show="type === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <svg x-show="type === 'info'" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p x-show="title" x-text="title" class="text-sm font-medium"></p>
                            <p x-text="message" :class="title ? 'mt-1 text-sm' : 'text-sm font-medium'"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="hide()" class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div x-show="duration > 0" class="mt-3">
                        <div class="bg-black bg-opacity-20 rounded-full h-1">
                            <div class="bg-white rounded-full h-1 transition-all ease-linear" :style="\`width: \${progress}%; transition-duration: \${duration}ms;\`"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const container = document.createElement('div');
    container.innerHTML = notificationHtml;
    document.body.appendChild(container.firstElementChild);
};
</script>
