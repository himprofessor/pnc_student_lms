<template>
  <div class="container bg-white p-8 shadow-lg rounded-lg max-w-4xl w-full">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Manage Educators</h1>

    <div v-if="statusMessage.text"
         :class="statusClasses"
         class="border px-4 py-3 mb-6 rounded-md relative" role="alert">
      <span class="block sm:inline font-medium">{{ statusMessage.text }}</span>
    </div>

    <div v-if="isLoading" class="text-center py-8 text-gray-600">Loading educators...</div>
    <div v-else-if="!Array.isArray(educators) || educators.length === 0"
         class="text-center py-8 text-gray-600">No educators found.</div>

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
            <td class="py-3 px-6">{{ educator.name }}</td>
            <td class="py-3 px-6">{{ educator.email }}</td>
            <td class="py-3 px-6">
              <span v-if="Array.isArray(educator.classes) && educator.classes.length === 0"
                    class="text-gray-500 italic">No classes assigned</span>
              <span v-else-if="Array.isArray(educator.classes)">
                <span v-for="cls in educator.classes" :key="cls.id"
                      class="bg-blue-200 text-blue-800 py-1 px-2 rounded-full text-xs font-semibold mr-1 mb-1 inline-block">
                  {{ cls.name }}
                </span>
              </span>
              <span v-else class="text-gray-400 italic">No class data</span>
            </td>
            <td class="py-3 px-6 text-center">
              <div class="flex justify-center space-x-2">
                <button @click="openEditModal(educator)" class="btn-yellow" title="Edit">✏️</button>
                <button @click="openDeleteModal(educator)" class="btn-red" title="Delete">🗑️</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Edit Educator Modal -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
      <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md relative">
        <h3 class="text-2xl font-bold text-center mb-6">Edit Educator</h3>
        <button @click="showEditModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">×</button>

        <form @submit.prevent="handleUpdateEducator" class="space-y-5">
          <FormInput id="name" label="Name" v-model="currentEducator.name" :error="editErrors.name" required />
          <FormInput id="email" label="Email" type="email" v-model="currentEducator.email" :error="editErrors.email" required />
          <FormInput id="password" label="New Password (optional)" type="password" v-model="editPassword" :error="editErrors.password" />
          <FormInput id="confirm-password" label="Confirm Password" type="password"
                     v-model="editConfirmPassword" :error="editErrors.confirmPassword" />

          <div>
            <label class="block text-sm font-semibold mb-1">Assign Classes:</label>
            <select v-model="currentEducator.class_ids" multiple class="form-select h-32 w-full">
              <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
            </select>
            <p v-if="editErrors.class_ids" class="text-red-500 text-xs mt-1">{{ editErrors.class_ids }}</p>
          </div>

          <div class="flex justify-end space-x-3">
            <button type="button" @click="showEditModal = false" class="btn-gray">Cancel</button>
            <button type="submit" :disabled="isUpdating" class="btn-blue">
              <span v-if="isUpdating" class="spinner"></span>Update Educator
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      :show="showDeleteModal"
      title="Confirm Deletion"
      :message="`Are you sure you want to delete educator ${educatorToDelete.name}?`"
      @confirm="handleDeleteEducator"
      @cancel="showDeleteModal = false"
      :confirmText="isDeleting ? 'Deleting...' : 'Delete'"
      :confirmButtonClass="isDeleting ? 'bg-red-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import FormInput from '@/components/FormInput.vue';
import ConfirmationModal from '@/components/ConfirmationModal.vue';

const educators = ref([]);
const classes = ref([]);
const isLoading = ref(false);
const statusMessage = reactive({ text: '', type: '' });

const showEditModal = ref(false);
const currentEducator = reactive({ id: null, name: '', email: '', class_ids: [] });
const editErrors = reactive({ name: '', email: '', password: '', confirmPassword: '', class_ids: '' });
const isUpdating = ref(false);
const editPassword = ref('');
const editConfirmPassword = ref('');

const showDeleteModal = ref(false);
const educatorToDelete = reactive({ id: null, name: '' });
const isDeleting = ref(false);

const statusClasses = reactive({
  get 'bg-green-100 border-green-400 text-green-700'() { return statusMessage.type === 'success'; },
  get 'bg-red-100 border-red-400 text-red-700'() { return statusMessage.type === 'error'; }
});

const fetchEducators = async () => {
  isLoading.value = true;
  statusMessage.text = '';
  try {
    const token = localStorage.getItem('authToken');
    const res = await fetch('http://127.0.0.1:8000/api/educators', {
      headers: { Accept: 'application/json', Authorization: `Bearer ${token}` }
    });
    if (!res.ok) throw new Error(`Status ${res.status}`);
    const data = await res.json();
    educators.value = data.map(e => ({
      ...e,
      classes: Array.isArray(e.classes) ? e.classes : [],
      class_ids: Array.isArray(e.classes) ? e.classes.map(c => c.id) : []
    }));
  } catch (err) {
    statusMessage.text = `Failed loading educators: ${err.message}`;
    statusMessage.type = 'error';
  } finally {
    isLoading.value = false;
  }
};

