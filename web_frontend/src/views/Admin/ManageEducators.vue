 <template>
    <div class="container bg-white p-8 shadow-lg rounded-lg max-w-4xl w-full">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Manage Educators</h1>

        <div v-if="statusMessage.text"
             :class="{'bg-green-100 border-green-400 text-green-700': statusMessage.type === 'success', 'bg-red-100 border-red-400 text-red-700': statusMessage.type === 'error'}"
             class="border px-4 py-3 mb-6 rounded-md relative" role="alert">
            <span class="block sm:inline font-medium">{{ statusMessage.text }}</span>
        </div>

        <div v-if="isLoading" class="text-center py-8 text-gray-600">Loading educators...</div>
        <div v-else-if="educators.length === 0" class="text-center py-8 text-gray-600">No educators found.</div>

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
                    <tr v-for="educator in educators" :key="educator.id" class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-6 text-left whitespace-nowrap">{{ educator.name }}</td>
                        <td class="py-3 px-6 text-left">{{ educator.email }}</td>
                        <td class="py-3 px-6 text-left">
                            <span v-if="educator.classes && educator.classes.length === 0" class="text-gray-500 italic">No classes assigned</span>
                            <span v-else>
                                <span v-for="cls in educator.classes" :key="cls.id"
                                      class="bg-blue-200 text-blue-800 py-1 px-2 rounded-full text-xs font-semibold mr-1 mb-1 inline-block">
                                    {{ cls.name }}
                                </span>
                            </span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <button @click="openEditModal(educator)" class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center hover:bg-yellow-200 transition duration-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L15.232 5.232z"></path>
                                    </svg>
                                </button>
                                <button @click="openDeleteModal(educator)" class="w-8 h-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center hover:bg-red-200 transition duration-200" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Edit Educator Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
            <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md relative">
                <h3 class="text-2xl font-bold text-center text-gray-800 mb-6 text-center">Edit Educator</h3>
                <button @click="showEditModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>

                <form @submit.prevent="handleUpdateEducator" class="space-y-5">
                    <FormInput id="edit-educator-name" label="Name" v-model="currentEducator.name" :error="editErrors.name" placeholder="Educator's full name" required />
                    <FormInput id="edit-educator-email" label="Email" type="email" v-model="currentEducator.email" :error="editErrors.email" placeholder="educator@passerellesnumeriques.org" hint="Email must end with @passerellesnumeriques.org" required />
                    <FormInput id="edit-educator-password" label="New Password (optional)" type="password" v-model="editPassword" :error="editErrors.password" placeholder="Leave blank to keep current" hint="Minimum 6 characters" />
                    <FormInput id="edit-educator-confirm-password" label="Confirm New Password" type="password" v-model="editConfirmPassword" :error="editErrors.confirmPassword" placeholder="Confirm new password" />

                    <div class="mb-4">
                        <label for="edit-educator-class-ids" class="block text-gray-700 text-sm font-semibold mb-2">Assign Classes:</label>
                        <select id="edit-educator-class-ids" v-model="currentEducator.class_ids" multiple
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
                            <span>Update Educator</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            title="Confirm Deletion"
            :message="'Are you sure you want to delete educator ' + educatorToDelete.name + '? This action cannot be undone.'"
            @confirm="handleDeleteEducator"
            @cancel="showDeleteModal = false"
            :confirmText="isDeleting ? 'Deleting...' : 'Delete'"
            :confirmButtonClass="isDeleting ? 'bg-red-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'"
        />
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import FormInput from '@/components/FormInput.vue'; // Adjusted path
import ConfirmationModal from '@/components/ConfirmationModal.vue'; // Adjusted path
// No apiSimulator import needed as we are using direct fetch

const props = defineProps({
    classes: Array // This prop is passed from App.vue
});

// Reactive state for educators and loading status
const educators = ref([]);
const isLoading = ref(false);
const statusMessage = reactive({ text: '', type: '' });

// Edit Modal State and Logic
const showEditModal = ref(false);
// Initialize currentEducator with user_id as the primary identifier for API calls
const currentEducator = reactive({ id: null, user_id: null, name: '', email: '', classes: [], class_ids: [] });
const editErrors = reactive({ name: '', email: '', password: '', confirmPassword: '', class_ids: '' });
const isUpdating = ref(false);
const editPassword = ref('');
const editConfirmPassword = ref('');

// Delete Modal State and Logic
const showDeleteModal = ref(false);
// Initialize educatorToDelete with user_id as the primary identifier for API calls
const educatorToDelete = reactive({ id: null, name: '' });
const isDeleting = ref(false);

