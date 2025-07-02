@props([
    'endpoint' => '',
    'interval' => 30000,
    'autoStart' => true,
    'showStatus' => true
])

<div x-data="realTimeUpdates({
    endpoint: '{{ $endpoint }}',
    interval: {{ $interval }},
    autoStart: {{ $autoStart ? 'true' : 'false' }},
    showStatus: {{ $showStatus ? 'true' : 'false' }}
})" x-init="init()">
    
    <!-- Status Indicator -->
    <div x-show="showStatus" class="flex items-center space-x-2 text-sm mb-4">
        <div class="flex items-center">
            <div 
                :class="{
                    'bg-green-500': status === 'connected',
                    'bg-yellow-500': status === 'connecting',
                    'bg-red-500': status === 'disconnected'
                }"
                class="w-2 h-2 rounded-full mr-2"
            ></div>
            <span x-text="statusText" class="text-gray-600"></span>
        </div>
        
        <div x-show="lastUpdate" class="text-gray-500">
            Last updated: <span x-text="lastUpdate"></span>
        </div>
        
        <!-- Manual Refresh Button -->
        <button 
            @click="fetchUpdate()"
            :disabled="loading"
            class="text-blue-600 hover:text-blue-700 disabled:opacity-50"
        >
            <svg 
                :class="{ 'animate-spin': loading }"
                class="w-4 h-4" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </button>
    </div>
    
    <!-- Content Slot -->
    <div>
        {{ $slot }}
    </div>
    
    <!-- Update Notifications -->
    <div x-show="hasUpdates" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-blue-800 text-sm font-medium">New updates available</span>
            </div>
            <button 
                @click="applyUpdates()"
                class="text-blue-600 hover:text-blue-700 text-sm font-medium"
            >
                Apply Updates
            </button>
        </div>
    </div>
</div>

<script>
function realTimeUpdates(config) {
    return {
        endpoint: config.endpoint,
        interval: config.interval,
        autoStart: config.autoStart,
        showStatus: config.showStatus,
        
        // State
        status: 'disconnected',
        loading: false,
        lastUpdate: null,
        hasUpdates: false,
        updateTimer: null,
        retryCount: 0,
        maxRetries: 3,
        data: {},
        pendingUpdates: {},
        
        // Event callbacks
        onUpdate: null,
        onError: null,
        onConnect: null,
        onDisconnect: null,
        
        get statusText() {
            const texts = {
                'connected': 'Live updates active',
                'connecting': 'Connecting...',
                'disconnected': 'Updates paused'
            };
            return texts[this.status] || 'Unknown status';
        },
        
        init() {
            if (this.autoStart) {
                this.start();
            }
            
            // Handle visibility change
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.pause();
                } else {
                    this.resume();
                }
            });
            
            // Handle online/offline
            window.addEventListener('online', () => this.resume());
            window.addEventListener('offline', () => this.pause());
        },
        
        start() {
            if (this.updateTimer) {
                clearInterval(this.updateTimer);
            }
            
            this.status = 'connecting';
            this.fetchUpdate();
            
            this.updateTimer = setInterval(() => {
                this.fetchUpdate();
            }, this.interval);
        },
        
        pause() {
            if (this.updateTimer) {
                clearInterval(this.updateTimer);
                this.updateTimer = null;
            }
            this.status = 'disconnected';
        },
        
        resume() {
            if (!this.updateTimer && navigator.onLine) {
                this.start();
            }
        },
        
        stop() {
            this.pause();
            this.status = 'disconnected';
        },
        
        async fetchUpdate() {
            if (this.loading || !navigator.onLine) return;
            
            try {
                this.loading = true;
                this.status = 'connecting';
                
                const response = await fetch(this.endpoint, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const newData = await response.json();
                
                // Check for changes
                if (this.hasDataChanged(newData)) {
                    this.pendingUpdates = newData;
                    this.hasUpdates = true;
                    
                    // Auto-apply updates if no user interaction is needed
                    if (this.shouldAutoApply(newData)) {
                        this.applyUpdates();
                    }
                }
                
                this.status = 'connected';
                this.lastUpdate = new Date().toLocaleTimeString();
                this.retryCount = 0;
                
                // Trigger callback
                if (this.onUpdate) {
                    this.onUpdate(newData);
                }
                
            } catch (error) {
                console.error('Real-time update error:', error);
                this.handleError(error);
            } finally {
                this.loading = false;
            }
        },
        
        hasDataChanged(newData) {
            return JSON.stringify(this.data) !== JSON.stringify(newData);
        },
        
        shouldAutoApply(newData) {
            // Auto-apply if it's just status updates or non-critical changes
            // Override this method for specific use cases
            return false;
        },
        
        applyUpdates() {
            this.data = { ...this.pendingUpdates };
            this.hasUpdates = false;
            this.pendingUpdates = {};
            
            // Dispatch custom event for components to listen to
            this.$dispatch('data-updated', this.data);
            
            window.showNotification('Updates applied successfully', 'success');
        },
        
        handleError(error) {
            this.retryCount++;
            
            if (this.retryCount <= this.maxRetries) {
                // Exponential backoff
                const delay = Math.pow(2, this.retryCount) * 1000;
                setTimeout(() => {
                    this.fetchUpdate();
                }, delay);
            } else {
                this.status = 'disconnected';
                
                if (this.onError) {
                    this.onError(error);
                }
                
                window.showNotification('Connection lost. Updates paused.', 'warning');
            }
        },
        
        // Public methods for external control
        setUpdateCallback(callback) {
            this.onUpdate = callback;
        },
        
        setErrorCallback(callback) {
            this.onError = callback;
        },
        
        setConnectCallback(callback) {
            this.onConnect = callback;
        },
        
        setDisconnectCallback(callback) {
            this.onDisconnect = callback;
        },
        
        getData() {
            return this.data;
        },
        
        setData(data) {
            this.data = data;
        },
        
        forceUpdate() {
            this.fetchUpdate();
        }
    }
}

// Global real-time update manager
window.RealTimeManager = {
    instances: new Map(),
    
    register(id, instance) {
        this.instances.set(id, instance);
    },
    
    unregister(id) {
        const instance = this.instances.get(id);
        if (instance) {
            instance.stop();
            this.instances.delete(id);
        }
    },
    
    pauseAll() {
        this.instances.forEach(instance => instance.pause());
    },
    
    resumeAll() {
        this.instances.forEach(instance => instance.resume());
    },
    
    stopAll() {
        this.instances.forEach(instance => instance.stop());
        this.instances.clear();
    }
};

// Handle page unload
window.addEventListener('beforeunload', () => {
    window.RealTimeManager.stopAll();
});
</script>
