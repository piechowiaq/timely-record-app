import {defineStore} from 'pinia';

export const useRegistriesStore = defineStore('registries', {
    state: () => ({
        // The current page number
        currentPage: 1,
        // Object to track selected registries per page
        selectedRegistries: new Set(), // Initial
        registriesData: [],
        allRegistryIds: new Set(),

    }),
    actions: {
        selectRegistry(registryId) {

            this.selectedRegistries.add(registryId);
        },
        deselectRegistry(registryId) {
            this.selectedRegistries.delete(registryId);
        },

        setRegistriesData(data) {
            this.registriesData = data;
        },
        setAllRegistryIds(ids) {
            this.allRegistryIds = new Set(ids);
        },
        selectAllRegistries() {
            this.selectedRegistries = new Set(this.allRegistryIds);
        },
        deselectAllRegistries() {
            this.selectedRegistries.clear();
        },
        resetSelection() {
            this.selectedRegistries.clear();
        }
    },
    getters: {

        selectedRegistriesIds: (state) => {
            // Convert the Set of selected registry IDs to an array
            return Array.from(state.selectedRegistries);
        },
        isAllSelected: (state) => {
            // Check if the count of selected registries is equal to the count of all registry IDs
            return state.allRegistryIds.size === state.selectedRegistries.size;
        },
    }
});
