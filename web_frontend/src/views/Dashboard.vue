<template>
  <div class="p-12 bg-gray-50 min-h-screen container ">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold">Welcome back, John Doe</h1>
      <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + New Leave Request
      </button>
    </div>

    <p class="text-gray-600 mb-4">Manage your leave requests and track their status</p>

    <!-- Summary Boxes -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded shadow text-center">
        <div class="text-yellow-500 text-2xl">🕒</div>
        <p class="text-lg font-semibold">Pending</p>
        <p>1</p>
      </div>
      <div class="bg-white p-4 rounded shadow text-center">
        <div class="text-green-500 text-2xl">✅</div>
        <p class="text-lg font-semibold">Approved</p>
        <p>1</p>
      </div>
      <div class="bg-white p-4 rounded shadow text-center">
        <div class="text-red-500 text-2xl">❌</div>
        <p class="text-lg font-semibold">Rejected</p>
        <p>0</p>
      </div>
      <div class="bg-white p-4 rounded shadow text-center">
        <div class="text-blue-500 text-2xl">📄</div>
        <p class="text-lg font-semibold">Total</p>
        <p>2</p>
      </div>
    </div>

    <!-- Recent Leave Requests -->
    <div class="bg-white p-4 rounded shadow">
      <h2 class="text-xl font-semibold mb-4">Recent Leave Requests</h2>

      <!-- Leave Request 1 -->
      <div class="border-b pb-4 mb-4">
        <div class="flex justify-between items-center">
          <div>
            <p class="font-semibold">🩺 Sick Leave</p>
            <p class="text-gray-500 text-sm">Jan 15, 2024 - Jan 17, 2024</p>
            <p class="text-gray-600 text-sm mt-1">Flu symptoms</p>
          </div>
          <div class="flex items-center gap-4">
            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-sm">Pending</span>
            <button class="text-red-600 text-sm hover:underline">Cancel</button>
          </div>
        </div>
      </div>

      <!-- Leave Request 2 -->
      <div>
        <div class="flex justify-between items-center">
          <div>
            <p class="font-semibold">❤️ Family Emergency</p>
            <p class="text-gray-500 text-sm">Jan 20, 2024 - Jan 22, 2024</p>
            <p class="text-gray-600 text-sm mt-1">Family emergency</p>
          </div>
          <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">Approved</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

</script>

<style>

</style>