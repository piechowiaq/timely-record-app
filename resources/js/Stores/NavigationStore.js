import { defineStore } from "pinia";

const NAVIGATION_OPTIONS = [
    { name: 'Dashboard', route: 'dashboard', iconName: 'fa-gauge-simple-high' },
    { name: 'Registries', route: 'login', iconName: 'fa-box-archive' },
];

export const useNavigationStore = defineStore("NavigationStore", {
    state: () => ({
        options: NAVIGATION_OPTIONS
    })
})
