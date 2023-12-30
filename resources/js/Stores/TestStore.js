// stores/registries.js
import {defineStore} from 'pinia';

export const useTestStore = defineStore('test', {
    // The state contains the core variables of the store.
    state: () => ({
        selectedRegistries: new Set(), // Holds the IDs of selected registries
        selectAll: false, // State to track if 'Select All' is checked
        isInitialized: false // New flag
    }),
    actions: {
        // Initializes selected registries with provided workspace registry IDs.
        // Ensures this initialization happens only once.
        initializeWorkspaceRegistries(workspaceRegistriesIds) {
            if (!this.isInitialized) {
                workspaceRegistriesIds.forEach(id => this.selectedRegistries.add(id));
                this.isInitialized = true;
            }
        },

        // Toggles the selection status of a single registry.
        // Adds the ID to selectedRegistries if not present, removes if it is.
        toggleRegistry(registryId) {
            if (this.selectedRegistries.has(registryId)) {
                this.selectedRegistries.delete(registryId);
            } else {
                this.selectedRegistries.add(registryId);
            }
        },

        // Sets the 'Select All' state. If true, all registries are added to the selection;
        // if false, the selection is cleared.
        setSelectAll(value, allRegistriesIds = []) {
            this.selectAll = value;
            if (value) {
                allRegistriesIds.forEach(registryId => this.selectedRegistries.add(registryId));
            } else {
                this.selectedRegistries.clear();
            }
        },

        // Checks if the count of selected registries matches the total count.
        // Updates 'selectAll' based on this comparison.
        updateSelectAllState(countOfTotalRegistries) {
            this.selectAll = this.selectedRegistries.size === countOfTotalRegistries;
        }
    }
});
