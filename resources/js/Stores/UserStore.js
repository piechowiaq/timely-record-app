import {defineStore} from "pinia";

export const useUserStore = defineStore("UserStore", {
    state: () => ({
        form: {
            first_name: '',
            last_name: '',
            email: '',
            role: '',
            workspacesIds: [],
        },
    }),
    actions: {
        updateForm(formData) {
            this.form = {...this.form, ...formData};
        },
        setWorkspacesIds(value) {
            this.form.workspacesIds = value;
        },
    },
})
