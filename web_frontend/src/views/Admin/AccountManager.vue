<template>
    <div class="container mx-auto bg-white p-8 shadow-lg rounded-lg max-w-xl w-full">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Create New Account</h1>

        <!-- Tab Navigation -->
        <div class="flex justify-center mb-6">
            <button @click="currentTab = 'educator'" 
                    :class="{'bg-blue-600 text-white shadow-md': currentTab === 'educator', 'bg-gray-200 text-gray-800 hover:bg-gray-300': currentTab !== 'educator'}" 
                    class="px-6 py-2 font-semibold text-base rounded-l-md transition duration-300 ease-in-out">
                Educator
            </button>
            <button @click="currentTab = 'student'" 
                    :class="{'bg-blue-600 text-white shadow-md': currentTab === 'student', 'bg-gray-200 text-gray-800 hover:bg-gray-300': currentTab !== 'student'}" 
                    class="px-6 py-2 font-semibold text-base rounded-r-md transition duration-300 ease-in-out">
                Student
            </button>
        </div>

        <!-- Global Status Message -->
        <div v-if="statusMessage.text" 
             :class="{'bg-green-100 border-green-400 text-green-700': statusMessage.type === 'success', 'bg-red-100 border-red-400 text-red-700': statusMessage.type === 'error'}" 
             class="border px-4 py-3 mb-6 rounded-md relative" role="alert">
            <span class="block sm:inline font-medium">{{ statusMessage.text }}</span>
        </div>

        <!-- Educator Form -->
        <div v-if="currentTab === 'educator'">
            <form @submit.prevent="handleEducatorSubmit" class="space-y-5">
                <FormInput id="educator-name" label="Name" v-model="educatorFormData.name" :error="educatorErrors.name" placeholder="Enter educator's full name" required />
                <FormInput id="educator-email" label="Email" type="email" v-model="educatorFormData.email" :error="educatorErrors.email" placeholder="educator@passerellesnumeriques.org" hint="Email must end with @passerellesnumeriques.org" required />
                <FormInput id="educator-password" label="Password" type="password" v-model="educatorFormData.password" :error="educatorErrors.password" placeholder="Enter a strong password" hint="Minimum 6 characters" required />
                <FormInput id="educator-confirm-password" label="Confirm Password" type="password" v-model="educatorFormData.confirmPassword" :error="educatorErrors.confirmPassword" placeholder="Confirm your password" required />

                    <div class="mb-4">
                        <label for="educator-class-ids" class="block text-gray-700 text-sm font-semibold mb-2">Assign Classes:</label>
                        <select id="educator-class-ids" v-model="educatorFormData.class_ids" multiple
                                class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :disabled="isLoadingClasses">
                            <option v-if="isLoadingClasses" value="" disabled>Loading classes...</option>
                            <option v-else-if="classes.length === 0" value="" disabled>No classes available.</option>
                            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
                        </select>
                        <p v-if="educatorErrors.class_ids" class="text-red-500 text-xs italic mt-1">{{ educatorErrors.class_ids }}</p>
                    </div>

                <button type="submit"
                        :disabled="isEducatorSubmitting || isLoadingClasses"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-300 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                    <svg v-if="isEducatorSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span v-if="isEducatorSubmitting">Creating Educator...</span>
                    <span v-else>Create Educator Account</span>
                </button>
            </form>
        </div>

        <!-- Student Form -->
        <div v-if="currentTab === 'student'">
            <form @submit.prevent="handleStudentSubmit" class="space-y-5">
                <FormInput id="student-name" label="Name" v-model="studentFormData.name" :error="studentErrors.name" placeholder="Enter student's full name" required />
                <FormInput id="student-email" label="Email" type="email" v-model="studentFormData.email" :error="studentErrors.email" placeholder="student@student.passerellesnumeriques.org" hint="Email must end with @student.passerellesnumeriques.org" required />
                <FormInput id="student-password" label="Password" type="password" v-model="studentFormData.password" :error="studentErrors.password" placeholder="Enter a strong password" hint="Minimum 6 characters" required />
                <FormInput id="student-confirm-password" label="Confirm Password" type="password" v-model="studentFormData.confirmPassword" :error="studentErrors.confirmPassword" placeholder="Confirm your password" required />

                <div class="mb-4">
                    <label for="student-class-ids" class="block text-gray-700 text-sm font-semibold mb-2">Assign Classes:</label>
                    <select id="student-class-ids" v-model="studentFormData.class_ids" multiple
                            class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :disabled="isLoadingClasses">
                        <option v-if="isLoadingClasses" value="" disabled>Loading classes...</option>
                        <option v-else-if="classes.length === 0" value="" disabled>No classes available.</option>
                        <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
                    </select>
                    <p v-if="studentErrors.class_ids" class="text-red-500 text-xs italic mt-1">{{ studentErrors.class_ids }}</p>
                </div>

                <button type="submit"
                        :disabled="isStudentSubmitting || isLoadingClasses"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-300 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                    <svg v-if="isStudentSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span v-if="isStudentSubmitting">Creating Student...</span>
                    <span v-else>Create Student Account</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import FormInput from '@/components/FormInput.vue';

