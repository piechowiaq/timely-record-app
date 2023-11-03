import {defineStore} from "pinia";

const WORKSPACE_NAVIGATION_OPTIONS = [
    {name: 'Dashboard', route: 'workspaces.dashboard', iconName: 'fa-gauge-simple-high'},
    {name: 'Registries', route: 'login', iconName: 'fa-box-archive'},
];

const PROJECT_NAVIGATION_OPTIONS = [
    {name: 'Dashboard', route: 'projects.dashboard', iconName: 'fa-gauge-simple-high'},
];

export const useNavigationStore = defineStore("NavigationStore", {
    state: () => ({
        workspaceOptions: WORKSPACE_NAVIGATION_OPTIONS,
        projectOptions: PROJECT_NAVIGATION_OPTIONS
    })
})
