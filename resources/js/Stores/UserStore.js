import {defineStore} from "pinia";

export const useUserStore = defineStore("UserStore", {
    state: () => ({
        form: {
            first_name: '',
            last_name: '',
            email: '',
            role: '',
            workspacesIds: [],
        }
    }),
    actions: {
        setFirstName(value) {
            this.form.first_name = value;
        },
        setLastName(value) {
            this.form.last_name = value;
        },
        setEmail(value) {
            this.form.email = value;
        },
        setRole(value) {
            this.form.role = value;
        },
        setWorkspacesIds(value) {
            this.form.workspacesIds = value;
        },
    },
    getters: {
        getFirstName() {
            return this.form.first_name;
        },
        getLastName() {
            return this.form.last_name;
        },
        getEmail() {
            return this.form.email;
        },
        getRole() {
            return this.form.role;
        },
        getWorkspacesIds() {
            return this.form.workspacesIds;
        },
    }
})
