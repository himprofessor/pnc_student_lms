<template>
    <div class="min-h-screen bg-gray-100 flex flex-col items-center py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full bg-white shadow-xl rounded-lg p-8 space-y-8">
            <h1 class="text-5xl font-extrabold text-center text-gray-900 mb-10">Admin Dashboard</h1>

            <p class="text-center text-lg text-gray-700 mb-12">
                Welcome to the administrator panel. From here, you can manage user accounts, classes, and view reports.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card for Account Management (Educator & Student Creation) -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg shadow-md p-6 flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:border-blue-300 card">
                    <div class="text-blue-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 14v7m-3-3h6"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Create Accounts</h2>
                    <p class="text-gray-600 mb-4">Create new educator and student accounts.</p>
                    <button @click="goToAccountManager"
                            class="mt-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-300 ease-in-out">
                        Go to Account Manager
                    </button>
                </div>

                <!-- Card for Managing Educators -->
                <div class="bg-green-50 border border-green-200 rounded-lg shadow-md p-6 flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:border-green-300 card">
                    <div class="text-green-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H2v-2a3 3 0 015.356-1.857M17 20v-2c0-.653-.165-1.282-.472-1.828M13 8a4 4 0 11-8 0 4 4 0 018 0zM12 14c-1.474 0-2.891.492-4 1.39M17 14c.322 0 .638.042.938.12M20 16a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Manage Educators</h2>
                    <p class="text-gray-600 mb-4">View, edit, and delete existing educator profiles.</p>
                    <button @click="goToManageEducators"
                            class="mt-auto bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-5 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-300 ease-in-out">
                        Go to Educators List
                    </button>
                </div>

                <!-- Card for Managing Students -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg shadow-md p-6 flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:border-purple-300 card">
                    <div class="text-purple-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Manage Students</h2>
                    <p class="text-gray-600 mb-4">View, edit, and delete existing student profiles.</p>
                    <button @click="goToManageStudents"
                            class="mt-auto bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-5 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition duration-300 ease-in-out">
                        Go to Students List
                    </button>
                </div>

                <!-- Placeholder Card for Reports -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg shadow-md p-6 flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:border-yellow-300 card">
                    <div class="text-yellow-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Reports & Analytics</h2>
                    <p class="text-gray-600 mb-4">Access various reports and analytics data.</p>
                    <button class="mt-auto bg-gray-300 text-gray-700 font-semibold py-2 px-5 rounded-md cursor-not-allowed opacity-70">
                        Coming Soon
                    </button>
                </div>

                <!-- Placeholder Card for Class Management -->
                <div class="bg-red-50 border border-red-200 rounded-lg shadow-md p-6 flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:border-red-300 card">
                    <div class="text-red-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Class Management</h2>
                    <p class="text-gray-600 mb-4">Create, edit, and manage student classes/groups.</p>
                    <button class="mt-auto bg-gray-300 text-gray-700 font-semibold py-2 px-5 rounded-md cursor-not-allowed opacity-70">
                        Coming Soon
                    </button>
                </div>

                <!-- Placeholder Card for Settings -->
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg shadow-md p-6 flex flex-col items-center text-center transition-all duration-300 hover:shadow-lg hover:border-indigo-300 card">
                    <div class="text-indigo-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Settings</h2>
                    <p class="text-gray-600 mb-4">Configure system-wide settings and preferences.</p>
                    <button class="mt-auto bg-gray-300 text-gray-700 font-semibold py-2 px-5 rounded-md cursor-not-allowed opacity-70">
                        Coming Soon
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';

const router = useRouter(); // Access the router instance

const goToAccountManager = () => {
    router.push({ name: 'AccountManager' });
};
const goToManageEducators = () => {
    router.push({ name: 'ManageEducators' });
};
const goToManageStudents = () => {
    router.push({ name: 'ManageStudents' });
};
</script>

<style scoped>
/* Scoped styles for this component */
/* No additional scoped styles needed as Tailwind CSS classes are used directly in the template. */
/* The global styles from the main HTML file (like font-family and border-radius) will still apply. */
</style>
