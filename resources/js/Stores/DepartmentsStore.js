import { defineStore } from "pinia";

export const useDepartmentsStore = defineStore("departments", {
    state: () => ({
        form: {
            departmentsIds: [],
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
