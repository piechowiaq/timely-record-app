import {defineStore} from "pinia";

const WORKSPACE_NAVIGATION_OPTIONS = [
    {name: 'Dashboard', route: 'workspaces.dashboard', iconName: 'fa-gauge-simple-high'},
    {name: 'Registries', route: 'workspace.registries.index', iconName: 'fa-box-archive'},
];

const PROJECT_NAVIGATION_OPTIONS = [
    {name: 'Dashboard', route: 'projects.dashboard', iconName: 'fa-gauge-simple-high'},
    {name: 'Users', route: 'users.index', iconName: 'fa-users'},
    {name: 'Registries', route: 'registries.index', iconName: 'fa-box-archive'},
    {name: 'Workspaces', route: 'workspaces.index', iconName: 'fa-building-shield'},
];

export const useNavigationStore = defineStore("NavigationStore", {
    state: () => ({
        workspaceOptions: WORKSPACE_NAVIGATION_OPTIONS,
        projectOptions: PROJECT_NAVIGATION_OPTIONS
    })
})
