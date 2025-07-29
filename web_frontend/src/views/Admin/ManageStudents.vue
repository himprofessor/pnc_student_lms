 <template>
    <div class="container bg-white p-8 shadow-lg rounded-lg max-w-4xl w-full">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Manage Students</h1>

        <div v-if="statusMessage.text"
             :class="statusClasses"
             class="border px-4 py-3 mb-6 rounded-md relative" role="alert">
            <span class="block sm:inline font-medium">{{ statusMessage.text }}</span>
        </div>

        <div v-if="isLoading" class="text-center py-8 text-gray-600">Loading students...</div>
        <div v-else-if="!Array.isArray(students) || students.length === 0"
             class="text-center py-8 text-gray-600">No students found.</div>

        <div v-else class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Email</th>
                        <th class="py-3 px-6 text-left">Classes</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                    <tr v-for="student in students" :key="student.id" class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-6 text-left whitespace-nowrap">{{ student.name }}</td>
                        <td class="py-3 px-6 text-left">{{ student.email }}</td>
                        <td class="py-3 px-6 text-left">
                            <span v-if="Array.isArray(student.student_classes) && student.student_classes.length === 0" class="text-gray-500 italic">No classes assigned</span>
                            <span v-else-if="Array.isArray(student.student_classes)">
                                <span v-for="cls in student.student_classes" :key="cls.id"
                                      class="bg-purple-200 text-purple-800 py-1 px-2 rounded-full text-xs font-semibold mr-1 mb-1 inline-block">
                                    {{ cls.name }}
                                </span>
                            </span>
                            <span v-else class="text-gray-400 italic">No class data</span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button @click="openEditModal(student)"
                                        class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center hover:bg-yellow-200 transition duration-200"
                                        title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L15.232 5.232z"></path></svg>
                                </button>
                                <button @click="openDeleteModal(student)"
                                        class="w-8 h-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center hover:bg-red-200 transition duration-200"
                                        title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Edit Student Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md relative">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Student</h3>
                <button @click="showEditModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>

                <form @submit.prevent="handleUpdateStudent" class="space-y-5">
                    <FormInput id="edit-student-name" label="Name" v-model="currentStudent.name" :error="editErrors.name" placeholder="Student's full name" required />
                    <FormInput id="edit-student-email" label="Email" type="email" v-model="currentStudent.email" :error="editErrors.email" placeholder="student@student.passerellesnumeriques.org" hint="Email must end with @student.passerellesnumeriques.org" required />
                    <FormInput id="edit-student-password" label="New Password (optional)" type="password" v-model="editPassword" :error="editErrors.password" placeholder="Leave blank to keep current" hint="Minimum 6 characters" />
                    <FormInput id="edit-student-confirm-password" label="Confirm New Password" type="password" v-model="editConfirmPassword" :error="editErrors.confirmPassword" placeholder="Confirm new password" />

                    <div class="mb-4">
                        <label for="edit-student-class-ids" class="block text-gray-700 text-sm font-semibold mb-2">Assign Classes:</label>
                        <select id="edit-student-class-ids" v-model="currentStudent.class_ids" multiple
                                class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent h-32">
                            <option v-if="classes.length === 0" value="" disabled>No classes available.</option>
                            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
                        </select>
                        <p v-if="editErrors.class_ids" class="text-red-500 text-xs italic mt-1">{{ editErrors.class_ids }}</p>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showEditModal = false"
                                class="px-5 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            Cancel
                        </button>
                        <button type="submit"
                                :disabled="isUpdating"
                                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                            <svg v-if="isUpdating" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="isUpdating">Updating...</span>
                            <span v-else>Update Student</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            title="Confirm Deletion"
            :message="'Are you sure you want to delete student ' + studentToDelete.name + '? This action cannot be undone.'"
            @confirm="handleDeleteStudent"
            @cancel="showDeleteModal = false"
            :confirmText="isDeleting ? 'Deleting...' : 'Delete'"
            :confirmButtonClass="isDeleting ? 'bg-red-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'"
        />
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import FormInput from '@/components/FormInput.vue';
import ConfirmationModal from '@/components/ConfirmationModal.vue';

// Reactive state for the list of students
const students = ref([]);
const classes = ref([]); // Assuming classes are fetched from a separate API or passed as prop
const isLoading = ref(false); // Loading state for fetching students
const statusMessage = reactive({ text: '', type: '' }); // Status messages for CRUD operations

