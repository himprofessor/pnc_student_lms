<template>
    <div class="min-h-screen bg-gray-100 text-gray-800 font-sans">
        <div class="px-6 py-6">
            <h2 class="text-2xl font-bold mb-1">Educator Dashboard</h2>
            <p class="text-gray-600">Review and manage student leave requests</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-6 mb-6">
            <!-- Pending -->
            <div class="bg-white p-5 rounded-lg shadow-sm flex items-center space-x-4">
                <div class="bg-yellow-100 p-2 rounded-full">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-semibold">Pending Review</div>
                    <p class="text-gray-900 text-xl font-bold">{{ pendingCount }}</p>
                </div>
            </div>

            <!-- Approved Today -->
            <div class="bg-white p-5 rounded-lg shadow-sm flex items-center space-x-4">
                <div class="bg-green-100 p-2 rounded-full">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-semibold">Approved Today</div>
                    <p class="text-gray-900 text-xl font-bold">{{ approvedTodayCount }}</p>
                </div>
            </div>

            <!-- Total -->
            <div class="bg-white p-5 rounded-lg shadow-sm flex items-center space-x-4">
                <div class="bg-blue-100 p-2 rounded-full">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-semibold">Total Requests</div>
                    <p class="text-gray-900 text-xl font-bold">{{ leaveRequests.length }}</p>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="px-6 mt-4 flex flex-wrap gap-4 items-center">
            <div class="relative flex items-center">
                <svg class="absolute left-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input v-model="search" type="text" placeholder="Search by student name..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full sm:w-80 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
        </div>

        <!-- Table -->
        <div class="mt-6 px-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <h3 class="text-lg font-semibold px-6 pt-4 text-gray-700">Leave Requests</h3>
                <p class="text-gray-600 px-6 pb-4">Review and manage student leave requests</p>
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-6 font-medium">Student</th>
                            <th class="py-3 px-6 font-medium">Leave Type</th>
                            <th class="py-3 px-6 font-medium">Duration</th>
                            <th class="py-3 px-6 font-medium">Status</th>
                            <th class="py-3 px-6 font-medium">Submitted</th>
                            <th class="py-3 px-6 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="(request, index) in paginatedRequests" :key="index">
                            <td class="py-4 px-6 flex items-center space-x-3">
                                <img :src="getProfileImageUrl(request)" :alt="`${request.student} profile`"
                                    class="w-10 h-10 rounded-full object-cover border border-gray-300"
                                    @error="handleImageError($event, request)" />
                                <div>
                                    <div class="font-medium text-gray-900">{{ request.student }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ request.student_id }}</div>
                                </div>
                            </td>
                            <td class="py-4 px-6">{{ request.leave_type }}</td>
                            <td class="py-4 px-6">{{ formatDuration(request.duration) }}</td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="{
                                    'bg-yellow-100 text-yellow-700': request.status === 'Pending',
                                    'bg-green-100 text-green-700': request.status === 'Approved',
                                    'bg-red-100 text-red-700': request.status === 'Rejected'
                                }">
                                    {{ request.status }}
                                </span>
                            </td>
                            <td class="py-4 px-6">{{ formatDate(request.submitted) }}</td>
                            <td class="py-4 px-6 space-x-3">
                                <button @click="viewDetail(request.id)"
                                    class="text-blue-600 hover:underline font-semibold">View</button>
                                <button v-if="request.status === 'Pending'" @click="approve(request.id)"
                                    class="text-green-600 hover:underline font-semibold">Approve</button>
                                <button v-if="request.status === 'Pending'" @click="reject(request.id)"
                                    class="text-red-600 hover:underline font-semibold">Reject</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="flex justify-center items-center space-x-4 py-4">
                    <button :disabled="currentPage === 1" @click="currentPage--"
                        class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 ">
                        Prev
                    </button>
                    <div>Page {{ currentPage }} of {{ totalPages }}</div>
                    <button :disabled="currentPage === totalPages" @click="currentPage++"
                        class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50">
                        Next
                    </button>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div v-if="showDetail" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg w-full max-w-md space-y-3">
                <h3 class="text-xl font-bold flex items-center space-x-3">
                    <img v-if="getDetailImageUrl()" :src="getDetailImageUrl()" :alt="detail.student"
                        class="w-12 h-12 rounded-full object-cover border border-gray-300"
                        @error="handleDetailImageError" />
                    <span>{{ detail.student }} - {{ detail.leave_type }}</span>
                </h3>
                <p><strong>From:</strong> {{ formatDate(detail.from_date) }}</p>
                <p><strong>To:</strong> {{ formatDate(detail.to_date) }}</p>
                <p><strong>Reason:</strong> {{ detail.reason }}</p>
                <p><strong>Contact:</strong> {{ detail.contact_info }}</p>
                <p v-if="detail.supporting_documents">
                    <a :href="detail.supporting_documents" target="_blank" class="text-blue-600 underline">View
                        Document</a>
                </p>
                <p><strong>Status:</strong> {{ detail.status }}</p>
                <p v-if="detail.approved_by"><strong>Approved by:</strong> {{ detail.approved_by }}</p>
                <p v-if="detail.rejection_reason"><strong>Rejection Reason:</strong> {{ detail.rejection_reason }}</p>
                <button @click="showDetail = false" class="mt-2 text-gray-600 underline">Close</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const search = ref('')
