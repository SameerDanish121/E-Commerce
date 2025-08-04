<!-- <template>
  <div class="profile-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="profile-card card border-0 shadow-sm overflow-hidden">
            <div class="cover-photo position-relative">
              <img :src="assetPath('images/back_1.jpg')" alt="Cover" class="w-100"
                style="height: 200px; object-fit: cover;">
              <div class="gradient-overlay"></div>
            </div>
            <div class="profile-info position-relative px-4 px-md-5">
              <div class="avatar-container position-absolute top-0 start-50 translate-middle">
                <div class="avatar-wrapper">
                  <img v-if="admin.profile_pic" :src="admin.profile_pic"
                    class="avatar img-thumbnail rounded-circle border-4 border-white shadow" alt="Profile Picture">
                  <div v-else class="avatar-placeholder rounded-circle border-4 border-white shadow">
                    <i class="bi bi-person fs-1 text-muted"></i>
                  </div>
                </div>
              </div>
              <div class="profile-content text-center pt-5 mt-4">
                <h2 class="mb-1">{{ admin.name }}</h2>
                <p class="text-muted mb-3">
                  <i class="bi bi-briefcase-fill me-1"></i>{{ admin.designation || 'No designation provided' }}
                </p>
                <p class="text-muted small">
                  <i class="bi bi-person-badge me-1"></i>Admin ID: {{ admin.id }}
                </p>
              </div>

              <div class="profile-stats d-flex justify-content-center border-top mt-4 pt-4">
                <button class="btn btn-sm btn-outline-primary" @click="showEditModal">
                  <i class="bi bi-pencil-square me-1"></i>Edit
                </button>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-md-6 mb-4">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Admin Information</h5>
                </div>
                <div class="card-body">
                  <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i
                          class="bi bi-briefcase me-2"></i>Designation:</span>
                      <span>{{ admin.designation || 'Not specified' }}</span>
                    </li>
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i class="bi bi-person-badge me-2"></i>Admin
                        ID:</span>
                      <span>{{ admin.id }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
                </div>
                <div class="card-body">
                  <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i
                          class="bi bi-envelope me-2"></i>Email:</span>
                      <span>{{ admin.email }}</span>
                    </li>
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i class="bi bi-phone me-2"></i>Phone:</span>
                      <span>{{ admin.contact_no || 'Not provided' }}</span>
                    </li>

                    <li class="d-flex" style="flex-wrap: wrap; align-items: flex-start;">
                      <span class="text-muted me-3" style="width: 120px; flex-shrink: 0;">
                        <i class="bi bi-geo-alt me-2"></i>Address:
                      </span>
                      <span style="flex: 1; word-break: break-word;">
                        {{ isAddressExpanded || shortAddress === admin.address ? admin.address : shortAddress + '...' }}
                        <template v-if="admin.address && admin.address.length > maxLength">
                          <a href="#" @click.prevent="isAddressExpanded = !isAddressExpanded" class="ms-2 text-primary"
                            style="cursor: pointer;">
                            {{ isAddressExpanded ? 'See less' : 'See more' }}
                          </a>
                        </template>
                      </span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header">
            <h5 class="modal-title">Edit Admin Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="updateProfile">
              <div class="row">
                <div class="col-md-4 text-center mb-4 mb-md-0">
                  <div class="avatar-edit-wrapper position-relative mx-auto">
                    <img v-if="admin.profile_pic || tempProfilePic"
                      :src="tempProfilePic ? tempProfilePic : admin.profile_pic"
                      class="avatar-edit img-thumbnail rounded-circle border-3 border-primary shadow mb-3"
                      alt="Profile Picture">
                    <div v-else class="avatar-edit-placeholder rounded-circle border-3 border-primary shadow mb-3">
                      <i class="bi bi-person fs-1 text-muted"></i>
                    </div>
                    <label for="profilePicUpload" class="btn btn-sm btn-outline-primary rounded-pill">
                      <i class="bi bi-camera me-1"></i> Change Photo
                      <input id="profilePicUpload" type="file" accept="image/*" @change="handleProfilePicChange"
                        class="d-none">
                    </label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Full Name</label>
                      <input type="text" class="form-control" v-model="formData.name" required maxlength="30"
                        pattern="^[A-Za-z\s'-]{1,30}$"
                        title="Only letters, spaces, apostrophes, and hyphens allowed (max 30 chars)">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Designation</label>
                      <input type="text" class="form-control" v-model="formData.designation" required maxlength="20"
                        pattern="^[A-Za-z\s'-]{1,20}$"
                        title="Only letters, spaces, apostrophes, and hyphens allowed (max 20 chars)">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Email</label>
                      <input type="email" class="form-control" v-model="formData.email" required maxlength="100"
                        pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Enter a valid email address">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Phone Number</label>
                      <input type="tel" class="form-control" v-model="formData.contact_no" maxlength="15"
                        pattern="^\d{11,15}$" title="Enter a valid phone number (11 to 15 digits only)"
                        @keypress="restrictToNumbers" />
                    </div>
                    <div class="col-12">
                      <label class="form-label">Address</label>
                      <textarea class="form-control" v-model="formData.address" maxlength="200" rows="4"
                        style="resize: none;"></textarea>
                      <small class="form-text text-muted">Max 200 characters.</small>
                    </div>
                  </div>
                  <div class="accordion mt-4" id="passwordAccordion">
                    <div class="accordion-item border-0">
                      <h2 class="accordion-header">
                        <button class="accordion-button collapsed shadow-none" type="button" data-bs-toggle="collapse"
                          data-bs-target="#passwordCollapse">
                          Change Password
                        </button>
                      </h2>
                      <div id="passwordCollapse" class="accordion-collapse collapse"
                        data-bs-parent="#passwordAccordion">
                        <div class="accordion-body pt-0">
                          <div class="row g-3">
                            <div class="col-md-6">
                              <label class="form-label">New Password</label>
                              <input type="password" class="form-control" v-model="formData.new_password" maxlength="15"
                                required @input="formData.new_password = formData.new_password.slice(0, 15)">
                              <div v-if="formData.new_password.length > 15" class="text-danger small mt-1">
                                Password cannot exceed 15 characters.
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                  <span v-if="isSubmitting">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Saving...
                  </span>
                  <span v-else>Save Changes</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { useAdminStore } from '../stores/adminStore';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import { Modal } from 'bootstrap';

export default {
  setup() {
    const adminStore = useAdminStore();
    const toast = useToast();
    const isSubmitting = ref(false);
    const tempProfilePic = ref(null);
    const profilePicFile = ref(null);
    const userEmail = ref('');
    let editModal = null;
    const formData = ref({
      name: adminStore.name,
      designation: adminStore.designation,
      contact_no: adminStore.contact_no,
      address: adminStore.address,
      email: '',
      new_password: '',
    });
    onMounted(async () => {
      editModal = new Modal(document.getElementById('editProfileModal'));
    });
    const formatDate = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    };

    const handleProfilePicChange = (event) => {
      const file = event.target.files[0];
      if (file) {
        profilePicFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
          tempProfilePic.value = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    };

    const removeProfilePic = () => {
      tempProfilePic.value = null;
      profilePicFile.value = null;
      formData.value.remove_profile_pic = true;
    };

    const showEditModal = () => {

      formData.value = {
        name: adminStore.name,
        designation: adminStore.designation,
        contact_no: adminStore.contact_no,
        address: adminStore.address,
        email: adminStore.email,
        new_password: '',
      };
      tempProfilePic.value = null;
      profilePicFile.value = null;
      editModal.show();
    };

    const updateProfile = async () => {
      isSubmitting.value = true;

      try {
        const formDataToSend = new FormData();

        for (const key in formData.value) {
          if (formData.value[key] !== null && formData.value[key] !== '') {
            formDataToSend.append(key, formData.value[key]);
          }
        }

        if (profilePicFile.value) {
          formDataToSend.append('profile_pic', profilePicFile.value);
        }

        if (formData.value.remove_profile_pic) {
          formDataToSend.append('remove_profile_pic', 'true');
        }

        const response = await axios.post(`/admin/update-profile/${adminStore.id}`, formDataToSend, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        adminStore.setAdminData(response.data.admin);
        editModal.hide();

        toast.success('Profile updated successfully!', {
          position: 'top-right',
          duration: 3000
        });
      } catch (error) {
        console.error('Error updating profile:', error);

        let message = 'An error occurred while updating your profile.';
        if (error.response && error.response.data) {
          message = error.response.data.message || message;
          if (error.response.data.errors) {
            const errors = error.response.data.errors;
            for (const field in errors) {
              toast.error(errors[field][0], {
                position: 'top-right',
                duration: 5000
              });
            }
            return;
          }
        }

        toast.error(message, {
          position: 'top-right',
          duration: 5000
        });
      } finally {
        isSubmitting.value = false;
      }
    };

    const assetPath = (path) => {
      return `/${path}`;
    };
    const isAddressExpanded = ref(false);
    const maxLength = 30;

    const admin = adminStore;
    const shortAddress = computed(() => {
      if (!admin.address) return '';
      return admin.address.slice(0, maxLength);
    });
    return {
      admin: adminStore,
      shortAddress,
      isAddressExpanded,
      formData,
      isSubmitting,
      tempProfilePic,
      userEmail,
      formatDate,
      handleProfilePicChange,
      removeProfilePic,
      showEditModal,
      updateProfile,
      assetPath,

    };
  }
};
</script>
<style scoped>
.profile-page {
  padding: 2rem 0;
  background-color: #f8f9fa;
  min-height: calc(100vh - var(--header-height));
}

.profile-card {
  border-radius: 12px;
  overflow: hidden;
}

.cover-photo {
  height: 200px;
  background: linear-gradient(135deg, #6e8efb, #a777e3);
  position: relative;
}

.gradient-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3));
}

