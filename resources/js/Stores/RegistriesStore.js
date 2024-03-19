import {defineStore} from 'pinia';

export const useRegistriesStore = defineStore('registries', {
    state: () => ({
        form: {
            registriesIds: [],
        },
        initialized: false,
    }),
    actions: {
        updateForm(formData) {
            this.form = {...this.form, ...formData};
            this.initialized = true;
        },
    },
});
