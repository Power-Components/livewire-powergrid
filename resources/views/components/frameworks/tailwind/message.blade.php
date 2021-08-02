<div class="message pb-2 dark:bg-gray-800">
    @if(session()->has('success') || session()->has('error'))
    <div
        class="mb-2 border px-4 py-3 rounded relative
        @if (session()->has('success'))
            bg-green-100 border-green-600 text-green-700 dark:bg-green-300 dark:text-green-900
        @elseif (session()->has('error'))
            bg-red-100 border border-red-600 text-red-700 dark:bg-red-300 dark:text-red-900
        @endif" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg onclick="document.getElementsByClassName('message')[0].style.display = 'none'"
                 class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 20 20"><title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </span>
    </div>
    @endif
</div>