.profile-info {
  background-color: #fff;
  padding-bottom: 1.5rem;
}

.avatar-container {
  z-index: 1;
}

.avatar-wrapper {
  position: relative;
  width: 120px;
  height: 120px;
}

.avatar {
  width: 120px;
  height: 120px;
  object-fit: cover;
}

.avatar-placeholder {
  width: 120px;
  height: 120px;
  background-color: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-edit-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.profile-content {
  padding-top: 60px;
}

.profile-stats {
  max-width: 400px;
  margin: 0 auto;
}

.stat-item {
  position: relative;
}

.stat-item:not(:last-child):after {
  content: "";
  position: absolute;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 1px;
  height: 30px;
  background-color: #e9ecef;
}
.avatar-edit-wrapper {
  max-width: 180px;
  margin: 0 auto;
}
.avatar-edit {
  width: 150px;
  height: 150px;
  object-fit: cover;
}
.avatar-edit-placeholder {
  width: 150px;
  height: 150px;
  background-color: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 768px) {
  .cover-photo {
    height: 160px;
  }

  .avatar-wrapper {
    width: 100px;
    height: 100px;
  }

  .avatar,
  .avatar-placeholder {
    width: 100px;
    height: 100px;
  }

  .avatar-edit-btn {
    width: 30px;
    height: 30px;
  }

  .profile-content {
    padding-top: 50px;
  }
}

@media (max-width: 576px) {
  .profile-stats {
    flex-direction: column;
    gap: 1rem;
  }

  .stat-item:not(:last-child):after {
    display: none;
  }

  .profile-content h2 {
    font-size: 1.5rem;
  }
}
</style> -->

<template>
  <div class="profile-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="profile-card card border-0 shadow-sm overflow-hidden">
            <div class="cover-photo position-relative">
              <img :src="assetPath('images/back_1.jpg')" alt="Cover" class="w-100" style="height: 200px; object-fit: cover;">
              <div class="gradient-overlay"></div>
            </div>
            <div class="profile-info position-relative px-4 px-md-5">
              <div class="avatar-container position-absolute top-0 start-50 translate-middle">
                <div class="avatar-wrapper">
                  <img v-if="admin.profile_pic" :src="admin.profile_pic"
                    class="avatar img-thumbnail rounded-circle border-4 border-white shadow" alt="Profile Picture">
                  <div v-else class="avatar-placeholder rounded-circle border-4 border-white shadow">
                    <i class="bi bi-person fs-1 text-muted"></i>
                  </div>
                </div>
              </div>
              <div class="profile-content text-center pt-5 mt-4">
                <h2 class="mb-1">{{ admin.name }}</h2>
                <p class="text-muted mb-3">
                  <i class="bi bi-briefcase-fill me-1"></i>{{ admin.designation || 'No designation provided' }}
                </p>
                <p class="text-muted small">
                  <i class="bi bi-person-badge me-1"></i>Admin ID: {{ admin.id }}
                </p>
              </div>

              <div class="profile-stats d-flex justify-content-center border-top mt-4 pt-4">
                <button class="btn btn-sm btn-outline-primary" @click="showEditModal">
                  <i class="bi bi-pencil-square me-1"></i>Edit
                </button>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-md-6 mb-4">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Admin Information</h5>
                </div>
                <div class="card-body">
                  <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i
                          class="bi bi-briefcase me-2"></i>Designation:</span>
                      <span>{{ admin.designation || 'Not specified' }}</span>
                    </li>
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i class="bi bi-person-badge me-2"></i>Admin
                        ID:</span>
                      <span>{{ admin.id }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
                </div>
                <div class="card-body">
                  <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i
                          class="bi bi-envelope me-2"></i>Email:</span>
                      <span>{{ admin.email }}</span>
                    </li>
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i class="bi bi-phone me-2"></i>Phone:</span>
                      <span>{{ admin.contact_no || 'Not provided' }}</span>
                    </li>

                    <li class="d-flex" style="flex-wrap: wrap; align-items: flex-start;">
                      <span class="text-muted me-3" style="width: 120px; flex-shrink: 0;">
                        <i class="bi bi-geo-alt me-2"></i>Address:
                      </span>
                      <span style="flex: 1; word-break: break-word;">
                        {{ isAddressExpanded || shortAddress === admin.address ? admin.address : shortAddress + '...' }}
                        <template v-if="admin.address && admin.address.length > maxLength">
                          <a href="#" @click.prevent="isAddressExpanded = !isAddressExpanded" class="ms-2 text-primary"
                            style="cursor: pointer;">
                            {{ isAddressExpanded ? 'See less' : 'See more' }}
                          </a>
                        </template>
                      </span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header">
            <h5 class="modal-title">Edit Admin Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="updateProfile">
              <div class="row">
                <div class="col-md-4 text-center mb-4 mb-md-0">
                  <div class="avatar-edit-wrapper position-relative mx-auto">
                    <img v-if="admin.profile_pic || tempProfilePic"
                      :src="tempProfilePic ? tempProfilePic : admin.profile_pic"
                      class="avatar-edit img-thumbnail rounded-circle border-3 border-primary shadow mb-3"
                      alt="Profile Picture">
                    <div v-else class="avatar-edit-placeholder rounded-circle border-3 border-primary shadow mb-3">
                      <i class="bi bi-person fs-1 text-muted"></i>
                    </div>
                    <label for="profilePicUpload" class="btn btn-sm btn-outline-primary rounded-pill">
                      <i class="bi bi-camera me-1"></i> Change Photo
                      <input id="profilePicUpload" type="file" accept="image/*" @change="handleProfilePicChange"
                        class="d-none">
                    </label>
                    <button v-if="tempProfilePic" @click="removeTempProfilePic" type="button"
                      class="btn btn-sm btn-outline-danger rounded-pill mt-2">
                      <i class="bi bi-trash me-1"></i> Remove
                    </button>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="row g-3">
                    <!-- Name Field -->
                    <div class="col-md-6">
                      <label class="form-label">Full Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" 
                        :class="{ 'is-invalid': formErrors.name }"
                        v-model="formData.name" 
                        @input="validateField('name')"
                        maxlength="255"
                        required>
                      <div class="invalid-feedback">{{ formErrors.name }}</div>
                    </div>
                    
                    <!-- Designation Field -->
                    <div class="col-md-6">
                      <label class="form-label">Designation</label>
                      <input type="text" class="form-control" 
                        :class="{ 'is-invalid': formErrors.designation }"
                        v-model="formData.designation" 
                        @input="validateField('designation')"
                        maxlength="255">
                      <div class="invalid-feedback">{{ formErrors.designation }}</div>
                    </div>
                    
                    <!-- Email Field -->
                    <div class="col-md-6">
                      <label class="form-label">Email <span class="text-danger">*</span></label>
                      <input type="email" class="form-control" 
                        :class="{ 'is-invalid': formErrors.email }"
                        v-model="formData.email" 
                        @input="validateField('email')"
                        maxlength="255"
                        required>
                      <div class="invalid-feedback">{{ formErrors.email }}</div>
                    </div>
                    
                    <!-- Phone Field -->
                    <div class="col-md-6">
                      <label class="form-label">Phone Number</label>
                      <input type="tel" class="form-control" 
                        :class="{ 'is-invalid': formErrors.contact_no }"
                        v-model="formData.contact_no" 
                        @input="validateField('contact_no')"
                        @keypress="restrictToNumbers"
                        maxlength="20">
                      <div class="invalid-feedback">{{ formErrors.contact_no }}</div>
                    </div>
                    
                    <!-- Address Field -->
                    <div class="col-12">
                      <label class="form-label">Address</label>
                      <textarea class="form-control" 
                        :class="{ 'is-invalid': formErrors.address }"
                        v-model="formData.address" 
                        @input="validateField('address')"
                        maxlength="500" 
                        rows="4"></textarea>
                      <div class="invalid-feedback">{{ formErrors.address }}</div>
                      <small class="form-text text-muted">Max 500 characters.</small>
                    </div>
                  </div>

                  <!-- Password Section -->
                  <div class="accordion mt-4" id="passwordAccordion">
                    <div class="accordion-item border-0">
                      <h2 class="accordion-header">
                        <button class="accordion-button collapsed shadow-none" type="button" 
                          data-bs-toggle="collapse" data-bs-target="#passwordCollapse">
                          Change Password
                        </button>
                      </h2>
                      <div id="passwordCollapse" class="accordion-collapse collapse" 
                        data-bs-parent="#passwordAccordion">
                        <div class="accordion-body pt-0">
                          <div class="row g-3">
                            <div class="col-md-6">
                              <label class="form-label">New Password</label>
                              <input type="password" class="form-control" 
                                :class="{ 'is-invalid': formErrors.password }"
                                v-model="formData.password" 
                                @input="validateField('password')"
                                minlength="6"
                                maxlength="255">
                              <div class="invalid-feedback">{{ formErrors.password }}</div>
                              <small class="form-text text-muted">Minimum 6 characters.</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting || hasFormErrors">
                  <span v-if="isSubmitting">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Saving...
                  </span>
                  <span v-else>Save Changes</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useAdminStore } from '../stores/adminStore';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import { Modal } from 'bootstrap';

export default {
  setup() {
    const adminStore = useAdminStore();
    const toast = useToast();
    const isSubmitting = ref(false);
    const tempProfilePic = ref(null);
    const profilePicFile = ref(null);
    const editModal = ref(null);
    
    // Form errors object
    const formErrors = ref({
      name: '',
      designation: '',
      email: '',
      contact_no: '',
      address: '',
      password: '',
      profile_pic: ''
    });

    // Form data with proper initial values
    const formData = ref({
      name: adminStore.name,
      designation: adminStore.designation,
      contact_no: adminStore.contact_no,
      address: adminStore.address,
      email: adminStore.email,
      password: ''
    });

    // Address expansion
    const isAddressExpanded = ref(false);
    const maxLength = 30;
    const shortAddress = computed(() => {
      if (!adminStore.address) return '';
      return adminStore.address.slice(0, maxLength);
    });

    // Computed property to check if form has errors
    const hasFormErrors = computed(() => {
      return Object.values(formErrors.value).some(error => error !== '');
    });

    onMounted(() => {
      editModal.value = new Modal(document.getElementById('editProfileModal'));
    });

    // Helper function for asset paths
    const assetPath = (path) => {
      return `/${path}`;
    };

    // Input restrictions
    const restrictToNumbers = (event) => {
      const charCode = event.which ? event.which : event.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        event.preventDefault();
      }
    };

    // Field validation
    const validateField = (field) => {
      const value = formData.value[field];
      
      switch(field) {
        case 'name':
          if (!value) {
            formErrors.value.name = 'Full name is required';
          } else if (value.length > 255) {
            formErrors.value.name = 'Name must be less than 255 characters';
          } else {
            formErrors.value.name = '';
          }
          break;
          
        case 'designation':
          if (value && value.length > 255) {
            formErrors.value.designation = 'Designation must be less than 255 characters';
          } else {
            formErrors.value.designation = '';
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
          
        case 'contact_no':
          if (value && value.length > 20) {
            formErrors.value.contact_no = 'Phone number must be less than 20 characters';
          } else if (value && !/^\d*$/.test(value)) {
            formErrors.value.contact_no = 'Only numbers allowed';
          } else {
            formErrors.value.contact_no = '';
          }
          break;
          
        case 'address':
          if (value && value.length > 500) {
            formErrors.value.address = 'Address must be less than 500 characters';
          } else {
            formErrors.value.address = '';
          }
          break;
          
        case 'password':
          if (value && value.length < 6) {
            formErrors.value.password = 'Password must be at least 6 characters';
          } else {
            formErrors.value.password = '';
          }
          break;
      }
    };

    const handleProfilePicChange = (event) => {
      const file = event.target.files[0];
      if (file) {
        // Validate image
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!validTypes.includes(file.type)) {
          formErrors.value.profile_pic = 'Only JPEG, PNG or JPG images are allowed';
          return;
        }
        
        if (file.size > maxSize) {
          formErrors.value.profile_pic = 'Image size must be less than 2MB';
          return;
        }
        
        formErrors.value.profile_pic = '';
        profilePicFile.value = file;
        
        const reader = new FileReader();
        reader.onload = (e) => {
          tempProfilePic.value = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    };

    const removeTempProfilePic = () => {
      tempProfilePic.value = null;
      profilePicFile.value = null;
      document.getElementById('profilePicUpload').value = '';
    };

    const showEditModal = () => {
      // Reset form data and errors when opening modal
      formData.value = {
        name: adminStore.name,
        designation: adminStore.designation,
        contact_no: adminStore.contact_no,
        address: adminStore.address,
        email: adminStore.email,
        password: ''
      };
      
      Object.keys(formErrors.value).forEach(key => {
        formErrors.value[key] = '';
      });
      
      tempProfilePic.value = null;
      profilePicFile.value = null;
      editModal.value.show();
    };

    const updateProfile = async () => {
      isSubmitting.value = true;
      
      try {
        const formDataToSend = new FormData();
        
        // Add all form fields
        for (const key in formData.value) {
          if (formData.value[key] !== null && formData.value[key] !== '') {
            formDataToSend.append(key, formData.value[key]);
          }
        }
        
        // Add profile picture if changed
        if (profilePicFile.value) {
          formDataToSend.append('profile_pic', profilePicFile.value);
        }
        
        const response = await axios.post(`/admin/update-profile/${adminStore.id}`, formDataToSend, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        
        // Update store with new data
        adminStore.setAdminData(response.data.admin);
        
        // Close modal and show success message
        editModal.value.hide();
        
        toast.success('Profile updated successfully!', {
          position: 'top-right',
          duration: 3000
        });
      } catch (error) {
        console.error('Error updating profile:', error);
        
        // Handle validation errors
        if (error.response?.status === 422) {
          const errors = error.response.data.errors;
          Object.keys(errors).forEach(key => {
            if (formErrors.value.hasOwnProperty(key)) {
              formErrors.value[key] = errors[key][0];
            }
          });
          
          // Show general error message if there are unhandled errors
          const unhandledErrors = Object.keys(errors).filter(
            key => !formErrors.value.hasOwnProperty(key)
          );
          
          if (unhandledErrors.length > 0) {
            toast.error(errors[unhandledErrors[0]][0], {
              position: 'top-right',
              duration: 5000
            });
          }
        } else {
          // Show general error message
          const message = error.response?.data?.message || 'An error occurred while updating your profile.';
          toast.error(message, {
            position: 'top-right',
            duration: 5000
          });
        }
      } finally {
        isSubmitting.value = false;
      }
    };

    return {
      admin: adminStore,
      formData,
      formErrors,
      hasFormErrors,
      isSubmitting,
      tempProfilePic,
      isAddressExpanded,
      shortAddress,
      maxLength,
      assetPath,
      validateField,
      restrictToNumbers,
      handleProfilePicChange,
      removeTempProfilePic,
      showEditModal,
      updateProfile
    };
  }
};
</script>

<style scoped>
.profile-page {
  padding: 2rem 0;
  background-color: #f8f9fa;
  min-height: calc(100vh - var(--header-height));
}

.profile-card {
  border-radius: 12px;
  overflow: hidden;
}

.cover-photo {
  height: 200px;
  background: linear-gradient(135deg, #6e8efb, #a777e3);
  position: relative;
}

.gradient-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3));
}

.profile-info {
  background-color: #fff;
  padding-bottom: 1.5rem;
}

.avatar-container {
  z-index: 1;
}

.avatar-wrapper {
  position: relative;
  width: 120px;
  height: 120px;
}

.avatar {
  width: 120px;
  height: 120px;
  object-fit: cover;
}

.avatar-placeholder {
  width: 120px;
  height: 120px;
  background-color: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-edit-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.profile-content {
  padding-top: 60px;
}

.profile-stats {
  max-width: 400px;
  margin: 0 auto;
}

.stat-item {
  position: relative;
}

.stat-item:not(:last-child):after {
  content: "";
  position: absolute;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 1px;
  height: 30px;
  background-color: #e9ecef;
}

.avatar-edit-wrapper {
  max-width: 180px;
  margin: 0 auto;
}

.avatar-edit {
  width: 150px;
  height: 150px;
  object-fit: cover;
}

.avatar-edit-placeholder {
  width: 150px;
  height: 150px;
  background-color: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Validation Styles */
.is-invalid {
  border-color: #dc3545;
}

.is-invalid:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.875em;
  color: #dc3545;
}

@media (max-width: 768px) {
  .cover-photo {
    height: 160px;
  }

  .avatar-wrapper {
    width: 100px;
    height: 100px;
  }

  .avatar,
  .avatar-placeholder {
    width: 100px;
    height: 100px;
  }

  .avatar-edit-btn {
    width: 30px;
    height: 30px;
  }

  .profile-content {
    padding-top: 50px;
  }
}

@media (max-width: 576px) {
  .profile-stats {
    flex-direction: column;
    gap: 1rem;
  }

  .stat-item:not(:last-child):after {
    display: none;
  }

  .profile-content h2 {
    font-size: 1.5rem;
  }
}
</style>