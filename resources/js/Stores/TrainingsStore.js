import { defineStore } from "pinia";

export const useTrainingsStore = defineStore("trainings", {
    state: () => ({
        form: {
            trainingsIds: [],
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
