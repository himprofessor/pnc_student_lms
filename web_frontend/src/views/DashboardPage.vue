<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
    <div class="px-6 py-6">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-2xl font-bold">Welcome back, John Doe</h1>
          <p class="text-sm text-gray-500">
            Manage your leave requests and track their status
          </p>
        </div>
        <button
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-1"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"
            ></path>
          </svg>
          <span>New Leave Request</span>
        </button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div
          class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3"
        >
          <div class="text-yellow-500 text-2xl p-2 bg-yellow-100 rounded-full">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              ></path>
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Pending</div>
            <div class="text-2xl font-bold">1</div>
          </div>
        </div>
        <div
          class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3"
        >
          <div class="text-green-500 text-2xl p-2 bg-green-100 rounded-full">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              ></path>
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Approved</div>
            <div class="text-2xl font-bold">1</div>
          </div>
        </div>
        <div
          class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3"
        >
          <div class="text-red-500 text-2xl p-2 bg-red-100 rounded-full">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
              ></path>
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Rejected</div>
            <div class="text-2xl font-bold">0</div>
          </div>
        </div>
        <div
          class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3"
        >
          <div class="text-blue-500 text-2xl p-2 bg-blue-100 rounded-full">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              ></path>
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Total</div>
            <div class="text-2xl font-bold">2</div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow border">
        <div class="border-b px-6 py-4">
          <h2 class="text-lg font-semibold">Recent Leave Requests</h2>
          <p class="text-sm text-gray-500">
            Your latest leave requests and their status
          </p>
        </div>

        <div class="divide-y">
          <div class="flex justify-between items-center px-6 py-4">
            <div class="flex items-center space-x-3">
              <svg
                class="w-6 h-6 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                ></path>
              </svg>
              <div>
                <div class="font-semibold">Sick Leave</div>
                <div class="text-sm text-gray-500">
                  Jan 15, 2024 - Jan 17, 2024
                </div>
                <div class="text-sm text-gray-500">Flu symptoms</div>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <span
                class="bg-yellow-100 text-yellow-700 text-xs font-medium px-2 py-1 rounded"
                >Pending</span
              >
              <button
                class="text-red-500 text-xs font-semibold hover:underline"
              >
                Cancel
              </button>
            </div>
          </div>

          <div class="flex justify-between items-center px-6 py-4">
            <div class="flex items-center space-x-3">
              <svg
                class="w-6 h-6 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 20h2a2 2 0 002-2V7a2 2 0 00-2-2h-3M7 11v10a2 2 0 002 2h6a2 2 0 002-2v-3m-7-3V7a2 2 0 012-2h6a2 2 0 012 2v6m-7 3v4"
                ></path>
              </svg>
              <div>
                <div class="font-semibold">Family Emergency</div>
                <div class="text-sm text-gray-500">
                  Jan 20, 2024 - Jan 22, 2024
                </div>
                <div class="text-sm text-gray-500">Family emergency</div>
              </div>
            </div>
            <div>
              <LeaveHistory />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import LeaveHistory from "@/components/LeaveHistory.vue";


</script>