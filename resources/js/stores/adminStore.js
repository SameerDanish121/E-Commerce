
import { defineStore } from 'pinia';

export const useAdminStore = defineStore('admin', {
  persist: true,
  state: () => ({
    id: null,
    user_id: null,
    name: '',
    designation: '',
    contact_no: '',
    address: '',
    profile_pic: '',
    email: '',
    permissions: [],
  }),
  actions: {
    setAdminData(admin) {
      this.id = admin.id;
      this.user_id = admin.user_id;
      this.name = admin.name;
      this.designation = admin.designation;
      this.contact_no = admin.contact_no;
      this.address = admin.address;
      this.profile_pic = admin.profile_pic;
      this.email = admin.user.email;
    },
    setPermissions(permissions) {
      this.permissions = permissions;
    },
    hasPermission(permissionName) {
      return this.permissions.includes(permissionName);
    },
    logout() {
      this.$reset();
    }
  },
});
