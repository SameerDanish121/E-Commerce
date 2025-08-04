<!-- <template>
  <div class="admin-profiles-page">
    <section class="admin-header">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center py-4">
          <h1 class="page-title">Admin Profiles</h1>
          <div class="d-flex gap-3">
            <button class="btn btn-info" @click="openGlobalPermissionsModal">
              <i class="bi bi-shield-lock me-2"></i>Manage Core Permissions
            </button>
            <button class="btn btn-primary" @click="openAddAdminModal">
              <i class="bi bi-plus-circle me-2"></i>Add Admin
            </button>
          </div>
        </div>
      </div>
    </section>
    <section class="filters-section py-3 bg-light">
      <div class="container-fluid">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Search Admins</label>
            <input v-model="searchQuery" type="text" class="form-control"
              placeholder="Search by name, email or username..." @input="handleSearch">
          </div>
          <div class="col-md-3">
            <label class="form-label">Designation</label>
            <select v-model="selectedDesignation" class="form-select" @change="handleDesignationChange">
              <option value="all">All Designations</option>
              <option v-for="designation in uniqueDesignations" :key="designation" :value="designation">
                {{ designation }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Sort By</label>
            <select v-model="sortBy" class="form-select" @change="handleSortChange">
              <option value="name">Name</option>
              <option value="username">Username</option>
              <option value="designation">Designation</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-outline-secondary w-100" @click="resetFilters">
              <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
            </button>
          </div>
        </div>
      </div>
    </section>

    <section class="admins-table-section py-4">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Profile</th>
                    <th>Admin Details</th>
                    <th>Contact</th>
                    <th>Designation</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="adminStore.isLoading">
                    <td colspan="5" class="text-center py-5">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-else-if="filteredAdmins.length === 0">
                    <td colspan="5" class="text-center py-5">
                      <h5>No admins found</h5>
                      <p class="text-muted">Try adjusting your search or filter criteria</p>
                    </td>
                  </tr>
                  <tr v-for="admin in filteredAdmins" :key="admin.id">
                    <td>
                      <img :src="admin.profile_pic || '/images/default-admin.png'" class="rounded-circle" width="50"
                        height="50" alt="Admin">
                    </td>
                    <td>
                      <strong>{{ admin.name }}</strong>
                      <p class="text-muted small mb-0">{{ admin.username }}</p>
                      <p class="text-muted small mb-0">{{ admin.email }}</p>
                    </td>
                    <td>
                      <p class="mb-0">{{ admin.contact_no || 'Not provided' }}</p>
                      <p class="text-muted small mb-0 truncate-address">
                        {{ admin.address || 'Not provided' }}
                      </p>
                    </td>

                    <td>
                      <span class="badge bg-primary">
                        {{ admin.designation || 'Not specified' }}
                      </span>
                    </td>

                    <td>
                      <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" @click="openViewModal(admin)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" @click="openEditModal(admin)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info" @click="openPermissionsModal(admin)">
                          <i class="bi bi-shield-lock"></i> Permissions
                        </button>
                        <button class="btn btn-sm btn-outline-danger" @click="confirmDeleteAdmin(admin)">
                          <i class="bi bi-trash"></i>
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
    <div class="modal fade" id="viewAdminModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Admin Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedAdmin" class="text-center">
              <img :src="selectedAdmin.profile_pic || '/images/default-admin.png'" class="rounded-circle mb-3"
                width="150" height="150" alt="Admin">
              <h3>{{ selectedAdmin.name }}</h3>
              <p class="text-muted">{{ selectedAdmin.designation || 'No designation' }}</p>

              <div class="card mt-4">
                <div class="card-body text-start">
                  <h5 class="card-title">Contact Information</h5>
                  <p class="mb-2">
                    <i class="bi bi-person me-2"></i>
                    <strong>Username:</strong> {{ selectedAdmin.username }}
                  </p>
                  <p class="mb-2">
                    <i class="bi bi-envelope me-2"></i>
                    <strong>Email:</strong> {{ selectedAdmin.email }}
                  </p>
                  <p class="mb-2">
                    <i class="bi bi-telephone me-2"></i>
                    <strong>Phone:</strong> {{ selectedAdmin.contact_no || 'Not provided' }}
                  </p>
                  <p class="mb-0">
                    <i class="bi bi-geo-alt me-2"></i>
                    <strong>Address:</strong> {{ selectedAdmin.address || 'Not provided' }}
                  </p>
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

    <div class="modal fade" id="adminFormModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Edit Admin' : 'Add New Admin' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="handleAdminSubmit">
              <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input v-model="adminForm.name" type="text" class="form-control" required>
              </div>

              <div v-if="!isEditing" class="mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input v-model="adminForm.username" type="text" class="form-control" required>
              </div>

              <div v-if="!isEditing" class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input v-model="adminForm.email" type="email" class="form-control" required>
              </div>

              <div v-if="!isEditing" class="mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input v-model="adminForm.password" type="password" class="form-control" required>
                <small class="text-muted">Minimum 6 characters</small>
              </div>

              <div class="mb-3">
                <label class="form-label">Designation</label>
                <input v-model="adminForm.designation" type="text" class="form-control">
              </div>

              <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input v-model="adminForm.contact_no" type="tel" class="form-control">
              </div>

              <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea v-model="adminForm.address" class="form-control" rows="2"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <input type="file" class="form-control" accept="image/*" @change="handleImageUpload">
                <small class="text-muted">Only jpeg, png, jpg (max 2MB)</small>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
                  {{ isEditing ? 'Update Admin' : 'Add Admin' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>


    <div class="modal fade" id="permissionsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Manage Permissions for {{ selectedAdmin?.name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedAdmin" class="row">
              <div class="col-md-6">
                <h6>Available Permissions</h6>
                <div class="list-group">
                  <div v-for="permission in allPermissions" :key="permission.id"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    {{ permission.name }}
                    <button class="btn btn-sm btn-success" @click="assignPermission(selectedAdmin.id, permission.id)"
                      v-if="!hasPermission(selectedAdmin, permission.name)">
                      Allow
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <h6>Current Permissions</h6>
                <div class="list-group">
                  <div v-for="permission in selectedAdmin.permissions" :key="permission"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    {{ permission }}
                    <button class="btn btn-sm btn-danger"
                      @click="removePermission(selectedAdmin.id, getPermissionId(permission))">
                      Revoke
                    </button>
                  </div>
                  <div v-if="selectedAdmin.permissions.length === 0" class="list-group-item text-muted">
                    No permissions assigned
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

   
    <div class="modal fade" id="globalPermissionsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Manage All Permissions</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex justify-content-between mb-3">
              <button class="btn btn-primary" @click="openAddPermissionModal">
                <i class="bi bi-plus-circle me-2"></i>Add New Permission
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Guard</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="permission in adminStore.permissions" :key="permission.id">
                    <td>{{ permission.id }}</td>
                    <td>{{ permission.name }}</td>
                    <td>{{ permission.guard_name }}</td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary me-2" @click="openEditPermissionModal(permission)">
                        Edit
                      </button>
                      <button class="btn btn-sm btn-outline-danger" @click="confirmDeletePermission(permission)">
                        Delete
                      </button>
                    </td>
                  </tr>
                  <tr v-if="adminStore.permissions.length === 0">
                    <td colspan="4" class="text-center py-4">No permissions found</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  
    <div class="modal fade" id="permissionFormModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditingPermission ? 'Edit Permission' : 'Add New Permission' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="handlePermissionSubmit">
              <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input v-model="permissionForm.name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Guard Name</label>
                <input v-model="permissionForm.guard_name" type="text" class="form-control" placeholder="web">
              </div>
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                  {{ isEditingPermission ? 'Update' : 'Create' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAllAdminStore } from '../stores/AllAdminStore';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';


const adminStore = useAllAdminStore();
let permissionsModal = null;
let globalPermissionsModal = null;
let permissionFormModal = null;

let viewAdminModal = null;
let adminFormModal = null;

const searchQuery = ref('');
const selectedDesignation = ref('all');
const sortBy = ref('name');
const sortOrder = ref('asc');

const isEditing = ref(false);
const isSubmitting = ref(false);
const adminForm = ref({
  id: null,
  name: '',
  username: '',
  email: '',
  password: '',
  designation: '',
  contact_no: '',
  address: '',
  profile_pic: null
});

const selectedAdmin = ref(null);
const isEditingPermission = ref(false);
const permissionForm = ref({
  id: null,
  name: '',
  guard_name: 'web'
});
const uniqueDesignations = computed(() => {
  const designations = new Set();
  adminStore.admins.forEach(admin => {
    if (admin.designation) {
      designations.add(admin.designation);
    }
  });
  return Array.from(designations).sort();
});
onMounted(async () => {
  await adminStore.fetchAllAdmins();
  await adminStore.fetchAllPermissions();


  viewAdminModal = new Modal(document.getElementById('viewAdminModal'));
  adminFormModal = new Modal(document.getElementById('adminFormModal'));

  permissionsModal = new Modal(document.getElementById('permissionsModal'));
  globalPermissionsModal = new Modal(document.getElementById('globalPermissionsModal'));
  permissionFormModal = new Modal(document.getElementById('permissionFormModal'));
});
const allPermissions = computed(() => {
  return adminStore.permissions;
});

function hasPermission(admin, permissionName) {
  return admin.permissions.includes(permissionName);
}

function getPermissionId(permissionName) {
  const permission = adminStore.permissions.find(p => p.name === permissionName);
  return permission ? permission.id : null;
}
function openPermissionsModal(admin) {
  selectedAdmin.value = { ...admin };
  permissionsModal.show();
}
function openGlobalPermissionsModal() {
  globalPermissionsModal.show();
}

function openAddPermissionModal() {
  isEditingPermission.value = false;
  permissionForm.value = {
    id: null,
    name: '',
    guard_name: 'web'
  };
  permissionFormModal.show();
}

function openEditPermissionModal(permission) {
  isEditingPermission.value = true;
  permissionForm.value = {
    id: permission.id,
    name: permission.name,
    guard_name: permission.guard_name
  };
  permissionFormModal.show();
}

async function handlePermissionSubmit() {
  try {
    isSubmitting.value = true;

    if (isEditingPermission.value) {
      await adminStore.updatePermission(permissionForm.value.id, permissionForm.value);
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Permission updated successfully',
        timer: 1500,
        showConfirmButton: false
      });
      await adminStore.fetchAllAdmins();
    } else {
      await adminStore.createPermission(permissionForm.value);
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Permission created successfully',
        timer: 1500,
        showConfirmButton: false
      });
    }

    permissionFormModal.hide();
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.response?.data?.message || error.message || 'Failed to save permission',
    });
  } finally {
    isSubmitting.value = false;
  }
}

