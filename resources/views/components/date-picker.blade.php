@props([
    'name' => 'date',
    'id' => null,
    'value' => '',
    'placeholder' => 'Select date',
    'required' => false,
    'minDate' => null,
    'maxDate' => null,
    'format' => 'Y-m-d',
    'displayFormat' => 'F j, Y'
])

@php
    $id = $id ?? $name;
    $minDate = $minDate ?? date('Y-m-d');
    $maxDate = $maxDate ?? date('Y-m-d', strtotime('+1 year'));
@endphp

<div x-data="datePicker({
    name: '{{ $name }}',
    value: '{{ $value }}',
    minDate: '{{ $minDate }}',
    maxDate: '{{ $maxDate }}',
    format: '{{ $format }}',
    displayFormat: '{{ $displayFormat }}'
})" class="relative">
    <!-- Hidden Input -->
    <input 
        type="hidden" 
        name="{{ $name }}" 
        :value="selectedDate"
        {{ $required ? 'required' : '' }}
    >
    
    <!-- Display Input -->
    <div class="relative">
        <input 
            type="text" 
            id="{{ $id }}"
            @click="toggleCalendar()"
            @keydown.escape="closeCalendar()"
            :value="displayValue"
            placeholder="{{ $placeholder }}"
            readonly
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-pointer pr-10"
            {{ $required ? 'required' : '' }}
        >
        
        <!-- Calendar Icon -->
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
    </div>
    
    <!-- Calendar Dropdown -->
    <div 
        x-show="isOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.away="closeCalendar()"
        class="absolute z-50 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg p-4 w-80"
        style="display: none;"
    >
        <!-- Calendar Header -->
        <div class="flex items-center justify-between mb-4">
            <button 
                type="button"
                @click="previousMonth()"
                class="p-1 hover:bg-gray-100 rounded"
            >
                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <h3 class="text-lg font-semibold text-gray-900" x-text="currentMonthYear"></h3>
            
            <button 
                type="button"
                @click="nextMonth()"
                class="p-1 hover:bg-gray-100 rounded"
            >
                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
        
        <!-- Days of Week -->
        <div class="grid grid-cols-7 gap-1 mb-2">
            <template x-for="day in daysOfWeek" :key="day">
                <div class="text-center text-sm font-medium text-gray-500 py-2" x-text="day"></div>
            </template>
        </div>
        
        <!-- Calendar Days -->
        <div class="grid grid-cols-7 gap-1">
            <template x-for="date in calendarDays" :key="date.dateString">
                <button 
                    type="button"
                    @click="selectDate(date)"
                    :disabled="!date.isCurrentMonth || !date.isSelectable"
                    :class="{
                        'bg-blue-600 text-white': date.isSelected,
                        'bg-blue-100 text-blue-600': date.isToday && !date.isSelected,
                        'text-gray-400': !date.isCurrentMonth,
                        'text-gray-300 cursor-not-allowed': !date.isSelectable,
                        'hover:bg-gray-100': date.isCurrentMonth && date.isSelectable && !date.isSelected,
                        'text-gray-900': date.isCurrentMonth && date.isSelectable
                    }"
                    class="h-8 w-8 text-sm rounded transition-colors duration-150"
                    x-text="date.day"
                >
                </button>
            </template>
        </div>
        
        <!-- Quick Actions -->
        <div class="flex justify-between mt-4 pt-4 border-t border-gray-200">
            <button 
                type="button"
                @click="selectToday()"
                class="text-sm text-blue-600 hover:text-blue-700 font-medium"
            >
                Today
            </button>
            <button 
                type="button"
                @click="clearDate()"
                class="text-sm text-gray-600 hover:text-gray-700 font-medium"
            >
                Clear
            </button>
        </div>
    </div>
</div>

<script>
function datePicker(config) {
    return {
        isOpen: false,
        selectedDate: config.value || '',
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        minDate: new Date(config.minDate),
        maxDate: new Date(config.maxDate),
        daysOfWeek: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        
        get displayValue() {
            if (!this.selectedDate) return '';
            const date = new Date(this.selectedDate);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        
        get currentMonthYear() {
            const date = new Date(this.currentYear, this.currentMonth);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long'
            });
        },
        
        get calendarDays() {
            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());
            
            const days = [];
            const currentDate = new Date(startDate);
            
            for (let i = 0; i < 42; i++) {
                const dateString = currentDate.toISOString().split('T')[0];
                const isCurrentMonth = currentDate.getMonth() === this.currentMonth;
                const isToday = dateString === new Date().toISOString().split('T')[0];
                const isSelected = dateString === this.selectedDate;
                const isSelectable = currentDate >= this.minDate && currentDate <= this.maxDate;
                
                days.push({
                    day: currentDate.getDate(),
                    dateString: dateString,
                    isCurrentMonth: isCurrentMonth,
                    isToday: isToday,
                    isSelected: isSelected,
                    isSelectable: isSelectable
                });
                
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            return days;
        },
        
        toggleCalendar() {
            this.isOpen = !this.isOpen;
        },
        
        closeCalendar() {
            this.isOpen = false;
        },
        
        selectDate(date) {
            if (!date.isSelectable) return;
            this.selectedDate = date.dateString;
            this.closeCalendar();
        },
        
        selectToday() {
            const today = new Date().toISOString().split('T')[0];
            const todayDate = new Date(today);
            if (todayDate >= this.minDate && todayDate <= this.maxDate) {
                this.selectedDate = today;
                this.closeCalendar();
            }
        },
        
        clearDate() {
            this.selectedDate = '';
            this.closeCalendar();
        },
        
        previousMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
        },
        
        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
        }
    }
}
</script>
