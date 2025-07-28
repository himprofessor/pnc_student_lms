<template>
    <nav class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-xl font-bold">Admin Panel</h1>
            <div>
                <router-link to="/admin/dashboard" class="text-white px-4">Dashboard</router-link>
                <router-link to="/admin/accounts" class="text-white px-4">Manage Accounts</router-link>
                <router-link to="/admin/educators" class="text-white px-4">Manage Educators</router-link>
                <router-link to="/admin/students" class="text-white px-4">Manage Students</router-link>
            </div>
        </div>
    </nav>
</template>