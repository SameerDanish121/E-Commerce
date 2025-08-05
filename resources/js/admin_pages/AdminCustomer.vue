<!-- <template>
  <div class="admin-customers-page">
    <section class="admin-header">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center py-4">
          <h1 class="page-title">Customer Management</h1>
          <button class="btn btn-primary" @click="refreshCustomers" :disabled="customerStore.isLoading">
            <i class="bi" :class="customerStore.isLoading ? 'bi-arrow-repeat spin' : 'bi-arrow-clockwise'"></i>
            Refresh Customers
          </button>
        </div>
      </div>
    </section>

    <section class="filters-section py-3 bg-light">
      <div class="container-fluid">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Search</label>
            <input v-model="searchQuery" type="text" class="form-control"
              placeholder="Search by name, username or ID..." @input="handleSearch">
          </div>
          <div class="col-md-2">
            <label class="form-label">Gender</label>
            <select v-model="selectedGender" class="form-select" @change="handleFilterChange">
              <option value="all">All Genders</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Sort By</label>
            <select v-model="sortBy" class="form-select" @change="handleSortChange">
              <option value="name">Name</option>
              <option value="total_orders">Total Orders</option>
              <option value="total_amount_spent">Total Spent</option>
              <option value="id">Customer ID</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Order</label>
            <select v-model="sortOrder" class="form-select" @change="handleSortChange">
              <option value="asc">Ascending</option>
              <option value="desc">Descending</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Min Orders</label>
            <input v-model="minOrders" type="number" class="form-control" min="0" @input="handleFilterChange">
          </div>
          <div class="col-md-1 d-flex align-items-end">
            <button class="btn btn-outline-secondary w-100" @click="resetFilters">
              <i class="bi bi-arrow-counterclockwise"></i>
            </button>
          </div>
        </div>
      </div>
    </section>

    <section class="customers-table-section py-4">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Username</th>
                    <th>Contact</th>
                    <th>Gender</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="customerStore.isLoading">
                    <td colspan="8" class="text-center py-5">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-else-if="filteredCustomers.length === 0">
                    <td colspan="8" class="text-center py-5">
                      <h5>No customers found</h5>
                      <p class="text-muted">Try adjusting your search or filter criteria</p>
                    </td>
                  </tr>
                  <tr v-for="customer in filteredCustomers" :key="customer.id">
                    <td>{{ customer.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img :src="customer.profile_pic || '/images/default-profile.png'" class="rounded-circle me-3"
                          width="40" height="40" alt="Customer">
                        <div>
                          <strong>{{ customer.name }}</strong>
                          <p class="text-muted small mb-0">{{ formatDate(customer.dob) }}</p>
                        </div>
                      </div>
                    </td>
                    <td>{{ customer.username }}</td>
                    <td>
                      <p class="mb-0">{{ customer.phone_no }}</p>
                      <p class="text-muted small mb-0">{{ customer.email || 'No email' }}</p>
                    </td>
                    <td>
                      <span class="badge" :class="genderBadgeClass(customer.gender)">
                        {{ customer.gender || 'Not specified' }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-primary rounded-pill">
                        {{ customer.total_orders || 0 }}
                      </span>
                    </td>
                    <td>Rs {{ customer.total_amount_spent ? customer.total_amount_spent.toLocaleString('en-IN') : '0.00'
                      }}</td>
                    <td>
                      <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" @click="viewCustomerDetails(customer)">
                          <i class="bi bi-eye"></i>
                        </button>

                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    <div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Customer Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedCustomer" class="row">
              <div class="col-md-4 text-center">
                <img :src="selectedCustomer.profile_pic || '/images/default-profile.png'"
                  class="img-thumbnail rounded-circle mb-3" width="150" height="150" alt="Customer">
                <h4>{{ selectedCustomer.name }}</h4>
                <p class="text-muted">{{ selectedCustomer.username }}</p>
              </div>
              <div class="col-md-8">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <div class="card bg-light mb-3">
                      <div class="card-body">
                        <h6 class="section-title">Basic Information</h6>
                        <p class="mb-2">
                          <i class="bi bi-gender-ambiguous me-2"></i>
                          <strong>Gender:</strong> {{ selectedCustomer.gender || 'Not specified' }}
                        </p>
                        <p class="mb-2">
                          <i class="bi bi-calendar me-2"></i>
                          <strong>Date of Birth:</strong> {{ formatDate(selectedCustomer.dob) || 'Not specified' }}
                        </p>
                        <p class="mb-0">
                          <i class="bi bi-envelope me-2"></i>
                          <strong>Email:</strong> {{ selectedCustomer.email || 'Not specified' }}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card bg-light mb-3">
                      <div class="card-body">
                        <h6 class="section-title">Order Statistics</h6>
                        <p class="mb-2">
                          <i class="bi bi-cart me-2"></i>
                          <strong>Total Orders:</strong> {{ selectedCustomer.total_orders || 0 }}
                        </p>
                        <p class="mb-2">
                          <i class="bi bi-currency-rupee me-2"></i>
                          <strong>Total Spent:</strong> Rs {{ selectedCustomer.total_amount_spent ?
                            selectedCustomer.total_amount_spent.toLocaleString('en-IN') : '0.00' }}
                        </p>
                        <p class="mb-0">
                          <i class="bi bi-telephone me-2"></i>
                          <strong>Phone:</strong> {{ selectedCustomer.phone_no || 'Not specified' }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card bg-light">
                  <div class="card-body">
                    <h6 class="section-title">Address</h6>
                    <p>{{ selectedCustomer.address || 'No address provided' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAllCustomerStore } from '../stores/AllCustomerStore';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';

const customerStore = useAllCustomerStore();
let customerDetailsModal = null;
const searchQuery = ref('');
const selectedGender = ref('all');
const sortBy = ref('name');
const sortOrder = ref('asc');
const minOrders = ref(null);
const selectedCustomer = ref(null);

onMounted(async () => {
  await customerStore.fetchAllCustomers();
  customerDetailsModal = new Modal(document.getElementById('customerDetailsModal'));
});

const filteredCustomers = computed(() => {
  let customers = [...customerStore.customers];
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    customers = customers.filter(customer =>
      customer.name.toLowerCase().includes(query) ||
      customer.username.toLowerCase().includes(query) ||
      customer.id.toString().includes(query)
    );
  }

  if (selectedGender.value !== 'all') {
    customers = customers.filter(customer =>
      customer.gender === selectedGender.value
    );
  }
  if (minOrders.value) {
    customers = customers.filter(customer =>
      customer.total_orders >= parseInt(minOrders.value)
    );
  }
  customers.sort((a, b) => {
    let compareA, compareB;

    if (sortBy.value === 'name') {
      compareA = a.name.toLowerCase();
      compareB = b.name.toLowerCase();
    } else if (sortBy.value === 'total_orders') {
      compareA = a.total_orders || 0;
      compareB = b.total_orders || 0;
    } else if (sortBy.value === 'total_amount_spent') {
      compareA = a.total_amount_spent || 0;
      compareB = b.total_amount_spent || 0;
    } else {
      compareA = a.id;
      compareB = b.id;
    }

    if (sortOrder.value === 'asc') {
      return compareA > compareB ? 1 : -1;
    } else {
      return compareA < compareB ? 1 : -1;
    }
  });

  return customers;
});

const refreshCustomers = async () => {
  await customerStore.fetchAllCustomers();
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedGender.value = 'all';
  minOrders.value = null;
  sortBy.value = 'name';
  sortOrder.value = 'asc';
};

const handleSearch = () => {
};

const handleFilterChange = () => {
};

const handleSortChange = () => {
};

const formatDate = (dateString) => {
  if (!dateString) return 'Not specified';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const genderBadgeClass = (gender) => {
  switch (gender) {
    case 'Male': return 'bg-info text-white';
    case 'Female': return 'bg-pink text-white';
    case 'Other': return 'bg-secondary text-white';
    default: return 'bg-light text-dark';
  }
};

const viewCustomerDetails = (customer) => {
  selectedCustomer.value = { ...customer };
  customerDetailsModal.show();
};

const editCustomer = (customer) => {
  Swal.fire({
    title: 'Edit Customer',
    text: 'Edit functionality would be implemented here',
    icon: 'info',
    confirmButtonText: 'OK'
  });
};

const confirmDeleteCustomer = (customer) => {
  Swal.fire({
    title: 'Are you sure?',
    text: `You are about to delete ${customer.name}. This action cannot be undone!`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      deleteCustomer(customer.id);
    }
  });
};

const deleteCustomer = async (customerId) => {
  try {
    Swal.fire({
      icon: 'success',
      title: 'Deleted!',
      text: 'Customer has been deleted (simulated).',
      timer: 1500,
      showConfirmButton: false
    });

    // Refresh the customer list
    await customerStore.fetchAllCustomers();
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.message || 'Failed to delete customer',
    });
  }
};
</script>

<style scoped>
.admin-customers-page {
  padding-top: 1rem;
}

.admin-header {
  border-bottom: 1px solid #eee;
  margin-bottom: 1.5rem;
}

.page-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.section-title {
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #6c757d;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #eee;
}

.badge {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.35em 0.65em;
}

.bg-pink {
  background-color: #ff6b9d;
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

/* Responsive adjustments */
@media (max-width: 992px) {
  .page-title {
    font-size: 1.5rem;
  }
}

@media (max-width: 768px) {
  .admin-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .filters-section .row>div {
    margin-bottom: 0.5rem;
  }

  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .table {
    width: 100%;
    margin-bottom: 1rem;
    display: block;
  }

  .table thead {
    display: none;
  }

  .table tbody {
    display: block;
    width: 100%;
  }

  .table tr {
    display: block;
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
  }

  .table td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border-bottom: 1px solid #dee2e6;
    position: relative;
    padding-left: 50%;
  }

  .table td::before {
    content: attr(data-label);
    position: absolute;
    left: 0.75rem;
    width: 45%;
    padding-right: 1rem;
    font-weight: 600;
    text-align: left;
  }

  .table td:last-child {
    border-bottom: 0;
  }

  /* Set data labels for each cell */
  .table td:nth-child(1)::before {
    content: "ID";
  }

  .table td:nth-child(2)::before {
    content: "Customer";
  }

  .table td:nth-child(3)::before {
    content: "Username";
  }

  .table td:nth-child(4)::before {
    content: "Contact";
  }

  .table td:nth-child(5)::before {
    content: "Gender";
  }

  .table td:nth-child(6)::before {
    content: "Orders";
  }

  .table td:nth-child(7)::before {
    content: "Total Spent";
  }

  .table td:nth-child(8)::before {
    content: "Actions";
  }

  .table td .d-flex {
    justify-content: flex-end;
  }
}

@media (max-width: 576px) {
  .section-title {
    font-size: 0.8rem;
  }

  .modal-dialog {
    margin: 0.5rem auto;
  }
}
</style>
 -->
<template>
  <div class="admin-customers-page">
    <!-- <section class="admin-header">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center py-4">
          <h1 class="page-title">Customer Management</h1>
          <button class="btn btn-primary" @click="refreshCustomers" :disabled="customerStore.isLoading">
            <i class="bi" :class="customerStore.isLoading ? 'bi-arrow-repeat spin' : 'bi-arrow-clockwise'"></i>
            Refresh Customers
          </button>
        </div>
      </div>
    </section> -->
    <section class="admin-header">
      <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">

          <h1 class="page-title mb-0 text-nowrap" style="font-size: 1.75rem;">
            Customer Management
          </h1>

          <button class="btn btn-primary text-nowrap px-3 py-2" @click="refreshCustomers"
            :disabled="customerStore.isLoading" style="font-size: 1rem;">
            <i class="bi" :class="customerStore.isLoading ? 'bi-arrow-repeat spin' : 'bi-arrow-clockwise'"></i>
            Refresh Customers
          </button>

        </div>
      </div>
    </section>

    <section class="filters-section py-3 bg-light">
      <div class="container-fluid">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Search</label>
            <input v-model="searchQuery" type="text" class="form-control"
              placeholder="Search by name, username or ID..."
              @input="searchQuery = $event.target.value.replace(/[^a-zA-Z\s]/g, '').slice(0, 20)">

          </div>
          <div class="col-md-3">
            <label class="form-label">Gender</label>
            <select v-model="selectedGender" class="form-select" @change="handleFilterChange">
              <option value="all">All Genders</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Min Spend on Orders</label>
            <input v-model="minOrders" type="number" class="form-control" min="0" @input="handleFilterChange">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-outline-secondary w-100" @click="resetFilters">
              <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- <section class="customers-table-section py-4">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table id="customersTable" class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>ID <span class="sort-icon"></span></th>
                    <th>Customer <span class="sort-icon"></span></th>
                    <th>Username <span class="sort-icon"></span></th>
                    <th>Contact <span class="sort-icon"></span></th>
                    <th>Gender <span class="sort-icon"></span></th>
                    <th>Orders <span class="sort-icon"></span></th>
                    <th>Total Spent <span class="sort-icon"></span></th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="customerStore.isLoading">
                    <td colspan="8" class="text-center py-5">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-else-if="filteredCustomers.length === 0">
                    <td colspan="8" class="text-center py-5">
                      <h5>No customers found</h5>
                      <p class="text-muted">Try adjusting your search or filter criteria</p>
                    </td>
                  </tr>
                  <tr v-for="customer in filteredCustomers" :key="customer.id">
                    <td>{{ customer.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img :src="customer.profile_pic || '/images/default-profile.png'" class="rounded-circle me-3"
                          width="40" height="40" alt="Customer">
                        <div>
                          <strong>{{ customer.name }}</strong>
                          <p class="text-muted small mb-0">{{ formatDate(customer.dob) }}</p>
                        </div>
                      </div>
                    </td>
                    <td>{{ customer.username }}</td>
                    <td>
                      <p class="mb-0">{{ customer.phone_no }}</p>
                      <p class="text-muted small mb-0">{{ customer.email || 'No email' }}</p>
                    </td>
                    <td>
                      <span class="badge" :class="genderBadgeClass(customer.gender)">
                        {{ customer.gender || 'Not specified' }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-primary rounded-pill">
                        {{ customer.total_orders || 0 }}
                      </span>
                    </td>
                    <td>Rs {{ customer.total_amount_spent ? customer.total_amount_spent.toLocaleString('en-IN') : '0.00'
                      }}</td>
                    <td>
                      <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" @click="viewCustomerDetails(customer)">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section> -->
    <section class="customers-table-section py-4">
      <div class="container-fluid">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table id="customersTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="sortable" @click="sortTable('id')">
                      ID
                      <span class="sort-icon ms-1">
                        <i class="bi bi-chevron-up"
                          :class="{ 'text-primary': currentSortColumn === 'id' && sortDirection === 'asc' }"></i>
                        <i class="bi bi-chevron-down"
                          :class="{ 'text-primary': currentSortColumn === 'id' && sortDirection === 'desc' }"></i>
                      </span>
                    </th>
                    <th class="sortable" @click="sortTable('name')">
                      Customer
                      <span class="sort-icon ms-1">
                        <i class="bi bi-chevron-up"
                          :class="{ 'text-primary': currentSortColumn === 'name' && sortDirection === 'asc' }"></i>
                        <i class="bi bi-chevron-down"
                          :class="{ 'text-primary': currentSortColumn === 'name' && sortDirection === 'desc' }"></i>
                      </span>
                    </th>
                    <th class="sortable" @click="sortTable('username')">
                      Username
                      <span class="sort-icon ms-1">
                        <i class="bi bi-chevron-up"
                          :class="{ 'text-primary': currentSortColumn === 'username' && sortDirection === 'asc' }"></i>
                        <i class="bi bi-chevron-down"
                          :class="{ 'text-primary': currentSortColumn === 'username' && sortDirection === 'desc' }"></i>
                      </span>
                    </th>
                    <th>Contact</th>
                    <th class="sortable" @click="sortTable('gender')">
                      Gender
                      <span class="sort-icon ms-1">
                        <i class="bi bi-chevron-up"
                          :class="{ 'text-primary': currentSortColumn === 'gender' && sortDirection === 'asc' }"></i>
                        <i class="bi bi-chevron-down"
                          :class="{ 'text-primary': currentSortColumn === 'gender' && sortDirection === 'desc' }"></i>
                      </span>
                    </th>
                    <th class="sortable text-center" @click="sortTable('total_orders')">
                      Orders
                      <span class="sort-icon ms-1">
                        <i class="bi bi-chevron-up"
                          :class="{ 'text-primary': currentSortColumn === 'total_orders' && sortDirection === 'asc' }"></i>
                        <i class="bi bi-chevron-down"
                          :class="{ 'text-primary': currentSortColumn === 'total_orders' && sortDirection === 'desc' }"></i>
                      </span>
                    </th>
                    <th class="sortable text-end" @click="sortTable('total_amount_spent')">
                      Total Spent
                      <span class="sort-icon ms-1">
                        <i class="bi bi-chevron-up"
                          :class="{ 'text-primary': currentSortColumn === 'total_amount_spent' && sortDirection === 'asc' }"></i>
                        <i class="bi bi-chevron-down"
                          :class="{ 'text-primary': currentSortColumn === 'total_amount_spent' && sortDirection === 'desc' }"></i>
                      </span>
                    </th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="customerStore.isLoading">
                    <td colspan="8" class="text-center py-5">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-else-if="filteredCustomers.length === 0">
                    <td colspan="8" class="text-center py-5">
                      <h5>No customers found</h5>
                      <p class="text-muted">Try adjusting your search or filter criteria</p>
                    </td>
                  </tr>
                  <tr v-for="customer in sortedCustomers" :key="customer.id">
                    <td class="fw-semibold">#{{ customer.id }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img :src="customer.profile_pic || '/images/default-profile.png'"
                          class="rounded-circle me-3 object-fit-cover" width="40" height="40" alt="Customer">
                        <div>
                          <strong>{{ customer.name }}</strong>
                          <p class="text-muted small mb-0">{{ formatDate(customer.dob) }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark">{{ customer.username }}</span>
                    </td>
                    <!-- <td>
                      <p class="mb-0">{{ customer.phone_no || 'N/A' }}</p>
                      <p class="text-muted small mb-0">{{ customer.email || 'No email' }}</p>
                    </td> -->
                    <td>
                      <p class="mb-0">{{ customer.phone_no || 'N/A' }}</p>
                      <p class="text-muted small mb-0 d-none d-sm-block">
                        {{ customer.email || 'No email' }}
                      </p>
                    </td>

                    <td>
                      <span class="badge" :class="genderBadgeClass(customer.gender)">
                        {{ customer.gender || 'Not specified' }}
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-primary rounded-pill px-2 py-1">
                        {{ customer.total_orders || 0 }}
                      </span>
                    </td>
                    <td class="text-end fw-semibold">
                      Rs {{ customer.total_amount_spent ? customer.total_amount_spent.toLocaleString('en-IN') : '0.00'
                      }}
                    </td>
                    <td class="text-end">
                      <div class="d-flex gap-2 justify-content-end">
                        <button class="btn btn-sm btn-outline-primary rounded-circle"
                          @click="viewCustomerDetails(customer)" title="View Details">
                          <i class="bi bi-eye"></i>
                        </button>

                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top"
              v-if="filteredCustomers.length > 0">
              <div class="text-muted small">
                Showing {{ filteredCustomers.length }} of {{ customerStore.customers.length }} entries
              </div>
              <nav aria-label="Page navigation">
                <!-- <ul class="pagination pagination-sm mb-0">
                  <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                  </li>
                  <li class="page-item active"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                  <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                  </li>
                </ul> -->
                <!-- <ul class="pagination pagination-sm mb-0">
                  <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <a class="page-link" href="#" @click.prevent="goToPage(currentPage - 1)">Previous</a>
                  </li>

                  <li class="page-item" v-for="page in totalPages" :key="page"
                    :class="{ active: currentPage === page }">
                    <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
                  </li>

                  <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <a class="page-link" href="#" @click.prevent="goToPage(currentPage + 1)">Next</a>
                  </li>
                </ul> -->

              </nav>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- <div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Customer Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedCustomer" class="row">
              <div class="col-md-4 text-center">
                <img :src="selectedCustomer.profile_pic || '/images/default-profile.png'"
                  class="img-thumbnail rounded-circle mb-3" width="150" height="150" alt="Customer">
                <h4>{{ selectedCustomer.name }}</h4>
                <p class="text-muted">{{ selectedCustomer.username }}</p>
              </div>
              <div class="col-md-8">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <div class="card bg-light mb-3">
                      <div class="card-body">
                        <h6 class="section-title">Basic Information</h6>
                        <p class="mb-2">
                          <i class="bi bi-gender-ambiguous me-2"></i>
                          <strong>Gender:</strong> {{ selectedCustomer.gender || 'Not specified' }}
                        </p>
                        <p class="mb-2">
                          <i class="bi bi-calendar me-2"></i>
                          <strong>Date of Birth:</strong> {{ formatDate(selectedCustomer.dob) || 'Not specified' }}
                        </p>
                        <p class="mb-0">
                          <i class="bi bi-envelope me-2"></i>
                          <strong>Email:</strong> {{ selectedCustomer.email || 'Not specified' }}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card bg-light mb-3">
                      <div class="card-body">
                        <h6 class="section-title">Order Statistics</h6>
                        <p class="mb-2">
                          <i class="bi bi-cart me-2"></i>
                          <strong>Total Orders:</strong> {{ selectedCustomer.total_orders || 0 }}
                        </p>
                        <p class="mb-2">
                          <i class="bi bi-currency-rupee me-2"></i>
                          <strong>Total Spent:</strong> Rs {{ selectedCustomer.total_amount_spent ?
                            selectedCustomer.total_amount_spent.toLocaleString('en-IN') : '0.00' }}
                        </p>
                        <p class="mb-0">
                          <i class="bi bi-telephone me-2"></i>
                          <strong>Phone:</strong> {{ selectedCustomer.phone_no || 'Not specified' }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card bg-light">
                  <div class="card-body">
                    <h6 class="section-title">Address</h6>
                    <p>{{ selectedCustomer.address || 'No address provided' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div> -->
    <div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Customer Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div v-if="selectedCustomer" class="row">
              <div class="col-md-4 text-center">
                <div class="d-flex justify-content-center">
                  <img :src="selectedCustomer.profile_pic || '/images/default-profile.png'" class="customer-avatar"
                    alt="Customer" />
                </div>
                <h4>{{ selectedCustomer.name }}</h4>
                <p class="text-muted">{{ selectedCustomer.username }}</p>
              </div>
              <div class="col-md-8">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <div class="card bg-light h-100">
                      <div class="card-body">
                        <h6 class="section-title">Basic Information</h6>
                        <p class="mb-2">
                          <i v-if="selectedCustomer.gender === 'Male'" class="bi bi-gender-male me-2 text-primary"></i>
                          <i v-else-if="selectedCustomer.gender === 'Female'"
                            class="bi bi-gender-female me-2 text-danger"></i>
                          <i v-else class="bi bi-gender-ambiguous me-2 text-muted"></i>
                          <strong>Gender:</strong> {{ selectedCustomer.gender || 'Not specified' }}
                        </p>
                        <p class="mb-2">
                          <i class="bi bi-calendar-date me-2"></i>
                          <strong>Date of Birth:</strong> {{ formatDate(selectedCustomer.dob) || 'Not specified' }}
                        </p>
                        <p class="mb-0">
                          <i class="bi bi-envelope me-2"></i>
                          <strong>Email:</strong> {{ selectedCustomer.email || 'Not specified' }}
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Order Stats -->
                  <div class="col-md-6">
                    <div class="card bg-light h-100">
                      <div class="card-body">
                        <h6 class="section-title">Order Statistics</h6>
                        <p class="mb-2">
                          <i class="bi bi-cart me-2"></i>
                          <strong>Total Orders:</strong> {{ formatShortNumber(selectedCustomer.total_orders) }}
                        </p>
                        <p class="mb-2">
                          <i class="bi bi-currency-rupee me-2"></i>
                          <strong>Total Spent:</strong>
                          Rs {{ formatShortNumber(selectedCustomer.total_amount_spent) }}
                        </p>
                        <p class="mb-0">
                          <i class="bi bi-telephone me-2"></i>
                          <strong>Phone:</strong> {{ selectedCustomer.phone_no || 'Not specified' }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Address -->
                <div class="card bg-light">
                  <div class="card-body">
                    <h6 class="section-title">Address</h6>
                    <p class="mb-0">{{ selectedCustomer.address || 'No address provided' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>



  </div>
</template>
<!-- 
<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { useAllCustomerStore } from '../stores/AllCustomerStore';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';

DataTable.use(DataTablesCore);

const customerStore = useAllCustomerStore();
let customerDetailsModal = null;
let dataTable = null;
const searchQuery = ref('');
const selectedGender = ref('all');
const minOrders = ref(null);
const selectedCustomer = ref(null);

onMounted(async () => {
  await customerStore.fetchAllCustomers();
  customerDetailsModal = new Modal(document.getElementById('customerDetailsModal'));
  initializeDataTable();
});

const initializeDataTable = () => {
  nextTick(() => {
    const table = document.getElementById('customersTable');
    if (table) {
      dataTable = new DataTablesCore(table, {
        responsive: true,
        columnDefs: [
          { orderable: true, targets: [0, 1, 2, 3, 4, 5, 6] },
          { orderable: false, targets: [7] }
        ],
        language: {
          search: "",
          searchPlaceholder: "Search within table...",
          lengthMenu: "Show _MENU_ entries",
          paginate: {
            previous: '<i class="bi bi-chevron-left"></i>',
            next: '<i class="bi bi-chevron-right"></i>'
          }
        },
        dom: '<"datatable-header"f<"ms-auto"l>>rt<"datatable-footer"ip>',
        initComplete: function() {
          const searchInput = $('.dataTables_filter input');
          searchInput.addClass('form-control form-control-sm');
          searchInput.css('width', '250px');
        
          $('.dataTables_length select').addClass('form-select form-select-sm');
        },
        drawCallback: function() {
          const headers = $(table).find('th');
          headers.each(function(index) {
            if (index !== 7) { 
              const icon = $(this).find('.sort-icon');
              const order = dataTable.order();
              if (order.length > 0 && order[0][0] === index) {
                icon.html(order[0][1] === 'asc' ? 
                  '<i class="bi bi-chevron-up ms-1"></i>' : 
                  '<i class="bi bi-chevron-down ms-1"></i>');
              } else {
                icon.html('<i class="bi bi-chevron-up ms-1 text-muted"></i>' + 
                         '<i class="bi bi-chevron-down ms-1 text-muted"></i>');
              }
            }
          });
        }
      });
    
      $(table).find('th').each(function(index) {
        if (index !== 7) {
          $(this).append('<span class="sort-icon"><i class="bi bi-chevron-up ms-1 text-muted"></i><i class="bi bi-chevron-down ms-1 text-muted"></i></span>');
        }
      });
    }
  });
};

const filteredCustomers = computed(() => {
  let customers = [...customerStore.customers];
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    customers = customers.filter(customer =>
      customer.name.toLowerCase().includes(query) ||
      customer.username.toLowerCase().includes(query) ||
      customer.id.toString().includes(query)
    );
  }

  if (selectedGender.value !== 'all') {
    customers = customers.filter(customer =>
      customer.gender === selectedGender.value
    );
  }
  
  if (minOrders.value) {
    customers = customers.filter(customer =>
      customer.total_orders >= parseInt(minOrders.value)
    );
  }

  return customers;
});

const refreshCustomers = async () => {
  await customerStore.fetchAllCustomers();
  if (dataTable) {
    dataTable.destroy();
  }
  initializeDataTable();
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedGender.value = 'all';
  minOrders.value = null;
};

const handleSearch = () => {
  if (dataTable) {
    dataTable.search(searchQuery.value).draw();
  }
};

const handleFilterChange = () => {
};

const formatDate = (dateString) => {
  if (!dateString) return 'Not specified';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const genderBadgeClass = (gender) => {
  switch (gender) {
    case 'Male': return 'bg-info text-white';
    case 'Female': return 'bg-pink text-white';
    case 'Other': return 'bg-secondary text-white';
    default: return 'bg-light text-dark';
  }
};

const viewCustomerDetails = (customer) => {
  selectedCustomer.value = { ...customer };
  customerDetailsModal.show();
};
</script> -->
<script setup>

import { ref, computed, onMounted } from 'vue';
import { useAllCustomerStore } from '../stores/AllCustomerStore';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';



const customerStore = useAllCustomerStore();
let customerDetailsModal = null;
const searchQuery = ref('');
const selectedGender = ref('all');
const minOrders = ref(null);
const selectedCustomer = ref(null);
const currentSortColumn = ref('id');
const sortDirection = ref('asc');
function formatShortNumber(num) {
  if (!num) return '0';
  const abs = Math.abs(num);
  if (abs >= 1.0e+9) return (num / 1.0e+9).toFixed(1).replace(/\.0$/, '') + 'B';
  if (abs >= 1.0e+6) return (num / 1.0e+6).toFixed(1).replace(/\.0$/, '') + 'M';
  if (abs >= 1.0e+3) return (num / 1.0e+3).toFixed(1).replace(/\.0$/, '') + 'K';
  return num.toString();
}

onMounted(async () => {
  await customerStore.fetchAllCustomers();
  customerDetailsModal = new Modal(document.getElementById('customerDetailsModal'));
});

const sortTable = (column) => {
  if (currentSortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    currentSortColumn.value = column;
    sortDirection.value = 'asc';
  }
};

const sortedCustomers = computed(() => {
  const customers = [...filteredCustomers.value];

  return customers.sort((a, b) => {
    let valA = a[currentSortColumn.value];
    let valB = b[currentSortColumn.value];

    // Handle null/undefined values
    if (valA == null) valA = '';
    if (valB == null) valB = '';

    // Numeric comparison for numeric fields
    if (['id', 'total_orders', 'total_amount_spent'].includes(currentSortColumn.value)) {
      return sortDirection.value === 'asc' ? valA - valB : valB - valA;
    }

    // String comparison for text fields
    if (typeof valA === 'string' && typeof valB === 'string') {
      return sortDirection.value === 'asc'
        ? valA.localeCompare(valB)
        : valB.localeCompare(valA);
    }

    return 0;
  });
});

const filteredCustomers = computed(() => {
  let customers = [...customerStore.customers];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    customers = customers.filter(customer =>
      customer.name.toLowerCase().includes(query) ||
      customer.username.toLowerCase().includes(query) ||
      customer.id.toString().includes(query) ||
      (customer.email && customer.email.toLowerCase().includes(query)) ||
      (customer.phone_no && customer.phone_no.includes(query))
    );
  }

  if (selectedGender.value !== 'all') {
    customers = customers.filter(customer =>
      customer.gender === selectedGender.value
    );
  }

  if (minOrders.value) {
    customers = customers.filter(customer =>
      customer.total_orders >= parseInt(minOrders.value)
    );
  }

  return customers;
});

const refreshCustomers = async () => {
  await customerStore.fetchAllCustomers();
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedGender.value = 'all';
  minOrders.value = null;
  currentSortColumn.value = 'id';
  sortDirection.value = 'asc';
};

const formatDate = (dateString) => {
  if (!dateString) return 'Not specified';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const genderBadgeClass = (gender) => {
  switch (gender?.toLowerCase()) {
    case 'male': return 'bg-info text-white';
    case 'female': return 'bg-pink text-white';
    case 'other': return 'bg-secondary text-white';
    default: return 'bg-light text-dark';
  }
};

const viewCustomerDetails = (customer) => {
  selectedCustomer.value = { ...customer };
  customerDetailsModal.show();
};
</script>

<style scoped>
.customers-table-section {
  padding-top: 1rem;
}

.card {
  border-radius: 0.75rem;
  overflow: hidden;
}

.table {
  margin-bottom: 0;
}

.table th {
  padding: 1rem;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #6c757d;
  white-space: nowrap;
  vertical-align: middle;
}

.table td {
  padding: 1rem;
  vertical-align: middle;
  border-top: 1px solid #f0f0f0;
}

.sortable {
  cursor: pointer;
  transition: all 0.2s;
}

.sortable:hover {
  background-color: #f8f9fa;
}

.sort-icon {
  display: inline-flex;
  flex-direction: column;
  line-height: 0.8;
}

.sort-icon i {
  font-size: 0.7rem;
  color: #dee2e6;
  transition: all 0.2s;
}

.sort-icon i:first-child {
  margin-bottom: -0.2rem;
}

.sort-icon i.text-primary {
  color: #0d6efd !important;
}

.badge {
  font-weight: 500;
  padding: 0.35em 0.65em;
}

.bg-pink {
  background-color: #ff6b9d;
}

.object-fit-cover {
  object-fit: cover;
}

/* Responsive adjustments */
@media (max-width: 992px) {

  .table th,
  .table td {
    padding: 0.75rem;
  }
}

@media (max-width: 768px) {
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .table thead {
    display: none;
  }

  .table tbody {
    display: block;
    width: 100%;
  }

  .table tr {
    display: block;
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 0.5rem;
  }

  .table td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border: none;
    border-bottom: 1px solid #f0f0f0;
    position: relative;
    padding-left: 50%;
    text-align: right;
  }

  .table td::before {
    content: attr(data-label);
    position: absolute;
    left: 0.75rem;
    width: 45%;
    padding-right: 1rem;
    font-weight: 600;
    text-align: left;
    color: #6c757d;
    font-size: 0.8rem;
  }

  .table td:last-child {
    border-bottom: 0;
  }

  /* Set data labels for each cell */
  .table td:nth-child(1)::before {
    content: "ID";
  }

  .table td:nth-child(2)::before {
    content: "Customer";
  }

  .table td:nth-child(3)::before {
    content: "Username";
  }

  .table td:nth-child(4)::before {
    content: "Contact";
  }

  .table td:nth-child(5)::before {
    content: "Gender";
  }

  .table td:nth-child(6)::before {
    content: "Orders";
  }

  .table td:nth-child(7)::before {
    content: "Total Spent";
  }

  .table td:nth-child(8)::before {
    content: "Actions";
  }

  /* Special handling for some cells */
  .table td .d-flex {
    justify-content: flex-end !important;
  }

  .table td .badge {
    margin-left: auto;
  }
}

@media (max-width: 576px) {
  .card-body {
    padding: 0.5rem;
  }

  .table td {
    padding-left: 40%;
  }

  .table td::before {
    width: 35%;
  }
}

.customer-avatar {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  object-fit: cover;
  object-position: center;
  display: block;
}

@media (max-width: 521px) {
  .page-title {
    font-size: 1.3rem !important;
  }

  .btn-primary {
    font-size: 0.85rem !important;
    padding: 0.4rem 0.75rem !important;
  }
}
</style>
<!-- <style scoped>
.admin-customers-page {
  padding-top: 1rem;
}

.admin-header {
  border-bottom: 1px solid #eee;
  margin-bottom: 1.5rem;
}

.page-title {
  font-size: 1.8rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.section-title {
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #6c757d;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #eee;
}

.badge {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.35em 0.65em;
}

.bg-pink {
  background-color: #ff6b9d;
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

/* DataTables customization */
.datatable-header {
  display: flex;
  align-items: center;
  padding: 1rem 1rem 0.5rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.datatable-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1rem;
  background-color: #f8f9fa;
  border-top: 1px solid #dee2e6;
}

.dataTables_wrapper .dataTables_filter input {
  border: 1px solid #dee2e6;
  padding: 0.375rem 0.75rem;
  border-radius: 0.25rem;
  height: 38px;
}

.dataTables_wrapper .dataTables_paginate {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
  padding: 0.375rem 0.75rem;
  border: 1px solid #dee2e6;
  border-radius: 0.25rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
  background: #0d6efd;
  color: white !important;
  border: 1px solid #0d6efd;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: #e9ecef;
  border: 1px solid #dee2e6;
}

.dataTables_wrapper .dataTables_length {
  margin-left: auto;
}

.sort-icon {
  display: inline-flex;
  flex-direction: column;
  margin-left: 0.25rem;
  line-height: 0.8;
}

.sort-icon i {
  font-size: 0.7rem;
}

.sort-icon i:first-child {
  margin-bottom: -0.2rem;
}

/* Responsive adjustments */
@media (max-width: 992px) {
  .page-title {
    font-size: 1.5rem;
  }
  
  .dataTables_wrapper .dataTables_filter input {
    width: 200px !important;
  }
}

@media (max-width: 768px) {
  .admin-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .filters-section .row>div {
    margin-bottom: 0.5rem;
  }

  .datatable-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .dataTables_wrapper .dataTables_length {
    margin-left: 0;
  }
  
  .dataTables_wrapper .dataTables_filter input {
    width: 100% !important;
  }

  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .table {
    width: 100%;
    margin-bottom: 1rem;
    display: block;
  }

  .table thead {
    display: none;
  }

  .table tbody {
    display: block;
    width: 100%;
  }

  .table tr {
    display: block;
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
  }

  .table td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border-bottom: 1px solid #dee2e6;
    position: relative;
    padding-left: 50%;
  }

  .table td::before {
    content: attr(data-label);
    position: absolute;
    left: 0.75rem;
    width: 45%;
    padding-right: 1rem;
    font-weight: 600;
    text-align: left;
  }

  .table td:last-child {
    border-bottom: 0;
  }

  /* Set data labels for each cell */
  .table td:nth-child(1)::before {
    content: "ID";
  }

  .table td:nth-child(2)::before {
    content: "Customer";
  }

  .table td:nth-child(3)::before {
    content: "Username";
  }

  .table td:nth-child(4)::before {
    content: "Contact";
  }

  .table td:nth-child(5)::before {
    content: "Gender";
  }

  .table td:nth-child(6)::before {
    content: "Orders";
  }

  .table td:nth-child(7)::before {
    content: "Total Spent";
  }

  .table td:nth-child(8)::before {
    content: "Actions";
  }

  .table td .d-flex {
    justify-content: flex-end;
  }
}

@media (max-width: 576px) {
  .section-title {
    font-size: 0.8rem;
  }

  .modal-dialog {
    margin: 0.5rem auto;
  }
  
  .datatable-footer {
    flex-direction: column;
    gap: 1rem;
  }
}
</style> -->