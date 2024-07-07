import { defineStore } from "pinia";

export const useDepartmentStore = defineStore("DepartmentStore", {
    state: () => ({
        form: {
            name: "",
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
