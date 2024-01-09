import {defineStore} from 'pinia';

export const useWorkspacesStore = defineStore('workspaces', {
    state: () => ({

        selectAll: false,
        isInitialized: false, // New flag
        selectedWorkspacesIds: new Set(), // Initial

    }),
    actions: {
        initializeWorkspaces(workspacesIds, allWorkspacesIdsCount) {
            if (!this.isInitialized) {
                workspacesIds.forEach(id => this.selectedWorkspacesIds.add(id));
                this.isInitialized = true;
            }

            this.selectAll = this.selectedWorkspacesIds.size === allWorkspacesIdsCount;
        },


        // Toggles the selection status of a single registry.
        // Adds the ID to selectedRegistriesIds if not present, removes if it is.
        toggleWorkspace(workspaceId) {
            if (this.selectedWorkspacesIds.has(workspaceId)) {
                this.selectedWorkspacesIds.delete(workspaceId);
            } else {
                this.selectedWorkspacesIds.add(workspaceId);
            }
        },

        // Checks if the count of selected registries matches the total count.
        // Updates 'selectAll' based on this comparison.
        updateSelectAllState(allWorkspacesIdsCount) {
            this.selectAll = this.selectedWorkspacesIds.size === allWorkspacesIdsCount;
        },

        setSelectAll(value, allWorkspacesIds = []) {
            this.selectAll = value;
            if (value) {
                allWorkspacesIds.forEach(workspaceId => this.selectedWorkspacesIds.add(workspaceId));
            } else {
                this.selectedWorkspacesIds.clear();
            }
        },

        // Clear selected registries and reset initialization state.
        clearSelectedWorkspaces() {
            this.selectedWorkspacesIds.clear();
            this.isInitialized = false;
        },
    },


    getters: {


        selectedWorkspacesIdsArray: (state) => {
            return Array.from(state.selectedWorkspacesIds);
        }
    }
});
