// stores/selectAllStore.js
import {defineStore} from 'pinia';

export const useSelectAllStore = defineStore('selectAllStore', {
    state: () => ({
        selectedWorkspaces: new Set(),
        isSelectAll: false,
    }),
    actions: {
        toggleSelectAll(value) {
            this.isSelectAll = value;
        },
        selectWorkspace(id) {
            this.selectedWorkspaces.add(id);
        },
        deselectWorkspace(id) {
            this.selectedWorkspaces.delete(id);
        },
        clearSelections() {
            this.selectedWorkspaces.clear();
        }
    },
});