// --- Edit Modal State and Logic ---
const showEditModal = ref(false);
// currentStudent now directly reflects the structure needed for the API, using user_id for identification
const currentStudent = reactive({ id: null, name: '', email: '', student_classes: [], class_ids: [] }); // Removed user_id as it's not a direct property of the User model
const editErrors = reactive({ name: '', email: '', password: '', confirmPassword: '', class_ids: '' });
const isUpdating = ref(false); // Loading state for update operation
const editPassword = ref(''); // Separate ref for new password input
const editConfirmPassword = ref(''); // Separate ref for confirm new password input

// --- Delete Modal State and Logic ---
const showDeleteModal = ref(false);
const studentToDelete = reactive({ id: null, name: '' }); // Uses user_id for identification
const isDeleting = ref(false); // Loading state for delete operation

// Computed property for status message classes
const statusClasses = computed(() => ({
    'bg-green-100 border-green-400 text-green-700': statusMessage.type === 'success',
    'bg-red-100 border-red-400 text-red-700': statusMessage.type === 'error'
}));

// Function to fetch all students
const fetchStudents = async () => {
    isLoading.value = true;
    statusMessage.text = ''; // Clear previous status message
    try {
        const token = localStorage.getItem('authToken'); // Retrieve the token from local storage
        const response = await fetch('http://127.0.0.1:8000/api/students', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}` // Include the token in the request
            }
        });

        if (!response.ok) {
            const errorResponse = await response.json();
            throw new Error(`HTTP error! status: ${response.status} - ${errorResponse.message || response.statusText}`);
        }

        const data = await response.json();
        students.value = data.map(stu => ({
            ...stu,
            // Ensure student_classes is an array, even if null or undefined from API
            student_classes: Array.isArray(stu.student_classes) ? stu.student_classes : [],
            // Map existing classes to class_ids for multi-select binding
            class_ids: Array.isArray(stu.student_classes) ? stu.student_classes.map(c => c.id) : []
        }));
    } catch (error) {
        console.error('Error fetching students:', error);
        statusMessage.text = `Failed to load students: ${error.message}`;
        statusMessage.type = 'error';
    } finally {
        isLoading.value = false;
    }
};

// Function to fetch all classes (assuming a separate endpoint for classes)
const fetchClasses = async () => {
    try {
        const token = localStorage.getItem('authToken');
        const response = await fetch('http://127.0.0.1:8000/api/classes', {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });
        if (!response.ok) {
            const errorResponse = await response.json();
            throw new Error(`Failed to fetch classes: ${errorResponse.message || response.statusText}`);
        }
        classes.value = await response.json();
    } catch (error) {
        console.error('Error fetching classes:', error);
        // Optionally display an error message to the user
    }
};


// Function to open the edit modal and populate it with student data
const openEditModal = (student) => {
    console.log('openEditModal: Student object received:', student); // Added log
    // CRITICAL CHANGE: Use student.id (the User's primary key) for API identification
    Object.assign(currentStudent, {
        id: student.id, // Use student.id as it's the User model's primary key
        name: student.name,
        email: student.email,
        student_classes: Array.isArray(student.student_classes) ? student.student_classes : [],
        class_ids: Array.isArray(student.student_classes) ? student.student_classes.map(c => c.id) : []
    });
    console.log('openEditModal: currentStudent after assignment:', currentStudent); // Added log
    editPassword.value = ''; // Clear password fields when opening modal
    editConfirmPassword.value = '';
    Object.keys(editErrors).forEach(key => editErrors[key] = ''); // Clear previous errors
    showEditModal.value = true;
};

// Client-side validation for the edit form
const validateEditForm = () => {
    let isValid = true;
    Object.keys(editErrors).forEach(key => editErrors[key] = ''); // Clear all errors at the start

    if (!currentStudent.name.trim()) {
        editErrors.name = 'Name is required.';
        isValid = false;
    }

    if (!currentStudent.email.trim()) {
        editErrors.email = 'Email is required.';
        isValid = false;
    } else if (!currentStudent.email.endsWith('@student.passerellesnumeriques.org')) {
        editErrors.email = 'Email must end with @student.passerellesnumeriques.org.';
        isValid = false;
    } else if (!/\S+@\S+\.\S+/.test(currentStudent.email)) {
        editErrors.email = 'Invalid email format.';
        isValid = false;
    }

    // Only validate password if trying to change it
    if (editPassword.value || editConfirmPassword.value) {
        if (editPassword.value.length < 6) {
            editErrors.password = 'Password must be at least 6 characters.';
            isValid = false;
        }
        if (editPassword.value !== editConfirmPassword.value) {
            editErrors.confirmPassword = 'Passwords do not match.';
            isValid = false;
        }
    }

    if (!Array.isArray(currentStudent.class_ids) || currentStudent.class_ids.length === 0) {
        editErrors.class_ids = 'At least one class must be assigned.';
        isValid = false;
    }
    return isValid;
};

// Handle student update submission
const handleUpdateStudent = async () => {
    if (!validateEditForm()) {
        statusMessage.text = 'Please correct the errors in the form.';
        statusMessage.type = 'error';
        return;
    }
    isUpdating.value = true;
    statusMessage.text = '';

    const payload = {
        name: currentStudent.name,
        email: currentStudent.email,
        // Ensure class_ids are numbers for the backend
        class_ids: currentStudent.class_ids.map(Number),
        ...(editPassword.value && { password: editPassword.value }) // Include password if provided
    };

    try {
        const token = localStorage.getItem('authToken'); // Retrieve the token from local storage

        console.log('handleUpdateStudent: Attempting to update student with ID (User ID) in URL:', currentStudent.id); // Added log
        console.log('handleUpdateStudent: Payload for update:', payload); // Added log

        const response = await fetch(`http://127.0.0.1:8000/api/students/${currentStudent.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}` // Include the token in the request
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            const errorResponse = await response.json();
            // If Laravel sends specific validation errors, display them
            if (response.status === 422 && errorResponse.errors) {
                Object.assign(editErrors, errorResponse.errors); // Assign backend validation errors
                statusMessage.text = 'Validation errors occurred. Please check the form.';
            } else {
                statusMessage.text = `HTTP error! status: ${response.status} - ${errorResponse.message || response.statusText}`;
            }
            statusMessage.type = 'error';
            throw new Error(`HTTP error! status: ${response.status}`); // Throw to catch block
        }

        const result = await response.json();
        statusMessage.text = result.message || 'Student updated successfully!';
        statusMessage.type = 'success';
        showEditModal.value = false; // Close modal on success
        fetchStudents(); // Refresh the list to show updated data
    } catch (error) {
        console.error('Error updating student:', error);
        if (!statusMessage.text.includes('Validation errors')) { // Avoid overwriting specific validation message
            statusMessage.text = `Failed to update student: ${error.message}`;
        }
        statusMessage.type = 'error';
    } finally {
        isUpdating.value = false;
    }
};

