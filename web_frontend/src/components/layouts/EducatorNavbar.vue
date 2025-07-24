<template>
  <header class="flex justify-between items-center py-4 px-8 bg-white shadow-sm">
    
      <!-- Logo -->
    <div class="flex items-center space-x-2">
      <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.042 12.042 0 0118 14.5C18 17.538 15.538 20 12.5 20S7 17.538 7 14.5c0-.697.096-1.374.273-2.017L12 14z" />
      </svg>
      <span class="text-lg font-semibold text-gray-800">LeaveMS</span>
    </div>

    <nav>
      <ul class="flex space-x-5">
        <li>
          <router-link
            to="/educator-dashboard"
            class="text-gray-600 font-medium pb-1.5 border-b-2 border-transparent hover:text-blue-600 transition-colors duration-200"
            active-class="text-blue-600 border-blue-600"
            exact-active-class="text-blue-600 border-blue-600"
          >
            Dashboard
          </router-link>
        </li>
        <li>
          <router-link
            to="/educator-history"
            class="text-gray-600 font-medium pb-1.5 border-b-2 border-transparent hover:text-blue-600 transition-colors duration-200"
            active-class="text-blue-600 border-blue-600"
            exact-active-class="text-blue-600 border-blue-600"
          >
            History
          </router-link>
        </li>
      </ul>
    </nav>
    <div class="flex items-center cursor-pointer">
      <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex justify-center items-center mr-2 font-bold text-sm">J</span>
      <span class="text-gray-700">Jane Smith</span>
      <i class="ml-1.5 border-solid border-gray-600 border-b-2 border-r-2 p-1 inline-block transform rotate-45"></i>
    </div>
  </header>
</template>
