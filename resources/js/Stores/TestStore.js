// stores/registries.js
import {defineStore} from 'pinia';

export const useTestStore = defineStore('test', {
    state: () => ({
        selectedRegistries: new Set(), // Holds the IDs of selected registries
        selectAll: false // State to track if 'Select All' is checked
    }),
    actions: {

        // Toggles the selection status of a single registry
        toggleRegistry(registryId) {
            if (this.selectedRegistries.has(registryId)) {
                this.selectedRegistries.delete(registryId);
            } else {
                this.selectedRegistries.add(registryId);
            }
        },

        // Updates the 'Select All' state and modifies the selection accordingly
        setSelectAll(value, allRegistriesIds = []) {
            this.selectAll = value;
            if (value) {
                allRegistriesIds.forEach(registryId => this.selectedRegistries.add(registryId));
            } else {
                this.selectedRegistries.clear();
            }
        },

        // Checks if all registries are selected, updates 'selectAll' accordingly
        updateSelectAllState(countOfTotalRegistries) {
            this.selectAll = this.selectedRegistries.size === countOfTotalRegistries;
        }
    }
});
