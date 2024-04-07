import { defineStore } from "pinia";

export const useUserStore = defineStore("UserStore", {
    state: () => ({
        form: {
            first_name: "",
            last_name: "",
            email: "",
            role: "",
            workspacesIds: [],
        },
        initialized: false,
    }),
    actions: {
        updateForm(formData) {
            this.form = { ...this.form, ...formData };
            this.initialized = true;
        },
    },
});
