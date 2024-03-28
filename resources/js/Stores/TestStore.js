import {defineStore} from 'pinia';

export const useTestStore = defineStore('user', {
    state: () => ({
        profile: {}
    }),
    actions: {
        setProfile(data) {
            this.profile = data;
        }
    }
});