async function assignPermission(adminId, permissionId) {
  try {
    await adminStore.assignPermission(adminId, permissionId);
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Permission assigned successfully',
      timer: 1500,
      showConfirmButton: false
    });

    // Refresh admin data
    await adminStore.fetchAllAdmins();
    await adminStore.fetchAllPermissions();

  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.response?.data?.message || error.message || 'Failed to assign permission',
    });
  }
}

async function removePermission(adminId, permissionId) {
  try {
    await adminStore.removePermission(adminId, permissionId);
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Permission removed successfully',
      timer: 1500,
      showConfirmButton: false
    });

    // Refresh admin data
    await adminStore.fetchAllAdmins();

    await adminStore.fetchAllPermissions();
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.response?.data?.message || error.message || 'Failed to remove permission',
    });
  }
}

function confirmDeletePermission(permission) {
  Swal.fire({
    title: 'Are you sure?',
    text: `You are about to delete permission "${permission.name}". This action cannot be undone!`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await adminStore.deletePermission(permission.id);
        Swal.fire({
          icon: 'success',
          title: 'Deleted!',
          text: 'Permission has been deleted.',
          timer: 1500,
          showConfirmButton: false
        });
        await adminStore.fetchAllAdmins();
        await adminStore.fetchAllPermissions();
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || error.message || 'Failed to delete permission',
        });
      }
    }
  });
}
/////////////////////NEW///////////////////
const filteredAdmins = computed(() => {
  let admins = [...adminStore.admins];


  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    admins = admins.filter(admin =>
      admin.name.toLowerCase().includes(query) ||
      admin.username.toLowerCase().includes(query) ||
      admin.email.toLowerCase().includes(query)
    );
  }


  if (selectedDesignation.value !== 'all') {
    admins = admins.filter(admin =>
      admin.designation === selectedDesignation.value
    );
  }

  admins.sort((a, b) => {
    let compareA, compareB;

    if (sortBy.value === 'name') {
      compareA = a.name.toLowerCase();
      compareB = b.name.toLowerCase();
    } else if (sortBy.value === 'username') {
      compareA = a.username.toLowerCase();
      compareB = b.username.toLowerCase();
    } else {
      compareA = a.designation?.toLowerCase() || '';
      compareB = b.designation?.toLowerCase() || '';
    }

    if (sortOrder.value === 'asc') {
      return compareA > compareB ? 1 : -1;
    } else {
      return compareA < compareB ? 1 : -1;
    }
  });

  return admins;
});
const handleSearch = () => {

};

