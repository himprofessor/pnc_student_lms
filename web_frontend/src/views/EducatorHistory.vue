<template>
  <div class="container mx-auto px-8 py-12">
    <h1 class="text-2xl font-bold text-gray-800 mb-13">Leave Requests History</h1>
    
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Filters</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Student Filter -->
        <div>
          <label for="student" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
          <input
            type="text"
            id="student"
            v-model="filters.student"
            placeholder="Search by student name"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          />
        </div>
        
        <!-- Date Range Filter -->
        <div>
          <label for="dateRange" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
          <div class="flex space-x-2">
            <input
              type="date"
              v-model="filters.startDate"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            />
            <input
              type="date"
              v-model="filters.endDate"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>
        </div>
        
        <!-- Status Filter -->
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            id="status"
            v-model="filters.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="">All Statuses</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="pending">Pending</option>
          </select>
        </div>
        
        <!-- Leave Type Filter -->
        <div>
          <label for="leaveType" class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
          <select
            id="leaveType"
            v-model="filters.leaveType"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="">All Types</option>
            <option value="medical">Medical</option>
            <option value="personal">Personal</option>
            <option value="vacation">Vacation</option>
            <option value="academic">Academic</option>
          </select>
        </div>
      </div>
      
      <div class="mt-4 flex justify-end">
        <button
          @click="resetFilters"
          class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Reset Filters
        </button>
      </div>
    </div>
    
    <!-- Leave Requests Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Student
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Leave Type
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Start Date
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                End Date
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Processed On
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="request in filteredRequests" :key="request.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="text-sm font-medium text-gray-900">{{ request.studentName }}</div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ request.leaveType }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ formatDate(request.startDate) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ formatDate(request.endDate) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="statusClasses(request.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ request.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(request.processedDate) }}
              </td>
            </tr>
            <tr v-if="filteredRequests.length === 0">
              <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                No leave requests found matching your filters.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing <span class="font-medium">{{ pagination.startIndex + 1 }}</span> to <span class="font-medium">{{ pagination.endIndex }}</span> of <span class="font-medium">{{ filteredRequests.length }}</span> results
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="prevPage"
                :disabled="pagination.currentPage === 1"
                :class="{'opacity-50 cursor-not-allowed': pagination.currentPage === 1}"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
              <button
                v-for="page in pagination.totalPages"
                :key="page"
                @click="goToPage(page)"
                :class="{'bg-indigo-50 border-indigo-500 text-indigo-600': page === pagination.currentPage, 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': page !== pagination.currentPage}"
                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
              >
                {{ page }}
              </button>
              <button
                @click="nextPage"
                :disabled="pagination.currentPage === pagination.totalPages"
                :class="{'opacity-50 cursor-not-allowed': pagination.currentPage === pagination.totalPages}"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LeaveHistory',
  data() {
    return {
      filters: {
        student: '',
        startDate: '',
        endDate: '',
        status: '',
        leaveType: ''
      },
      leaveRequests: [
        // Sample data - in a real app, this would come from an API
        {
          id: 1,
          studentName: 'John Doe',
          leaveType: 'medical',
          startDate: '2023-06-01',
          endDate: '2023-06-03',
          status: 'approved',
          processedDate: '2023-05-30'
        },
        {
          id: 2,
          studentName: 'Jane Smith',
          leaveType: 'personal',
          startDate: '2023-06-05',
          endDate: '2023-06-07',
          status: 'rejected',
          processedDate: '2023-06-01'
        },
        {
          id: 3,
          studentName: 'Alex Johnson',
          leaveType: 'academic',
          startDate: '2023-06-10',
          endDate: '2023-06-12',
          status: 'approved',
          processedDate: '2023-06-08'
        },
        {
          id: 4,
          studentName: 'Sarah Williams',
          leaveType: 'vacation',
          startDate: '2023-06-15',
          endDate: '2023-06-20',
          status: 'approved',
          processedDate: '2023-06-12'
        },
        {
          id: 5,
          studentName: 'Michael Brown',
          leaveType: 'medical',
          startDate: '2023-06-18',
          endDate: '2023-06-19',
          status: 'rejected',
          processedDate: '2023-06-16'
        },
        {
          id: 6,
          studentName: 'Emily Davis',
          leaveType: 'personal',
          startDate: '2023-06-22',
          endDate: '2023-06-23',
          status: 'approved',
          processedDate: '2023-06-20'
        },
        {
          id: 7,
          studentName: 'David Wilson',
          leaveType: 'academic',
          startDate: '2023-06-25',
          endDate: '2023-06-27',
          status: 'approved',
          processedDate: '2023-06-23'
        },
        {
          id: 8,
          studentName: 'Jessica Miller',
          leaveType: 'medical',
          startDate: '2023-06-28',
          endDate: '2023-06-30',
          status: 'rejected',
          processedDate: '2023-06-26'
        }
      ],
      pagination: {
        currentPage: 1,
        itemsPerPage: 5,
        totalPages: 0,
        startIndex: 0,
        endIndex: 0
      }
    }
  },
  computed: {
    filteredRequests() {
      let filtered = this.leaveRequests;
      
      // Apply filters
      if (this.filters.student) {
        filtered = filtered.filter(request => 
          request.studentName.toLowerCase().includes(this.filters.student.toLowerCase())
        );
      }
      
      if (this.filters.startDate) {
        filtered = filtered.filter(request => 
          new Date(request.startDate) >= new Date(this.filters.startDate)
        );
      }
      
      if (this.filters.endDate) {
        filtered = filtered.filter(request => 
          new Date(request.endDate) <= new Date(this.filters.endDate)
        );
      }
      
      if (this.filters.status) {
        filtered = filtered.filter(request => 
          request.status === this.filters.status
        );
      }
      
      if (this.filters.leaveType) {
        filtered = filtered.filter(request => 
          request.leaveType === this.filters.leaveType
        );
      }
      
      // Update pagination
      this.pagination.totalPages = Math.ceil(filtered.length / this.pagination.itemsPerPage);
      this.pagination.startIndex = (this.pagination.currentPage - 1) * this.pagination.itemsPerPage;
      this.pagination.endIndex = Math.min(
        this.pagination.startIndex + this.pagination.itemsPerPage,
        filtered.length
      );
      
      // Return paginated results
      return filtered.slice(
        this.pagination.startIndex,
        this.pagination.endIndex
      );
    }
  },
  methods: {
    resetFilters() {
      this.filters = {
        student: '',
        startDate: '',
        endDate: '',
        status: '',
        leaveType: ''
      };
      this.pagination.currentPage = 1;
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    statusClasses(status) {
      return {
        'bg-green-100 text-green-800': status === 'approved',
        'bg-red-100 text-red-800': status === 'rejected',
        'bg-yellow-100 text-yellow-800': status === 'pending'
      };
    },
    prevPage() {
      if (this.pagination.currentPage > 1) {
        this.pagination.currentPage--;
      }
    },
    nextPage() {
      if (this.pagination.currentPage < this.pagination.totalPages) {
        this.pagination.currentPage++;
      }
    },
    goToPage(page) {
      this.pagination.currentPage = page;
    }
  },
  created() {
    // Initialize pagination
    this.pagination.totalPages = Math.ceil(this.leaveRequests.length / this.pagination.itemsPerPage);
    this.pagination.endIndex = Math.min(
      this.pagination.itemsPerPage,
      this.leaveRequests.length
    );
  }
}
</script>

<style scoped>
/* Additional custom styles can be added here if needed */
</style>