// Function to fetch all educators
const fetchEducators = async () => {
    isLoading.value = true;
    statusMessage.text = '';
    try {
        const token = localStorage.getItem('authToken'); // Retrieve the token from local storage

        const response = await fetch('http://127.0.0.1:8000/api/educators', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}` // Include the token in the request
            }
        });

        console.log('Response:', response); // Log the entire response

        if (!response.ok) {
            const errorResponse = await response.json();
            console.error('Error response:', errorResponse);
            throw new Error(`HTTP error! status: ${response.status} - ${errorResponse.message || response.statusText}`);
        }

        const data = await response.json();
        console.log('Fetched educators:', data); // Log data to see its structure

        educators.value = data.map(edu => ({
            ...edu,
            // Ensure classes is an array, even if null or undefined from API
            classes: edu.classes || [],
            // Map existing classes to class_ids for multi-select binding
            class_ids: edu.classes ? edu.classes.map(c => c.id) : []
        }));
    } catch (error) {
        console.error('Error fetching educators:', error);
        statusMessage.text = `Failed to load educators: ${error.message}`;
        statusMessage.type = 'error';
    } finally {
        isLoading.value = false;
    }
};

// Function to open the edit modal and populate it with educator data
const openEditModal = (educator) => {
    // Crucial change: Assign educator.user_id to currentEducator.id for API calls
    Object.assign(currentEducator, {
        id: educator.user_id, // Use educator.user_id for API identification
        user_id: educator.user_id, // Keep user_id for internal consistency if needed
        name: educator.name,
        email: educator.email,
        classes: educator.classes || [],
        class_ids: educator.classes ? educator.classes.map(c => c.id) : []
    });
    editPassword.value = '';
    editConfirmPassword.value = '';
    Object.keys(editErrors).forEach(key => editErrors[key] = '');
    showEditModal.value = true;
};

// Client-side validation for the edit form
const validateEditForm = () => {
    let isValid = true;
    Object.keys(editErrors).forEach(key => editErrors[key] = '');

    if (!currentEducator.name.trim()) { editErrors.name = 'Name is required.'; isValid = false; }
    if (!currentEducator.email.trim()) { editErrors.email = 'Email is required.'; isValid = false; }
    else if (!currentEducator.email.endsWith('@passerellesnumeriques.org')) { editErrors.email = 'Email must end with @passerellesnumeriques.org.'; isValid = false; }
    else if (!/\S+@\S+\.\S+/.test(currentEducator.email)) { editErrors.email = 'Invalid email format.'; isValid = false; }

    if (editPassword.value || editConfirmPassword.value) {
        if (editPassword.value.length < 6) { editErrors.password = 'Password must be at least 6 characters.'; isValid = false; }
        if (editPassword.value !== editConfirmPassword.value) { editErrors.confirmPassword = 'Passwords do not match.'; isValid = false; }
    }

    if (currentEducator.class_ids.length === 0) { editErrors.class_ids = 'At least one class must be assigned.'; isValid = false; }
    return isValid;
};

// Handle educator update submission
const handleUpdateEducator = async () => {
    if (!validateEditForm()) {
        statusMessage.text = 'Please correct the errors in the form.';
        statusMessage.type = 'error';
        return;
    }
    isUpdating.value = true;
    statusMessage.text = '';

    const payload = {
        name: currentEducator.name,
        email: currentEducator.email,
        class_ids: currentEducator.class_ids.map(id => parseInt(id)),
        ...(editPassword.value && { password: editPassword.value })
    };

    try {
        const token = localStorage.getItem('authToken');

        console.log('Updating educator with ID (user_id):', currentEducator.id);
        const response = await fetch(`http://127.0.0.1:8000/api/educators/${currentEducator.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            const errorResponse = await response.json();
            throw new Error(`HTTP error! status: ${response.status} - ${errorResponse.message || response.statusText}`);
        }

        const result = await response.json();
        statusMessage.text = result.message || 'Educator updated successfully!';
        statusMessage.type = 'success';
        showEditModal.value = false;
        fetchEducators();
    } catch (error) {
        console.error('Error updating educator:', error);
        statusMessage.text = `Failed to update educator: ${error.message}`;
        statusMessage.type = 'error';
    } finally {
        isUpdating.value = false;
    }
};

// Function to open the delete confirmation modal
const openDeleteModal = (educator) => {
    // Crucial change: Assign educator.user_id to educatorToDelete.id for API calls
    educatorToDelete.id = educator.user_id; // Use educator.user_id for API identification
    educatorToDelete.name = educator.name;
    showDeleteModal.value = true;
};

// Handle educator deletion
const handleDeleteEducator = async () => {
    isDeleting.value = true;
    statusMessage.text = '';
    try {
        const token = localStorage.getItem('authToken');

        console.log('Deleting educator with ID (user_id):', educatorToDelete.id);
        const response = await fetch(`http://127.0.0.1:8000/api/educators/${educatorToDelete.id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            const errorResponse = await response.json();
            throw new Error(`HTTP error! status: ${response.status} - ${errorResponse.message || response.statusText}`);
        }

        const result = await response.json();
        statusMessage.text = result.message || 'Educator deleted successfully!';
        statusMessage.type = 'success';
        showDeleteModal.value = false;
        fetchEducators();
    } catch (error) {
        console.error('Error deleting educator:', error);
        statusMessage.text = `Failed to delete educator: ${error.message}`;
        statusMessage.type = 'error';
    } finally {
        isDeleting.value = false;
    }
};

// Fetch educators when the component is mounted
onMounted(fetchEducators);
</script>
<style scope>
.container{
     max-width: 900%;
}
</style>
 
 