const handleDesignationChange = () => {

};

const handleSortChange = () => {
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedDesignation.value = 'all';
  sortBy.value = 'name';
  sortOrder.value = 'asc';
};

const openViewModal = (admin) => {
  selectedAdmin.value = { ...admin };
  viewAdminModal.show();
};

const openAddAdminModal = () => {
  isEditing.value = false;
  resetAdminForm();
  adminFormModal.show();
};

const openEditModal = (admin) => {
  isEditing.value = true;
  adminForm.value = {
    id: admin.id,
    name: admin.name,
    username: admin.username,
    email: admin.email,
    password: '',
    designation: admin.designation || '',
    contact_no: admin.contact_no || '',
    address: admin.address || '',
    profile_pic: null
  };
  adminFormModal.show();
};

const resetAdminForm = () => {
  adminForm.value = {
    id: null,
    name: '',
    username: '',
    email: '',
    password: '',
    designation: '',
    contact_no: '',
    address: '',
    profile_pic: null
  };
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    adminForm.value.profile_pic = file;
  }
};

const handleAdminSubmit = async () => {
  isSubmitting.value = true;

  try {
    const formData = new FormData();

    for (const key in adminForm.value) {
      if (adminForm.value[key] !== null && key !== 'id') {
        formData.append(key, adminForm.value[key]);
      }
    }

    if (isEditing.value) {
      await adminStore.updateAdmin(adminForm.value.id, formData);
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Admin updated successfully',
        timer: 1500,
        showConfirmButton: false
      });
      await adminStore.fetchAllAdmins();
      await adminStore.fetchAllPermissions();
    } else {
      await adminStore.addAdmin(formData);
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'New admin added successfully',
        timer: 1500,
        showConfirmButton: false
      });
    }

    adminFormModal.hide();
    resetAdminForm();
  } catch (error) {
    let errorMessage = 'Failed to save admin';
    if (error.response?.data?.errors) {
      errorMessage = Object.values(error.response.data.errors).join('\n');
    } else if (error.response?.data?.message) {
      errorMessage = error.response.data.message;
    }

    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: errorMessage,
    });
  } finally {
    isSubmitting.value = false;
  }
};

const confirmDeleteAdmin = (admin) => {
  Swal.fire({
    title: 'Are you sure?',
    text: `You are about to delete ${admin.name}. This action cannot be undone!`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await adminStore.deleteAdmin(admin.id);
        Swal.fire({
          icon: 'success',
          title: 'Deleted!',
          text: 'Admin has been deleted.',
          timer: 1500,
          showConfirmButton: false
        });
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || error.message || 'Failed to delete admin',
        });
      }
    }
  });
};
</script>

