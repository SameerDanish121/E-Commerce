import { defineStore } from 'pinia';

export const useCustomerStore = defineStore('customer', {
    state: () => ({
        id: null,
        user_id: null,
        name: '',
        address: '',
        phone_no: '',
        dob: '',
        gender: '',
        profile_pic: '',
        email:''
    }),
    actions: {
        setCustomerData(customer) {
            this.id = customer.id;
            this.user_id = customer.user_id;
            this.name = customer.name;
            this.address = customer.address;
            this.phone_no = customer.phone_no;
            this.dob = customer.dob;
            this.gender = customer.gender;
            this.profile_pic = customer.profile_pic;
            this.email=customer.users.email;
        },
        logout() {
            this.$reset();
        }
    },persist: true 
});
