import {defineStore} from 'pinia';

export const useRegistriesStore = defineStore('registries', {
    // The state contains the core variables of the store.
    state: () => ({
        selectedRegistriesIds: new Set(), // Holds the IDs of selected registries
        selectAll: false, // State to track if 'Select All' is checked
        isInitialized: false // New flag
    }),
    actions: {
        // Initializes selected registries with provided workspace registry IDs.
        // Ensures this initialization happens only once.
        initializeWorkspaceRegistries(workspaceRegistriesIds, allRegistriesIdsCount) {
            if (!this.isInitialized) {
                workspaceRegistriesIds.forEach(id => this.selectedRegistriesIds.add(id));
                this.isInitialized = true;
            }

            this.selectAll = this.selectedRegistriesIds.size === allRegistriesIdsCount;
        },

        // Toggles the selection status of a single registry.
        // Adds the ID to selectedRegistriesIds if not present, removes if it is.
        toggleRegistry(registryId) {
            if (this.selectedRegistriesIds.has(registryId)) {
                this.selectedRegistriesIds.delete(registryId);
            } else {
                this.selectedRegistriesIds.add(registryId);
            }
        },

        // Sets the 'Select All' state. If true, all registries are added to the selection;
        // if false, the selection is cleared.
        setSelectAll(value, allRegistriesIds = []) {
            this.selectAll = value;
            if (value) {
                allRegistriesIds.forEach(registryId => this.selectedRegistriesIds.add(registryId));
            } else {
                this.selectedRegistriesIds.clear();
            }
        },

        // Checks if the count of selected registries matches the total count.
        // Updates 'selectAll' based on this comparison.
        updateSelectAllState(allRegistriesIdsCount) {
            this.selectAll = this.selectedRegistriesIds.size === allRegistriesIdsCount;
        }
    },
    getters: {
        // Getter to return the selected registries as an array
        selectedRegistriesIdsArray: (state) => {
            return Array.from(state.selectedRegistriesIds);
        }
    },
});