// Internal state for classes and loading status
const classes = ref([]);
const isLoadingClasses = ref(false);

// Function to fetch classes from the API
const fetchClasses = async () => {
    isLoadingClasses.value = true;
    try {
        const response = await fetch('http://127.0.0.1:8000/api/classes', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });
        if (!response.ok) {
            throw new Error('Failed to fetch classes');
        }
        const data = await response.json();
        classes.value = data; // Load classes into the reactive state
    } catch (error) {
        console.error('Error fetching classes:', error);
        statusMessage.text = 'Failed to load classes. Please try again.';
        statusMessage.type = 'error';
    } finally {
        isLoadingClasses.value = false;
    }
};

// Call fetchClasses when the component is mounted
onMounted(fetchClasses);

// Reactive state for the current active tab
const currentTab = ref('educator');

// Reactive state for displaying status messages (success/error)
const statusMessage = reactive({
    text: '',
    type: '' // 'success' or 'error'
});

// --- Educator Form Logic ---
const educatorFormData = reactive({
    name: '',
    email: '',
    password: '',
    confirmPassword: '',
    class_ids: []
});
const educatorErrors = reactive({
    name: '', email: '', password: '', confirmPassword: '', class_ids: ''
});
const isEducatorSubmitting = ref(false);

// Client-side validation for educator form
const validateEducatorForm = () => {
    let isValid = true;
    Object.keys(educatorErrors).forEach(key => educatorErrors[key] = ''); // Clear previous errors

    if (!educatorFormData.name.trim()) { educatorErrors.name = 'Name is required.'; isValid = false; }
    if (!educatorFormData.email.trim()) { educatorErrors.email = 'Email is required.'; isValid = false; }
    else if (!educatorFormData.email.endsWith('@passerellesnumeriques.org')) { educatorErrors.email = 'Email must end with @passerellesnumeriques.org.'; isValid = false; }
    else if (!/\S+@\S+\.\S+/.test(educatorFormData.email)) { educatorErrors.email = 'Invalid email format.'; isValid = false; }
    if (educatorFormData.password.length < 6) { educatorErrors.password = 'Password must be at least 6 characters.'; isValid = false; }
    if (educatorFormData.password !== educatorFormData.confirmPassword) { educatorErrors.confirmPassword = 'Passwords do not match.'; isValid = false; }
    if (educatorFormData.class_ids.length === 0) { educatorErrors.class_ids = 'At least one class must be assigned.'; isValid = false; }
    return isValid;
};