const fetchClasses = async () => {
  try {
    const token = localStorage.getItem('authToken');
    const res = await fetch('http://127.0.0.1:8000/api/classes', {
      headers: { Accept: 'application/json', Authorization: `Bearer ${token}` }
    });
    if (!res.ok) throw new Error('Failed to fetch classes');
    classes.value = await res.json();
  } catch (err) {
    console.error(err);
  }
};

const openEditModal = (educator) => {
  Object.assign(currentEducator, {
    id: educator.user_id ?? educator.id,
    name: educator.name,
    email: educator.email,
    class_ids: Array.isArray(educator.classes) ? educator.classes.map(c => c.id) : []
  });
  editPassword.value = '';
  editConfirmPassword.value = '';
  Object.keys(editErrors).forEach(k => editErrors[k] = '');
  showEditModal.value = true;
};

const validateEditForm = () => {
  let valid = true;
  Object.keys(editErrors).forEach(k => editErrors[k] = '');
  if (!currentEducator.name.trim()) { editErrors.name = 'Name required'; valid = false; }
  if (!currentEducator.email.trim() || !currentEducator.email.endsWith('@passerellesnumeriques.org')) {
    editErrors.email = 'Valid @passerellesnumeriques.org email required'; valid = false;
  }
  if (editPassword.value || editConfirmPassword.value) {
    if (editPassword.value.length < 6) { editErrors.password = 'At least 6 chars'; valid = false; }
    if (editPassword.value !== editConfirmPassword.value) {
      editErrors.confirmPassword = 'Passwords must match'; valid = false;
    }
  }
  if (!Array.isArray(currentEducator.class_ids) || currentEducator.class_ids.length === 0) {
    editErrors.class_ids = 'At least one class must be assigned'; valid = false;
  }
  return valid;
};

const handleUpdateEducator = async () => {
  if (!validateEditForm()) {
    statusMessage.text = 'Please fix errors';
    statusMessage.type = 'error';
    return;
  }
  isUpdating.value = true;
  statusMessage.text = '';
  const payload = {
    name: currentEducator.name,
    email: currentEducator.email,
    class_ids: currentEducator.class_ids.map(Number)
  };
  if (editPassword.value) payload.password = editPassword.value;

  try {
    const token = localStorage.getItem('authToken');
    const res = await fetch(`http://127.0.0.1:8000/api/educators/${currentEducator.id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json', Authorization: `Bearer ${token}` },
      body: JSON.stringify(payload)
    });
    const result = await res.json();
    if (!res.ok) throw new Error(result.message || res.statusText);
    statusMessage.text = result.message || 'Update successful';
    statusMessage.type = 'success';
    showEditModal.value = false;
    await fetchEducators();
  } catch (err) {
    statusMessage.text = `Update failed: ${err.message}`;
    statusMessage.type = 'error';
  } finally {
    isUpdating.value = false;
  }
};

const openDeleteModal = (educator) => {
  educatorToDelete.id = educator.user_id ?? educator.id;
  educatorToDelete.name = educator.name;
  showDeleteModal.value = true;
};

const handleDeleteEducator = async () => {
  isDeleting.value = true;
  statusMessage.text = '';
  try {
    const token = localStorage.getItem('authToken');
    const res = await fetch(`http://127.0.0.1:8000/api/educators/${educatorToDelete.id}`, {
      method: 'DELETE',
      headers: { Accept: 'application/json', Authorization: `Bearer ${token}` }
    });
    if (!res.ok) {
      const err = await res.json();
      throw new Error(err.message || res.statusText);
    }
    statusMessage.text = 'Educator deleted';
    statusMessage.type = 'success';
    showDeleteModal.value = false;
    await fetchEducators();
  } catch (err) {
    statusMessage.text = `Delete failed: ${err.message}`;
    statusMessage.type = 'error';
  } finally {
    isDeleting.value = false;
  }
};

onMounted(() => {
  fetchEducators();
  fetchClasses();
});
</script>

<style scoped>
.container { max-width: 900%; }
.btn-yellow { @apply w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center hover:bg-yellow-200; }
.btn-red { @apply w-8 h-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center hover:bg-red-200; }
.btn-gray { @apply px-5 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300; }
.btn-blue { @apply px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700; }
.spinner { @apply animate-spin mr-2 h-5 w-5 text-white; }
.form-select { @apply shadow-sm border border-gray-300 rounded-md py-2 px-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent; }
.modal-overlay { @apply bg-black bg-opacity-50; }
</style>