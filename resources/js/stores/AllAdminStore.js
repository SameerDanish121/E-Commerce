// import { defineStore } from 'pinia';
// import { ref } from 'vue';
// import axios from 'axios';

// export const useAllAdminStore = defineStore('AllAdminStore', () => {
//     const admins = ref([]);
//     const isLoading = ref(false);
//     const error = ref(null);
//     async function fetchAllAdmins() {
//         try {
//             isLoading.value = true;
//             const response = await axios.get('/admins');
//             admins.value = response.data.admins;
//         } catch (err) {
//             handleError(err);
//         } finally {
//             isLoading.value = false;
//         }
//     }
//     async function addAdmin(formData) {
//         try {
//             isLoading.value = true;
//             const response = await axios.post('/admins', formData, {
//                 headers: { 'Content-Type': 'multipart/form-data' }
//             });
//             admins.value.push(response.data.admin);
//             return response.data;
//         } catch (err) {
//             handleError(err);
//             throw err;
//         } finally {
//             isLoading.value = false;
//         }
//     }


//     async function updateAdmin(id, formData) {
//         try {
//             isLoading.value = true;
//             const response = await axios.post(`/admins/${id}?_method=PUT`, formData, {
//                 headers: { 'Content-Type': 'multipart/form-data' }
//             });

//             const index = admins.value.findIndex(a => a.id === id);
//             if (index !== -1) {
//                 admins.value[index] = response.data.admin;
//             }
//             return response.data;
//         } catch (err) {
//             handleError(err);
//             throw err;
//         } finally {
//             isLoading.value = false;
//         }
//     }

   
//     async function deleteAdmin(id) {
//         try {
//             isLoading.value = true;
//             await axios.delete(`/admins/${id}`);
//             admins.value = admins.value.filter(a => a.id !== id);
//         } catch (err) {
//             handleError(err);
//             throw err;
//         } finally {
//             isLoading.value = false;
//         }
//     }

//     function handleError(err) {
//         if (err.response && err.response.data) {
//             error.value = err.response.data.message || 'Server Error';
//         } else {
//             error.value = err.message || 'Network Error';
//         }
//         console.error('Admin Store Error:', error.value);
//     }

//     return {
//         admins,
//         isLoading,
//         error,
//         fetchAllAdmins,
//         addAdmin,
//         updateAdmin,
//         deleteAdmin
//     };
// });

import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useAllAdminStore = defineStore('AllAdminStore', () => {
    const admins = ref([]);
    const permissions = ref([]);
    const isLoading = ref(false);
    const error = ref(null);

    async function fetchAllAdmins() {
        try {
            isLoading.value = true;
            const response = await axios.get('/admins');
            admins.value = response.data.admins;
        } catch (err) {
            handleError(err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchAllPermissions() {
        try {
            isLoading.value = true;
            const response = await axios.get('/permissions');
            permissions.value = response.data.permissions;
        } catch (err) {
            handleError(err);
        } finally {
            isLoading.value = false;
        }
    }

    async function addAdmin(formData) {
        try {
            isLoading.value = true;
            const response = await axios.post('/admins', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            admins.value.push(response.data.admin);
            return response.data;
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function updateAdmin(id, formData) {
        try {
            isLoading.value = true;
            const response = await axios.post(`/admins/${id}?_method=PUT`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            const index = admins.value.findIndex(a => a.id === id);
            if (index !== -1) {
                admins.value[index] = response.data.admin;
            }
            return response.data;
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function deleteAdmin(id) {
        try {
            isLoading.value = true;
            await axios.delete(`/admins/${id}`);
            admins.value = admins.value.filter(a => a.id !== id);
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function createPermission(permissionData) {
        try {
            isLoading.value = true;
            const response = await axios.post('/permissions', permissionData);
            permissions.value.push(response.data.permission);
            return response.data;
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function updatePermission(id, permissionData) {
        try {
            isLoading.value = true;
            const response = await axios.put(`/permissions/${id}`, permissionData);
            const index = permissions.value.findIndex(p => p.id === id);
            if (index !== -1) {
                permissions.value[index] = response.data.permission;
            }
            return response.data;
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function deletePermission(id) {
        try {
            isLoading.value = true;
            await axios.delete(`/permissions/${id}`);
            permissions.value = permissions.value.filter(p => p.id !== id);
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function assignPermission(adminId, permissionId) {
        try {
            isLoading.value = true;
            await axios.post('/assign-permission', {
                admin_id: adminId,
                permission_id: permissionId
            });
            
            // Update the admin's permissions in the store
            const adminIndex = admins.value.findIndex(a => a.id === adminId);
            if (adminIndex !== -1) {
                const permission = permissions.value.find(p => p.id === permissionId);
                if (permission && !admins.value[adminIndex].permissions.includes(permission.name)) {
                    admins.value[adminIndex].permissions.push(permission.name);
                }
            }
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function removePermission(adminId, permissionId) {
        try {
            isLoading.value = true;
            await axios.post('/remove-permission', {
                admin_id: adminId,
                permission_id: permissionId
            });
            
            // Update the admin's permissions in the store
            const adminIndex = admins.value.findIndex(a => a.id === adminId);
            if (adminIndex !== -1) {
                const permission = permissions.value.find(p => p.id === permissionId);
                if (permission) {
                    admins.value[adminIndex].permissions = 
                        admins.value[adminIndex].permissions.filter(p => p !== permission.name);
                }
            }
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    function handleError(err) {
        if (err.response && err.response.data) {
            error.value = err.response.data.message || 'Server Error';
        } else {
            error.value = err.message || 'Network Error';
        }
        console.error('Admin Store Error:', error.value);
    }

    return {
        admins,
        permissions,
        isLoading,
        error,
        fetchAllAdmins,
        fetchAllPermissions,
        addAdmin,
        updateAdmin,
        deleteAdmin,
        createPermission,
        updatePermission,
        deletePermission,
        assignPermission,
        removePermission
    };
});
