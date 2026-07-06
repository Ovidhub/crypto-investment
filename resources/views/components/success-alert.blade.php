<div>
    @if(Session::has('success'))
    <div class="mb-6 animate-in slide-in-from-top duration-300">
        <div class="dark:bg-dark-50 bg-light-100 border-l-4 border-secondary rounded-lg overflow-hidden shadow-lg backdrop-blur-sm bg-opacity-90 dark:bg-opacity-90">
            <div class="p-4 flex items-start">
                <div class="flex-shrink-0 mr-3">
                    <div class="h-9 w-9 rounded-full bg-secondary/10 flex items-center justify-center">
                        <i class="fas fa-check-circle text-secondary"></i>
                    </div>
                </div>
                <div class="flex-1 mr-2">
                    <h4 class="dark:text-white text-dark text-sm font-medium mb-1">Success</h4>
                    <div class="dark:text-gray-300 text-gray-700 text-sm">
                        {{ Session::get('success') }}
                    </div>
                </div>
                <button type="button" class="dark:text-gray-400 text-gray-600 hover:text-dark dark:hover:text-white transition-colors" onclick="this.parentElement.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>