<style scoped>
.truncate-address {
  display: inline-block;
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.admin-profiles-page {
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

.badge {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.35em 0.65em;
}

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

  .table td:nth-child(1)::before {
    content: "Profile";
  }

  .table td:nth-child(2)::before {
    content: "Admin Details";
  }

  .table td:nth-child(3)::before {
    content: "Contact";
  }

  .table td:nth-child(4)::before {
    content: "Designation";
  }

  .table td:nth-child(5)::before {
    content: "Actions";
  }

  .table td .d-flex {
    justify-content: flex-end;
  }
}

@media (max-width: 576px) {
  .modal-dialog {
    margin: 0.5rem auto;
  }
}

.permission-badge {
  cursor: pointer;
}

.permission-allowed {
  background-color: #28a745;
}

.permission-forbidden {
  background-color: #dc3545;
}

.list-group-item {
  transition: all 0.2s;
}

.list-group-item:hover {
  background-color: #f8f9fa;
}
</style> -->


<template>
  <div class="admin-profiles-page">
    <!-- Header Section -->
    <section class="admin-header">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center py-4">
          <h1 class="page-title">Admin Profiles</h1>
          <div class="d-flex gap-3">
            <button class="btn btn-info" @click="openGlobalPermissionsModal">
              <i class="bi bi-shield-lock me-2"></i>Manage Core Permissions
            </button>
            <button class="btn btn-primary" @click="openAddAdminModal">
              <i class="bi bi-plus-circle me-2"></i>Add Admin
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Filters Section -->
    <section class="filters-section py-3 bg-light">
      <div class="container-fluid">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Search Admins</label>
            <input v-model="searchQuery" type="text" class="form-control"
              placeholder="Search by name, email or username..." @input="handleSearch">
          </div>
          <div class="col-md-3">
            <label class="form-label">Designation</label>
            <select v-model="selectedDesignation" class="form-select" @change="handleDesignationChange">
              <option value="all">All Designations</option>
              <option v-for="designation in uniqueDesignations" :key="designation" :value="designation">
                {{ designation }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Sort By</label>
            <select v-model="sortBy" class="form-select" @change="handleSortChange">
              <option value="name">Name</option>
              <option value="username">Username</option>
              <option value="designation">Designation</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-outline-secondary w-100" @click="resetFilters">
              <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Admins Table Section -->
    <section class="admins-table-section py-4">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Profile</th>
                    <th>Admin Details</th>
                    <th>Contact</th>
                    <th>Designation</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="adminStore.isLoading">
                    <td colspan="5" class="text-center py-5">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-else-if="filteredAdmins.length === 0">
                    <td colspan="5" class="text-center py-5">
                      <h5>No admins found</h5>
                      <p class="text-muted">Try adjusting your search or filter criteria</p>
                    </td>
                  </tr>
                  <tr v-for="admin in filteredAdmins" :key="admin.id">
                    <td>
                      <img :src="admin.profile_pic || '/images/default-admin.png'" class="rounded-circle" width="50"
                        height="50" alt="Admin">
                    </td>
                    <td>
                      <strong>{{ admin.name }}</strong>
                      <p class="text-muted small mb-0">{{ admin.username }}</p>
                      <p class="text-muted small mb-0">{{ admin.email }}</p>
                    </td>
                    <td>
                      <p class="mb-0">{{ admin.contact_no || 'Not provided' }}</p>
                      <p class="text-muted small mb-0 truncate-address">
                        {{ admin.address || 'Not provided' }}
                      </p>
                    </td>
                    <td>
                      <span class="badge bg-primary">
                        {{ admin.designation || 'Not specified' }}
                      </span>
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" @click="openViewModal(admin)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" @click="openEditModal(admin)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info" @click="openPermissionsModal(admin)">
                          <i class="bi bi-shield-lock"></i> Permissions
                        </button>
                        <button class="btn btn-sm btn-outline-danger" @click="confirmDeleteAdmin(admin)">
                          <i class="bi bi-trash"></i>
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

    <!-- View Admin Modal -->
    <div class="modal fade" id="viewAdminModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Admin Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedAdmin" class="text-center">
              <img :src="selectedAdmin.profile_pic || '/images/default-admin.png'" class="rounded-circle mb-3"
                width="150" height="150" alt="Admin">
              <h3>{{ selectedAdmin.name }}</h3>
              <p class="text-muted">{{ selectedAdmin.designation || 'No designation' }}</p>

              <div class="card mt-4">
                <div class="card-body text-start">
                  <h5 class="card-title">Contact Information</h5>
                  <p class="mb-2">
                    <i class="bi bi-person me-2"></i>
                    <strong>Username:</strong> {{ selectedAdmin.username }}
                  </p>
                  <p class="mb-2">
                    <i class="bi bi-envelope me-2"></i>
                    <strong>Email:</strong> {{ selectedAdmin.email }}
                  </p>
                  <p class="mb-2">
                    <i class="bi bi-telephone me-2"></i>
                    <strong>Phone:</strong> {{ selectedAdmin.contact_no || 'Not provided' }}
                  </p>
                  <p class="mb-0">
                    <i class="bi bi-geo-alt me-2"></i>
                    <strong>Address:</strong> {{ selectedAdmin.address || 'Not provided' }}
                  </p>
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

    <!-- Admin Form Modal -->
    <div class="modal fade" id="adminFormModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Edit Admin' : 'Add New Admin' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="handleAdminSubmit">
              <!-- Name Field -->
              <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input v-model="adminForm.name" type="text" class="form-control"
                  :class="{ 'is-invalid': formErrors.name }" @input="validateField('name')">
                <div class="invalid-feedback">{{ formErrors.name }}</div>
              </div>

              <!-- Username Field -->
              <div v-if="!isEditing" class="mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input v-model="adminForm.username" type="text" class="form-control"
                  :class="{ 'is-invalid': formErrors.username }" @input="validateField('username')"
                  @keypress="restrictUsernameInput">
                <div class="invalid-feedback">{{ formErrors.username }}</div>
                <small class="form-text text-muted">3-20 characters, letters, numbers, underscores only</small>
              </div>

              <!-- Email Field -->
              <div v-if="!isEditing" class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input v-model="adminForm.email" type="email" class="form-control"
                  :class="{ 'is-invalid': formErrors.email }" @input="validateField('email')">
                <div class="invalid-feedback">{{ formErrors.email }}</div>
              </div>

              <!-- Password Field -->
              <div v-if="!isEditing" class="mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input v-model="adminForm.password" type="password" class="form-control"
                  :class="{ 'is-invalid': formErrors.password }" @input="validateField('password')">
                <div class="invalid-feedback">{{ formErrors.password }}</div>
                <small class="form-text text-muted">Minimum 6 characters</small>
              </div>

              <!-- Designation Field -->
              <!-- <div class="mb-3">
                <label class="form-label">Designation</label>
                <input v-model="adminForm.designation" type="text" class="form-control"
                  :class="{ 'is-invalid': formErrors.designation }" @input="validateField('designation')">
                <div class="invalid-feedback">{{ formErrors.designation }}</div>
              </div>

             
              <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input v-model="adminForm.contact_no" type="tel" class="form-control"
                  :class="{ 'is-invalid': formErrors.contact_no }" @input="validateField('contact_no')"
                  @keypress="restrictToNumbers">
                <div class="invalid-feedback">{{ formErrors.contact_no }}</div>
              </div> -->
              <!-- Designation Field -->
              <div class="mb-3">
                <label class="form-label">Designation</label>
                <input v-model="adminForm.designation" type="text" class="form-control" maxlength="50"
                  :class="{ 'is-invalid': formErrors.designation }" @input="validateField('designation')">
                <div class="invalid-feedback">{{ formErrors.designation }}</div>
                <small class="text-muted">{{ adminForm.designation.length }}/50</small>
              </div>

              <!-- Contact Number Field -->
              <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input v-model="adminForm.contact_no" type="tel" class="form-control" maxlength="15"
                  :class="{ 'is-invalid': formErrors.contact_no }" @input="validateField('contact_no')"
                  @keypress="restrictToNumbers">
                <div class="invalid-feedback">{{ formErrors.contact_no }}</div>
                <small class="text-muted">{{ adminForm.contact_no.length }}/15</small>
              </div>

              <!-- Address Field -->
              <!-- <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea v-model="adminForm.address" class="form-control" rows="2"
                  :class="{ 'is-invalid': formErrors.address }"
                  @input="validateField('address')"></textarea>
                <div class="invalid-feedback">{{ formErrors.address }}</div>
              </div> -->
              <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea v-model="adminForm.address" class="form-control" rows="2" maxlength="150"
                  style="resize: none;" :class="{ 'is-invalid': formErrors.address }"
                  @input="validateField('address')"></textarea>
                <div class="invalid-feedback">{{ formErrors.address }}</div>
                <small class="text-muted">{{ adminForm.address.length }}/150</small>
              </div>

              <!-- Profile Picture Field -->
              <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <input type="file" class="form-control" accept="image/jpeg,image/png,image/jpg"
                  :class="{ 'is-invalid': formErrors.profile_pic }" @change="handleImageUpload">
                <div class="invalid-feedback">{{ formErrors.profile_pic }}</div>
                <small class="form-text text-muted">Only jpeg, png, jpg (max 2MB)</small>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting || hasFormErrors">
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
                  {{ isEditing ? 'Update Admin' : 'Add Admin' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Permissions Modal -->
    <div class="modal fade" id="permissionsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Manage Permissions for {{ selectedAdmin?.name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedAdmin" class="row">
              <div class="col-md-6">
                <h6>Available Permissions</h6>
                <div class="list-group">
                  <div v-for="permission in allPermissions" :key="permission.id"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    {{ permission.name }}
                    <button class="btn btn-sm btn-success" @click="assignPermission(selectedAdmin.id, permission.id)"
                      v-if="!hasPermission(selectedAdmin, permission.name)">
                      Allow
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <h6>Current Permissions</h6>
                <div class="list-group">
                  <div v-for="permission in selectedAdmin.permissions" :key="permission"
                    class="list-group-item d-flex justify-content-between align-items-center">
                    {{ permission }}
                    <button class="btn btn-sm btn-danger"
                      @click="removePermission(selectedAdmin.id, getPermissionId(permission))">
                      Revoke
                    </button>
                  </div>
                  <div v-if="selectedAdmin.permissions.length === 0" class="list-group-item text-muted">
                    No permissions assigned
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

    <!-- Global Permissions Management Modal -->
    <div class="modal fade" id="globalPermissionsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Manage All Permissions</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex justify-content-between mb-3">
              <button class="btn btn-primary" @click="openAddPermissionModal">
                <i class="bi bi-plus-circle me-2"></i>Add New Permission
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Guard</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="permission in adminStore.permissions" :key="permission.id">
                    <td>{{ permission.id }}</td>
                    <td>{{ permission.name }}</td>
                    <td>{{ permission.guard_name }}</td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary me-2" @click="openEditPermissionModal(permission)">
                        Edit
                      </button>
                      <button class="btn btn-sm btn-outline-danger" @click="confirmDeletePermission(permission)">
                        Delete
                      </button>
                    </td>
                  </tr>
                  <tr v-if="adminStore.permissions.length === 0">
                    <td colspan="4" class="text-center py-4">No permissions found</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Permission Modal -->
    <div class="modal fade" id="permissionFormModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditingPermission ? 'Edit Permission' : 'Add New Permission' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="handlePermissionSubmit">
              <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input v-model="permissionForm.name" type="text" class="form-control"
                  :class="{ 'is-invalid': formErrors.permissionName }" required>
                <div class="invalid-feedback">{{ formErrors.permissionName }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Guard Name</label>
                <input v-model="permissionForm.guard_name" type="text" class="form-control" placeholder="web">
              </div>
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                  {{ isSubmitting ? 'Processing...' : isEditingPermission ? 'Update' : 'Create' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useAllAdminStore } from '../stores/AllAdminStore';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';

const adminStore = useAllAdminStore();
let permissionsModal = null;
let globalPermissionsModal = null;
let permissionFormModal = null;
let viewAdminModal = null;
let adminFormModal = null;

// Form and error handling
const formErrors = ref({
  name: '',
  username: '',
  email: '',
  password: '',
  designation: '',
  contact_no: '',
  address: '',
  profile_pic: '',
  permissionName: ''
});

// Search and filter
const searchQuery = ref('');
const selectedDesignation = ref('all');
const sortBy = ref('name');
const sortOrder = ref('asc');

// Form states
const isEditing = ref(false);
const isSubmitting = ref(false);
const adminForm = ref({
  id: null,
  name: '',
  username: '',
  email: '',
  password: '',
  designation: '',
  contact_no: '',
  address: '',
  profile_pic: null
});

const selectedAdmin = ref(null);
const isEditingPermission = ref(false);
const permissionForm = ref({
  id: null,
  name: '',
  guard_name: 'web'
});

// Computed properties
const uniqueDesignations = computed(() => {
  const designations = new Set();
  adminStore.admins.forEach(admin => {
    if (admin.designation) {
      designations.add(admin.designation);
    }
  });
  return Array.from(designations).sort();
});

const allPermissions = computed(() => {
  return adminStore.permissions;
});

const hasFormErrors = computed(() => {
  return Object.values(formErrors.value).some(error => error !== '') ||
    (!isEditing.value && (!adminForm.value.name || !adminForm.value.username ||
      !adminForm.value.email || !adminForm.value.password));
});

const filteredAdmins = computed(() => {
  let admins = [...adminStore.admins];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    admins = admins.filter(admin =>
      admin.name.toLowerCase().includes(query) ||
      admin.username.toLowerCase().includes(query) ||
      admin.email.toLowerCase().includes(query)
    );
  }

  if (selectedDesignation.value !== 'all') {
    admins = admins.filter(admin =>
      admin.designation === selectedDesignation.value
    );
  }

  admins.sort((a, b) => {
    let compareA, compareB;

    if (sortBy.value === 'name') {
      compareA = a.name.toLowerCase();
      compareB = b.name.toLowerCase();
    } else if (sortBy.value === 'username') {
      compareA = a.username.toLowerCase();
      compareB = b.username.toLowerCase();
    } else {
      compareA = a.designation?.toLowerCase() || '';
      compareB = b.designation?.toLowerCase() || '';
    }

    if (sortOrder.value === 'asc') {
      return compareA > compareB ? 1 : -1;
    } else {
      return compareA < compareB ? 1 : -1;
    }
  });

  return admins;
});

// Lifecycle hooks
onMounted(async () => {
  await adminStore.fetchAllAdmins();
  await adminStore.fetchAllPermissions();

  viewAdminModal = new Modal(document.getElementById('viewAdminModal'));
  adminFormModal = new Modal(document.getElementById('adminFormModal'));
  permissionsModal = new Modal(document.getElementById('permissionsModal'));
  globalPermissionsModal = new Modal(document.getElementById('globalPermissionsModal'));
  permissionFormModal = new Modal(document.getElementById('permissionFormModal'));
});

// Input restriction methods
function restrictToNumbers(event) {
  const charCode = event.which ? event.which : event.keyCode;
  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    event.preventDefault();
  }
}

function restrictUsernameInput(event) {
  const charCode = event.which ? event.which : event.keyCode;
  // Allow only letters, numbers, and underscore
  if (!(charCode >= 48 && charCode <= 57) && // numeric (0-9)
    !(charCode >= 65 && charCode <= 90) && // upper alpha (A-Z)
    !(charCode >= 97 && charCode <= 122) && // lower alpha (a-z)
    charCode !== 95) { // underscore (_)
    event.preventDefault();
  }
}

// Validation methods
function validateField(field) {
  const value = adminForm.value[field];

  switch (field) {
    case 'name':
      if (!value) {
        formErrors.value.name = 'Full name is required';
      } else if (value.length > 255) {
        formErrors.value.name = 'Name must be less than 255 characters';
      } else {
        formErrors.value.name = '';
      }
      break;

    case 'username':
      if (!value) {
        formErrors.value.username = 'Username is required';
      } else if (value.length < 3 || value.length > 20) {
        formErrors.value.username = 'Username must be between 3-20 characters';
      } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
        formErrors.value.username = 'Only letters, numbers and underscore allowed';
      } else {
        formErrors.value.username = '';
      }
      break;

    case 'email':
      if (!value) {
        formErrors.value.email = 'Email is required';
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        formErrors.value.email = 'Please enter a valid email address';
      } else if (value.length > 255) {
        formErrors.value.email = 'Email must be less than 255 characters';
      } else {
        formErrors.value.email = '';
      }
      break;

    case 'password':
      if (!isEditing.value && !value) {
        formErrors.value.password = 'Password is required';
      } else if (value && value.length < 6) {
        formErrors.value.password = 'Password must be at least 6 characters';
      } else {
        formErrors.value.password = '';
      }
      break;

    case 'contact_no':
      if (value && value.length > 20) {
        formErrors.value.contact_no = 'Contact number must be less than 20 characters';
      } else if (value && !/^[0-9]+$/.test(value)) {
        formErrors.value.contact_no = 'Only numbers allowed';
      } else {
        formErrors.value.contact_no = '';
      }
      break;

    case 'designation':
      if (value && value.length > 255) {
        formErrors.value.designation = 'Designation must be less than 255 characters';
      } else {
        formErrors.value.designation = '';
      }
      break;

    case 'address':
      if (value && value.length > 500) {
        formErrors.value.address = 'Address must be less than 500 characters';
      } else {
        formErrors.value.address = '';
      }
      break;
  }
}

// Permission methods
function hasPermission(admin, permissionName) {
  return admin.permissions.includes(permissionName);
}

function getPermissionId(permissionName) {
  const permission = adminStore.permissions.find(p => p.name === permissionName);
  return permission ? permission.id : null;
}

function openPermissionsModal(admin) {
  selectedAdmin.value = { ...admin };
  permissionsModal.show();
}

function openGlobalPermissionsModal() {
  globalPermissionsModal.show();
}

function openAddPermissionModal() {
  isEditingPermission.value = false;
  permissionForm.value = {
    id: null,
    name: '',
    guard_name: 'web'
  };
  formErrors.value.permissionName = '';
  permissionFormModal.show();
}

function openEditPermissionModal(permission) {
  isEditingPermission.value = true;
  permissionForm.value = {
    id: permission.id,
    name: permission.name,
    guard_name: permission.guard_name
  };
  formErrors.value.permissionName = '';
  permissionFormModal.show();
}

async function handlePermissionSubmit() {
  try {
    isSubmitting.value = true;
    formErrors.value.permissionName = '';

    if (!permissionForm.value.name) {
      formErrors.value.permissionName = 'Permission name is required';
      return;
    }

    if (isEditingPermission.value) {
      await adminStore.updatePermission(permissionForm.value.id, permissionForm.value);
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Permission updated successfully',
        timer: 1500,
        showConfirmButton: false
      });
      await adminStore.fetchAllAdmins();
    } else {
      await adminStore.createPermission(permissionForm.value);
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Permission created successfully',
        timer: 1500,
        showConfirmButton: false
      });
    }

    permissionFormModal.hide();
  } catch (error) {
    let errorMessage = 'Failed to save permission';

    if (error.response?.data?.errors?.name) {
      formErrors.value.permissionName = error.response.data.errors.name[0];
    } else if (error.response?.data?.message) {
      errorMessage = error.response.data.message;
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: errorMessage,
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: error.message || 'Failed to save permission',
      });
    }
  } finally {
    isSubmitting.value = false;
  }
}
async function assignPermission(adminId, permissionId) {
  try {
    // Optimistically update the UI
    const permission = adminStore.permissions.find(p => p.id === permissionId);
    if (permission && !selectedAdmin.value.permissions.includes(permission.name)) {
      selectedAdmin.value.permissions.push(permission.name);
    }

    await adminStore.assignPermission(adminId, permissionId);

    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Permission assigned successfully',
      timer: 1500,
      showConfirmButton: false
    });

    // Still refresh data in background to ensure consistency
    await adminStore.fetchAllAdmins();
    await adminStore.fetchAllPermissions();
  } catch (error) {
    // Revert optimistic update if failed
    if (permission) {
      selectedAdmin.value.permissions = selectedAdmin.value.permissions.filter(p => p !== permission.name);
    }

    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.response?.data?.message || error.message || 'Failed to assign permission',
    });
  }
}
// async function assignPermission(adminId, permissionId) {
//   try {
//     await adminStore.assignPermission(adminId, permissionId);
//     Swal.fire({
//       icon: 'success',
//       title: 'Success!',
//       text: 'Permission assigned successfully',
//       timer: 1500,
//       showConfirmButton: false
//     });