// Handle educator form submission
const handleEducatorSubmit = async () => {
    if (!validateEducatorForm()) {
        statusMessage.text = 'Please correct the errors in the form.';
        statusMessage.type = 'error';
        return;
    }
    isEducatorSubmitting.value = true;
    statusMessage.text = ''; // Clear previous status message

    const payload = {
        name: educatorFormData.name,
        email: educatorFormData.email,
        password: educatorFormData.password,
        class_ids: educatorFormData.class_ids.map(id => parseInt(id))
    };

    try {
        const token = localStorage.getItem('authToken'); // Retrieve the token from local storage
        
        const response = await fetch('http://127.0.0.1:8000/api/educators', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}` // Include the token in the request
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            const result = await response.json();
            throw new Error(result.message || 'Failed to create educator.');
        }

        const result = await response.json();
        statusMessage.text = result.message;
        statusMessage.type = 'success';
        // Reset form fields on success
        Object.assign(educatorFormData, { name: '', email: '', password: '', confirmPassword: '', class_ids: [] });
        Object.keys(educatorErrors).forEach(key => educatorErrors[key] = ''); // Clear errors
    } catch (error) {
        let errorMessage = 'Failed to create educator.';
        if (error.errors) {
            for (const key in error.errors) {
                if (educatorErrors[key] !== undefined) educatorErrors[key] = error.errors[key][0];
            }
            errorMessage = 'Please correct the highlighted errors.';
        } else if (error.message) {
            errorMessage = error.message;
        }
        statusMessage.text = errorMessage;
        statusMessage.type = 'error';
    } finally {
        isEducatorSubmitting.value = false;
    }
};

// --- Student Form Logic ---
const studentFormData = reactive({
    name: '',
    email: '',
    password: '',
    confirmPassword: '',
    class_ids: []
});
const studentErrors = reactive({
    name: '', email: '', password: '', confirmPassword: '', class_ids: ''
});
const isStudentSubmitting = ref(false);

const validateStudentForm = () => {
    let isValid = true;
    Object.keys(studentErrors).forEach(key => studentErrors[key] = ''); // Clear errors

    if (!studentFormData.name.trim()) { studentErrors.name = 'Name is required.'; isValid = false; }
    if (!studentFormData.email.trim()) { studentErrors.email = 'Email is required.'; isValid = false; }
    else if (!studentFormData.email.endsWith('@student.passerellesnumeriques.org')) { studentErrors.email = 'Student email must end with @student.passerellesnumeriques.org.'; isValid = false; }
    else if (!/\S+@\S+\.\S+/.test(studentFormData.email)) { studentErrors.email = 'Invalid email format.'; isValid = false; }
    if (studentFormData.password.length < 6) { studentErrors.password = 'Password must be at least 6 characters.'; isValid = false; }
    if (studentFormData.password !== studentFormData.confirmPassword) { studentErrors.confirmPassword = 'Passwords do not match.'; isValid = false; }
    if (studentFormData.class_ids.length === 0) { studentErrors.class_ids = 'At least one class must be assigned.'; isValid = false; }
    return isValid;
};

const handleStudentSubmit = async () => {
    if (!validateStudentForm()) {
        statusMessage.text = 'Please correct the errors in the form.';
        statusMessage.type = 'error';
        return;
    }
    isStudentSubmitting.value = true;
    statusMessage.text = '';

    const payload = {
        name: studentFormData.name,
        email: studentFormData.email,
        password: studentFormData.password,
        class_ids: studentFormData.class_ids.map(id => parseInt(id))
    };

    try {
        const token = localStorage.getItem('authToken'); // Retrieve the token from local storage
        
        const response = await fetch('http://127.0.0.1:8000/api/students', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}` // Include the token in the request
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            const result = await response.json();
            throw new Error(result.message || 'Failed to create student.');
        }

        const result = await response.json();
        statusMessage.text = result.message;
        statusMessage.type = 'success';
        // Reset form fields on success
        Object.assign(studentFormData, { name: '', email: '', password: '', confirmPassword: '', class_ids: [] });
        Object.keys(studentErrors).forEach(key => studentErrors[key] = ''); // Clear errors
    } catch (error) {
        let errorMessage = 'Failed to create student.';
        if (error.errors) {
            for (const key in error.errors) {
                if (studentErrors[key] !== undefined) studentErrors[key] = error.errors[key][0];
            }
            errorMessage = 'Please correct the highlighted errors.';
        } else if (error.message) {
            errorMessage = error.message;
        }
        statusMessage.text = errorMessage;
        statusMessage.type = 'error';
    } finally {
        isStudentSubmitting.value = false;
    }
};
</script>

<style scoped>
/* Scoped styles for this component */
.container {
    max-width: 900px; /* Adjust max width as needed */
}

h1 {
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); /* Add a subtle shadow to the heading */
}

button {
    transition: 0.3s;
}

button:hover {
    transform: translateY(-2px); /* Add a slight lift effect on hover */
}

select {
    transition: border-color 0.3s;
}

select:focus {
    border-color: #3b82f6; /* Change border color on focus */
    box-shadow: 0 0 5px rgba(59, 130, 246, 0.5); /* Add shadow on focus */
}
</style>