const leaveRequests = ref([])
const showDetail = ref(false)
const detail = ref({})
const currentPage = ref(1)
const perPage = 5
const user = ref(null)
const isLoading = ref(false)
const apiError = ref(null)
const detailImageError = ref(false)

const formatDate = (dateStr) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' }
    return new Date(dateStr).toLocaleDateString('en-US', options)
}

const formatDuration = (range) => {
    const [from, to] = range.split(' to ')
    return `${formatDate(from)} to ${formatDate(to)}`
}

const fetchLeaveRequests = async () => {
    try {
        const res = await axios.get('http://127.0.0.1:8000/api/educator/leave-requests', {
            headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
        })

        console.log('=== DEBUGGING LEAVE REQUESTS RESPONSE ===')
        console.log('Full response:', res.data)
        
        if (res.data && res.data.length > 0) {
            console.log('First leave request:', res.data[0])
            console.log('Available fields in first request:', Object.keys(res.data[0]))
            
            // Check for different possible image field names
            const firstRequest = res.data[0]
            console.log('Checking image fields:')
            console.log('  - profile_image:', firstRequest.profile_image)
            console.log('  - img:', firstRequest.img) 
            console.log('  - img_url:', firstRequest.img_url)
            console.log('  - student_img:', firstRequest.student_img)
            console.log('  - student.img:', firstRequest.student?.img)
            console.log('  - student.profile_image:', firstRequest.student?.profile_image)
            console.log('  - student object:', firstRequest.student)
        }
        
        leaveRequests.value = res.data

    } catch (err) {
        console.error('Error loading requests:', err)
    }
}



const viewDetail = async (id) => {
    try {
        const res = await axios.get(`http://127.0.0.1:8000/api/educator/leave-request/${id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
        })

        console.log('Leave request detail response:', res.data)

        detail.value = {
            ...res.data.leave_request,
            // Ensure the detail has the student profile image data
            profile_image: res.data.leave_request.profile_image || res.data.leave_request.student_img || res.data.leave_request.img || null
        }

        console.log('Detail with student profile image:', detail.value.profile_image)

        detailImageError.value = false // Reset image error for modal
        showDetail.value = true
    } catch (err) {
        console.error('Error loading request detail:', err)
    }
}

const approve = async (id) => {
    await axios.post(`http://127.0.0.1:8000/api/educator/leave-request/${id}/approve`, {}, {
        headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
    })
    alert('Approved!')
    showDetail.value = false
    fetchLeaveRequests()
}

const reject = async (id) => {
    const reason = prompt('Enter reason for rejection:')
    if (!reason) return alert('Rejection reason is required')
    await axios.post(`http://127.0.0.1:8000/api/educator/leave-request/${id}/reject`, {
        rejection_reason: reason
    }, {
        headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
    })
    alert('Rejected')
    showDetail.value = false
    fetchLeaveRequests()
}

const filteredRequests = computed(() => {
    if (!search.value) return leaveRequests.value
    return leaveRequests.value.filter(r =>
        r.student.toLowerCase().includes(search.value.toLowerCase())
    )
})

const paginatedRequests = computed(() => {
    const start = (currentPage.value - 1) * perPage
    return filteredRequests.value.slice(start, start + perPage)
})

const totalPages = computed(() =>
    Math.ceil(filteredRequests.value.length / perPage)
)

const pendingCount = computed(() =>
    leaveRequests.value.filter(r => r.status === 'Pending').length
)

const approvedTodayCount = computed(() => {
    const today = new Date().toISOString().split('T')[0]
    return leaveRequests.value.filter(r =>
        r.status === 'Approved' && r.submitted.startsWith(today)
    ).length
})



// Updated function for detail modal image
const getDetailImageUrl = () => {
    if (!detail.value || !detail.value.profile_image || detailImageError.value) {
        console.log('No detail student profile image data, using default avatar')
        const initial = detail.value?.student ? detail.value.student.charAt(0).toUpperCase() : '?'
        return `data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2QjcyODAiLz4KPHRleHQgeD0iMjAiIHk9IjI2IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj4ke2luaXRpYWx9PC90ZXh0Pjwvc3ZnPg==`
    }

    console.log('Using detail student profile image:', detail.value.profile_image)

    // The backend already returns a full URL in profile_image field, so we can use it directly
    return detail.value.profile_image
}