//     await adminStore.fetchAllAdmins();
//     await adminStore.fetchAllPermissions();
//   } catch (error) {
//     Swal.fire({
//       icon: 'error',
//       title: 'Error',
//       text: error.response?.data?.message || error.message || 'Failed to assign permission',
//     });
//   }
// }

// async function removePermission(adminId, permissionId) {
//   try {
//     await adminStore.removePermission(adminId, permissionId);
//     Swal.fire({
//       icon: 'success',
//       title: 'Success!',
//       text: 'Permission removed successfully',
//       timer: 1500,
//       showConfirmButton: false
//     });

//     await adminStore.fetchAllAdmins();
//     await adminStore.fetchAllPermissions();
//   } catch (error) {
//     Swal.fire({
//       icon: 'error',
//       title: 'Error',
//       text: error.response?.data?.message || error.message || 'Failed to remove permission',
//     });
//   }
// }
async function removePermission(adminId, permissionId) {
  try {
    // Optimistically update the UI
    const permission = adminStore.permissions.find(p => p.id === permissionId);
    if (permission) {
      selectedAdmin.value.permissions = selectedAdmin.value.permissions.filter(p => p !== permission.name);
    }

    await adminStore.removePermission(adminId, permissionId);

    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Permission removed successfully',
      timer: 1500,
      showConfirmButton: false
    });
    await adminStore.fetchAllAdmins();
    await adminStore.fetchAllPermissions();
  } catch (error) {
    if (permission) {
      selectedAdmin.value.permissions.push(permission.name);
    }

    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.response?.data?.message || error.message || 'Failed to remove permission',
    });
  }
}
function confirmDeletePermission(permission) {
  Swal.fire({
    title: 'Are you sure?',
    text: `You are about to delete permission "${permission.name}". This action cannot be undone!`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await adminStore.deletePermission(permission.id);
        Swal.fire({
          icon: 'success',
          title: 'Deleted!',
          text: 'Permission has been deleted.',
          timer: 1500,
          showConfirmButton: false
        });
        await adminStore.fetchAllAdmins();
        await adminStore.fetchAllPermissions();
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || error.message || 'Failed to delete permission',
        });
      }
    }
  });
}

