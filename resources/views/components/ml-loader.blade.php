<div id="ml-loader" class="hidden fixed inset-0 bg-gray-900 bg-opacity-40 flex items-center justify-center z-50">
    <div class="flex flex-col items-center w-80">
        <svg class="animate-spin h-12 w-12 text-indigo-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        <span class="text-white text-lg font-semibold mb-2">Calculating ML Matches...</span>
        <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
            <div id="ml-progress-bar" class="bg-indigo-500 h-4 rounded-full transition-all duration-200" style="width: 0%"></div>
        </div>
        <span id="ml-progress-text" class="text-white text-sm">0%</span>
    </div>
</div>
<script>
    let mlProgressInterval = null;
    function showMLLoader() {
        document.getElementById('ml-loader').classList.remove('hidden');
        let progress = 0;
        document.getElementById('ml-progress-bar').style.width = '0%';
        document.getElementById('ml-progress-text').innerText = '0%';
        mlProgressInterval = setInterval(function() {
            if (progress < 90) {
                progress += Math.floor(Math.random() * 5) + 2; // Simulate progress
                if (progress > 90) progress = 90;
                document.getElementById('ml-progress-bar').style.width = progress + '%';
                document.getElementById('ml-progress-text').innerText = progress + '%';
            }
        }, 300);
    }
    function hideMLLoader() {
        document.getElementById('ml-loader').classList.add('hidden');
        if (mlProgressInterval) {
            clearInterval(mlProgressInterval);
            mlProgressInterval = null;
        }
        document.getElementById('ml-progress-bar').style.width = '100%';
        document.getElementById('ml-progress-text').innerText = '100%';
    }
</script>
