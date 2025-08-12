<template>
  <div class="profile-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="profile-card card border-0 shadow-sm overflow-hidden">
            <div class="cover-photo position-relative">
              <img :src="assetPath('images/back_1.jpg')" alt="Cover" class="w-100"
                style="height: 200px; object-fit: cover;" />
              <div class="gradient-overlay"></div>
            </div>
            <div class="profile-info position-relative px-4 px-md-5">
              <div class="avatar-container position-absolute top-0 start-50 translate-middle">
                <div class="avatar-wrapper">
                  <img v-if="customer.profile_pic" :src="customer.profile_pic"
                    class="avatar img-thumbnail rounded-circle border-4 border-white shadow" alt="Profile Picture">
                  <div v-else class="avatar-placeholder rounded-circle border-4 border-white shadow">
                    <i class="bi bi-person fs-1 text-muted"></i>
                  </div>
                </div>
              </div>

              <div class="profile-content text-center pt-5 mt-4">
                <h2 class="mb-1">{{ customer.name }}</h2>
                <p class="text-muted mb-3">
                  <i class="bi bi-geo-alt-fill me-1"></i>{{ customer.address || 'No address provided' }}
                </p>
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
                  <h5 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                  <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i
                          class="bi bi-gender-ambiguous me-2"></i>Gender:</span>
                      <span>{{ customer.gender || 'Not specified' }}</span>
                    </li>
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i class="bi bi-calendar-event me-2"></i>Date
                        of Birth:</span>
                      <span>{{ customer.dob ? formatDate(customer.dob) : 'Not specified' }}</span>
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
                      <span>{{ customer.email }}</span>
                    </li>
                    <li class="mb-3 d-flex">
                      <span class="text-muted me-3" style="width: 120px;"><i class="bi bi-phone me-2"></i>Phone:</span>
                      <span>{{ customer.phone_no || 'Not provided' }}</span>
                    </li>

                    <li class="d-flex align-items-start mb-2">
                      <span class="text-muted fw-semibold d-inline-block" style="width: 130px;">
                        <i class="bi bi-geo-alt me-2"></i>Address:
                      </span>
                      <span>{{ customer.address || 'Not provided' }}</span>
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
            <h5 class="modal-title">Edit Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="updateProfile">
              <div class="row">
                <div class="col-md-4 text-center mb-4 mb-md-0">
                  <div class="avatar-edit-wrapper position-relative mx-auto">
                    <img v-if="customer.profile_pic || tempProfilePic"
                      :src="tempProfilePic ? tempProfilePic : customer.profile_pic"
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
                      <input type="text" class="form-control" v-model="formData.name" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Email</label>
                      <input type="email" class="form-control" v-model="formData.email" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Date of Birth</label>
                      <!-- <input type="date" class="form-control" v-model="formData.dob"
                        :max="new Date(new Date().setFullYear(new Date().getFullYear() - 10)).toISOString().split('T')[0]"> -->
                      <input type="date" class="form-control" v-model="formData.dob"
                        :max="new Date().toISOString().split('T')[0]">

                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Gender</label>
                      <select class="form-select" v-model="formData.gender">
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Phone Number</label>
                      <input type="tel" class="form-control" v-model="formData.phone_no" pattern="^[0-9]{11,15}$"
                        maxlength="15" minlength="11" inputmode="numeric"
                        @input="formData.phone_no = formData.phone_no.replace(/\D/g, '')">
                    </div>

                    <div class="col-12">
                      <label class="form-label">Address</label>
                      <textarea class="form-control" rows="2" v-model="formData.address" maxlength="200"
                        style="resize: none; overflow: hidden;"></textarea>
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
                              <input type="password" class="form-control" v-model="formData.new_password">
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
import { useCustomerStore } from '../stores/customerStore';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import { Modal } from 'bootstrap';

export default {
  setup() {
    const customerStore = useCustomerStore();
    const toast = useToast();
    const isSubmitting = ref(false);
    const tempProfilePic = ref(null);
    const profilePicFile = ref(null);
    const userEmail = ref('');
    let editModal = null;
    const formData = ref({
      name: customerStore.name,
      address: customerStore.address,
      phone_no: customerStore.phone_no,
      dob: customerStore.dob,
      gender: customerStore.gender,
      email: '',
      new_password: '',
      profile_pic: customerStore.profile_pic
    });
    onMounted(async () => {
      editModal = new Modal(document.getElementById('editProfileModal'));
    });
    const formattedJoinDate = computed(() => {
      if (!customerStore.created_at) return '';
      const date = new Date(customerStore.created_at);
      return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
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
        name: customerStore.name,
        address: customerStore.address,
        phone_no: customerStore.phone_no,
        dob: customerStore.dob,
        gender: customerStore.gender,
        email: customerStore.email,
        new_password: '',
        remove_profile_pic: false
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
        const response = await axios.post(`/update/user/${customerStore.id}`, formDataToSend, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        customerStore.setCustomerData(response.data.customer);
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
    return {
      customer: customerStore,
      formData,
      isSubmitting,
      tempProfilePic,
      userEmail,
      formattedJoinDate,
      formatDate,
      handleProfilePicChange,
      removeProfilePic,
      showEditModal,
      updateProfile,
      assetPath
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
</style>