// Admin form methods
const handleSearch = () => {
  // Search functionality remains the same
};

const handleDesignationChange = () => {
  // Designation filter remains the same
};

const handleSortChange = () => {
  // Sort functionality remains the same
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedDesignation.value = 'all';
  sortBy.value = 'name';
  sortOrder.value = 'asc';
};

const openViewModal = (admin) => {
  selectedAdmin.value = { ...admin };
  viewAdminModal.show();
};

const openAddAdminModal = () => {
  isEditing.value = false;
  resetAdminForm();
  adminFormModal.show();
};

const openEditModal = (admin) => {
  isEditing.value = true;
  adminForm.value = {
    id: admin.id,
    name: admin.name,
    username: admin.username,
    email: admin.email,
    password: '',
    designation: admin.designation || '',
    contact_no: admin.contact_no || '',
    address: admin.address || '',
    profile_pic: null
  };
  adminFormModal.show();
};

const resetAdminForm = () => {
  adminForm.value = {
    id: null,
    name: '',
    username: '',
    email: '',
    password: '',
    designation: '',
    contact_no: '',
    address: '',
    profile_pic: null
  };
  // Reset errors
  Object.keys(formErrors.value).forEach(key => {
    formErrors.value[key] = '';
  });
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate image
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    const maxSize = 2 * 1024 * 1024; // 2MB

    if (!validTypes.includes(file.type)) {
      formErrors.value.profile_pic = 'Only JPEG, PNG, or JPG images are allowed';
      return;
    }

    if (file.size > maxSize) {
      formErrors.value.profile_pic = 'Image size must be less than 2MB';
      return;
    }

    formErrors.value.profile_pic = '';
    adminForm.value.profile_pic = file;
  }
};

