import {defineStore} from 'pinia';

export const useWorkspacesStore = defineStore('workspaces', {
    state: () => ({
        // The current page number
        currentPage: 1,
        // Object to track selected workspaces per page
        selectedWorkspaces: new Set(), // Initial
        workspacesData: [],
        allWorkspaceIds: [],

    }),
    actions: {
        selectWorkspace(workspaceId) {

            this.selectedWorkspaces.add(workspaceId);
        },
        deselectWorkspace(workspaceId) {
            this.selectedWorkspaces.delete(workspaceId);
        },

        setWorkspacesData(data) {
            this.workspacesData = data;
        },
        setAllWorkspaceIds(ids) {
            this.allWorkspaceIds = new Set(ids);
        },
        selectAllWorkspaces() {
            if (!this.allWorkspaceIds) {
                // Initialize as an empty set if undefined
                this.allWorkspaceIds = new Set();
            }

            // Now you can safely use forEach
            this.allWorkspaceIds.forEach(workspaceId => {
                if (!this.selectedWorkspaces) {
                    this.selectedWorkspaces = new Set();
                }
                this.selectedWorkspaces.add(workspaceId);
            });

        },


        deselectAllWorkspaces() {
            this.selectedWorkspaces = new Set();
        }
    },
    getters: {

        selectedWorkspacesIds: (state) => {
            // Convert the Set of selected workspace IDs to an array
            return Array.from(state.selectedWorkspaces);
        },
        isAllSelected: (state) => {
            // Check if the count of selected workspaces is equal to the count of all workspace IDs
            return state.allWorkspaceIds.size === state.selectedWorkspaces.size;
        },
    }
});