// Function to open the delete confirmation modal
const openDeleteModal = (student) => {
    console.log('openDeleteModal: Student object received:', student); // Added log
    // CRITICAL CHANGE: Use student.id (the User's primary key) for API identification
    studentToDelete.id = student.id; // Use student.id as it's the User model's primary key
    studentToDelete.name = student.name;
    console.log('openDeleteModal: studentToDelete after assignment:', studentToDelete); // Added log
    showDeleteModal.value = true;
};

// Handle student deletion
const handleDeleteStudent = async () => {
    isDeleting.value = true;
    statusMessage.text = '';
    try {
        const token = localStorage.getItem('authToken'); // Retrieve the token from local storage

        console.log('handleDeleteStudent: Attempting to delete student with ID (User ID):', studentToDelete.id); // Added log

        const response = await fetch(`http://127.0.0.1:8000/api/students/${studentToDelete.id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}` // Include the token in the request
            }
        });

        if (!response.ok) {
            const errorResponse = await response.json();
            throw new Error(`HTTP error! status: ${response.status} - ${errorResponse.message || response.statusText}`);
        }

        const result = await response.json();
        statusMessage.text = result.message || 'Student deleted successfully!';
        statusMessage.type = 'success';
        showDeleteModal.value = false; // Close modal on success
        fetchStudents(); // Refresh the list
    } catch (error) {
        console.error('Error deleting student:', error);
        statusMessage.text = `Failed to delete student: ${error.message}`;
        statusMessage.type = 'error';
    } finally {
        isDeleting.value = false;
    }
};

// Fetch students and classes when the component is mounted
onMounted(() => {
    fetchStudents();
    fetchClasses();
});
</script>

<style scoped>
/* Scoped styles for this component */
.container {
max-width: 900%;
}

.btn-yellow { @apply w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center hover:bg-yellow-200 transition duration-200; }
.btn-red { @apply w-8 h-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center hover:bg-red-200 transition duration-200; }
.btn-gray { @apply px-5 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400; }
.btn-blue { @apply px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center; }
.spinner { @apply animate-spin -ml-1 mr-3 h-5 w-5 text-white; }
.form-select { @apply shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent; }
.modal-overlay { @apply bg-black bg-opacity-50; }
</style>