const handleAdminSubmit = async () => {
  isSubmitting.value = true;

  // Validate all fields before submission
  Object.keys(adminForm.value).forEach(field => {
    if (field !== 'id' && field !== 'profile_pic') {
      validateField(field);
    }
  });

  if (Object.values(formErrors.value).some(error => error !== '')) {
    isSubmitting.value = false;
    return;
  }

  try {
    const formData = new FormData();

    for (const key in adminForm.value) {
      if (adminForm.value[key] !== null && key !== 'id') {
        formData.append(key, adminForm.value[key]);
      }
    }

    if (isEditing.value) {
      await adminStore.updateAdmin(adminForm.value.id, formData);
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Admin updated successfully',
        timer: 1500,
        showConfirmButton: false
      });
    } else {
      await adminStore.addAdmin(formData);
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'New admin added successfully',
        timer: 1500,
        showConfirmButton: false
      });
    }

    adminFormModal.hide();
    resetAdminForm();
    await adminStore.fetchAllAdmins();
    await adminStore.fetchAllPermissions();
  } catch (error) {
    let errorMessage = 'Failed to save admin';

    // Handle validation errors from server
    if (error.response?.data?.errors) {
      const serverErrors = error.response.data.errors;

      // Map server errors to our error object
      Object.keys(serverErrors).forEach(key => {
        if (formErrors.value.hasOwnProperty(key)) {
          formErrors.value[key] = serverErrors[key][0];
        }
      });

      // Show general error for fields not in our form
      const unhandledErrors = Object.keys(serverErrors).filter(
        key => !formErrors.value.hasOwnProperty(key)
      );

      if (unhandledErrors.length > 0) {
        errorMessage = serverErrors[unhandledErrors[0]][0];
      }
    } else if (error.response?.data?.message) {
      errorMessage = error.response.data.message;
    }

    if (errorMessage !== 'Failed to save admin') {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: errorMessage,
      });
    }
  } finally {
    isSubmitting.value = false;
  }
};