// Handle image errors for detail modal
const handleDetailImageError = () => {
    console.log('Detail image failed to load, using default avatar')
    detailImageError.value = true
}

const hasToken = computed(() => !!localStorage.getItem('authToken'))

const displayName = computed(() => {
    if (isLoading.value) return 'Loading...'
    if (apiError.value) return 'Error'
    if (!user.value) return 'Guest'

    return user.value.name ||
        user.value.full_name ||
        user.value.first_name ||
        (user.value.email ? user.value.email.split('@')[0] : null) ||
        'User'
})

const getUserInitials = () => {
    if (!user.value) return 'G'

    const name = user.value.name ||
        user.value.full_name ||
        user.value.first_name ||
        user.value.email ||
        'Guest'

    if (name.includes('@')) {
        return name.charAt(0).toUpperCase()
    }

    const words = name.split(' ')
    if (words.length >= 2) {
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase()
    }

    return name.charAt(0).toUpperCase()
}

// Function to get profile image URL from leave request data
const getProfileImageUrl = (request) => {
    console.log(`=== DEBUG: Getting image for student ${request.student_id} ===`)
    console.log('Request object:', request)

    // Try multiple possible field names for the image
    let imageField = null
    
    // Check different possible locations for the image data
    if (request.profile_image) {
        imageField = request.profile_image
        console.log('📷 Found image in request.profile_image:', imageField)
    } else if (request.img_url) {
        imageField = request.img_url
        console.log('📷 Found image in request.img_url:', imageField)
    } else if (request.img) {
        imageField = request.img
        console.log('📷 Found image in request.img:', imageField)
    } else if (request.student_img) {
        imageField = request.student_img
        console.log('📷 Found image in request.student_img:', imageField)
    } else if (request.student?.img) {
        imageField = request.student.img
        console.log('📷 Found image in request.student.img:', imageField)
    } else if (request.student?.profile_image) {
        imageField = request.student.profile_image
        console.log('📷 Found image in request.student.profile_image:', imageField)
    } else if (request.student?.img_url) {
        imageField = request.student.img_url
        console.log('📷 Found image in request.student.img_url:', imageField)
    }

    console.log('Available request fields:', Object.keys(request))
    console.log('Student object:', request.student)

    // If no image field found, return placeholder with student initial
    if (!imageField) {
        const initial = request.student ? request.student.charAt(0).toUpperCase() : '?'
        console.log(`❌ No img field found for student ${request.student_id}, returning placeholder: ${initial}`)
        return `data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2QjcyODAiLz4KPHRleHQgeD0iMjAiIHk9IjI2IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj4ke2luaXRpYWx9PC90ZXh0Pjwvc3ZnPg==`
    }

    // Check if it's already a full URL or needs to be constructed
    let finalUrl = imageField
    if (imageField && !imageField.startsWith('http://') && !imageField.startsWith('https://')) {
        if (imageField.startsWith('/')) {
            finalUrl = `http://127.0.0.1:8000${imageField}`
        } else {
            finalUrl = `http://127.0.0.1:8000/storage/${imageField}`
        }
    }

    console.log(`✅ Final image URL for student ${request.student_id}:`, finalUrl)
    return finalUrl
}

const handleImageError = (event, request) => {
    console.log(`Profile image failed to load for student ${request.student}, falling back to initials`)
    const initial = request.student ? request.student.charAt(0).toUpperCase() : '?'
    event.target.src = `data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2QjcyODAiLz4KPHRleHQgeD0iMjAiIHk9IjI2IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj4ke2luaXRpYWx9PC90ZXh0Pjwvc3ZnPg==`
}

const fetchUser = async () => {
    const token = localStorage.getItem('authToken')

    if (!token) {
        console.log('No auth token found')
        return
    }

    isLoading.value = true
    apiError.value = null

    try {
        const response = await axios.get('http://127.0.0.1:8000/api/user', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })

        user.value = response.data
        localStorage.setItem('user_data', JSON.stringify(response.data))

        console.log('User loaded successfully:', user.value)
        console.log('Image from img column:', user.value.img)

    } catch (error) {
        console.error('Error fetching user:', error)
        apiError.value = error.response?.data?.message || error.message

        if (error.response?.status === 401) {
            localStorage.removeItem('authToken')
            localStorage.removeItem('user_data')
            console.log('User unauthorized, token removed')
        }
    } finally {
        isLoading.value = false
    }
}

onMounted(fetchLeaveRequests)
</script>

<style scoped>
/* Tailwind CSS is used */
</style>