const confirmDeleteAdmin = (admin) => {
  Swal.fire({
    title: 'Are you sure?',
    text: `You are about to delete ${admin.name}. This action cannot be undone!`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await adminStore.deleteAdmin(admin.id);
        Swal.fire({
          icon: 'success',
          title: 'Deleted!',
          text: 'Admin has been deleted.',
          timer: 1500,
          showConfirmButton: false
        });
        await adminStore.fetchAllAdmins();
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || error.message || 'Failed to delete admin',
        });
      }
    }
  });
};
</script>

<style scoped>
.truncate-address {
  display: inline-block;
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.admin-profiles-page {
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

.badge {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.35em 0.65em;
}

.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.875em;
  color: #dc3545;
}

.is-invalid {
  border-color: #dc3545;
}

.is-invalid:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

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

  .table td:nth-child(1)::before {
    content: "Profile";
  }

  .table td:nth-child(2)::before {
    content: "Admin Details";
  }

  .table td:nth-child(3)::before {
    content: "Contact";
  }

  .table td:nth-child(4)::before {
    content: "Designation";
  }

  .table td:nth-child(5)::before {
    content: "Actions";
  }

  .table td .d-flex {
    justify-content: flex-end;
  }
}

@media (max-width: 576px) {
  .modal-dialog {
    margin: 0.5rem auto;
  }
}

.permission-badge {
  cursor: pointer;
}

.permission-allowed {
  background-color: #28a745;
}

.permission-forbidden {
  background-color: #dc3545;
}

.list-group-item {
  transition: all 0.2s;
}

.list-group-item:hover {
  background-color: #f8f9fa;
}
</style>