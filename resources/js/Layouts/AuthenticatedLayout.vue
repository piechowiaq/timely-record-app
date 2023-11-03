<script setup>
import {ref} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {Link, usePage} from '@inertiajs/vue3';
import {useNavigationStore} from "@/Stores/NavigationStore.js";
import FlashMessages from "@/Components/FlashMessages.vue";

const showingNavigationDropdown = ref(false);

const projectId = usePage().props.auth.user.project_id;

const navigation = useNavigationStore();

const user = usePage().props.auth.user;

const userHasNoWorkspace = !user.workspaces || !user.workspaces.length

const props = defineProps({
    workspace: {
        type: Object
    },
})

const page = usePage().props.route;

const showWorkspaceNavigation = Boolean(props.workspace) && !page.endsWith('/edit');
const showProjectNavigation = Boolean(props.workspace) && page.endsWith('/edit') || !Boolean(props.workspace)


</script>

<template>
    <div>
        <div class="min-h-screen flex-col flex bg-gray-100 dark:bg-gray-900">
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <!-- Primary Navigation Menu -->
                <div class=" px-4 ">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center justify-center w-56 -ml-4">
                                <Link :href="route('dashboard')">
                                    <div class="flex justify-start items-center">
                                        <ApplicationLogo class="w-10 h-10"/>
                                        <p class="ml-2 font-bold whitespace-nowrap tracking-widest text-gray-600"><span
                                            class="text-cyan-600 ">TIMELY</span> RECORD</p>
                                    </div>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div v-if="showWorkspaceNavigation"
                                 class="hidden space-x-8 items-center sm:-my-px sm:ml-10 sm:flex">
                                <Link
                                    :href="route('workspaces.dashboard', { project: projectId, workspace: workspace.id })"
                                    class="text-cyan-600 hover:text-cyan-700 text-sm">
                                    {{ workspace.name }}
                                </Link>
                            </div>

                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="whitespace-nowrap inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{
                                                    $page.props.auth.user.first_name
                                                }}  {{ $page.props.auth.user.last_name }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profile</DropdownLink>
                                        <DropdownLink :href="route('projects.dashboard', projectId)"> Project Settings
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div v-if="showWorkspaceNavigation" class="pt-2 pb-3 space-y-1">
                        <ul>
                            <li v-for="option in navigation.workspaceOptions" :key="option.route">
                                <ResponsiveNavLink :disabled="userHasNoWorkspace" as="button"
                                                   :href="route(option.route, { project: projectId, workspace: workspace.id})"
                                                   :active="route().current(option.route)"
                                >
                                    {{ option.name }}
                                </ResponsiveNavLink>
                            </li>
                        </ul>
                    </div>
                    <div v-else-if="showProjectNavigation" class="pt-2 pb-3 space-y-1">
                        <ul>
                            <li v-for="option in navigation.projectOptions" :key="option.route">
                                <ResponsiveNavLink :disabled="userHasNoWorkspace" as="button"
                                                   :href="route(option.route)"
                                                   :active="route().current(option.route)"
                                >
                                    {{ option.name }}
                                </ResponsiveNavLink>
                            </li>
                        </ul>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('projects.dashboard', projectId)"> Project Settings
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="flex flex-grow overflow-hidden">
                <!-- Side Navigation Menu -->
                <aside v-if="showWorkspaceNavigation" class="flex-shrink-0 hidden pt-2 sm:block w-56 p-2 bg-white">
                    <ul>
                        <li v-for="option in navigation.workspaceOptions" :key="option.route" class="pb-2">
                            <NavLink :disabled="userHasNoWorkspace" as="button"
                                     :href="route(option.route, { project: projectId, workspace: workspace.id})"
                                     :active="route().current(option.route)"
                                     :iconName="option.iconName">
                                {{ option.name }}
                            </NavLink>
                        </li>
                    </ul>
                </aside>
                <aside v-else-if="showProjectNavigation" class="flex-shrink-0 hidden pt-2 sm:block w-56 p-2 bg-white">
                    <ul>
                        <li v-for="option in navigation.projectOptions" :key="option.route" class="pb-2">
                            <NavLink :disabled="userHasNoWorkspace" as="button"
                                     :href="route(option.route)"
                                     :active="route().current(option.route)"
                                     :iconName="option.iconName">
                                {{ option.name }}
                            </NavLink>
                        </li>
                    </ul>
                </aside>
                <div class="flex-grow">
                    <!-- Page Heading -->
                    <header class=" dark:bg-white bg-gray-500 shadow m-2  " v-if="$slots.header">
                        <div class="container mx-auto px-4 py-2 sm:flex sm:h-10 justify-between  items-center ">
                            <slot name="header"/>
                            <FlashMessages/>
                        </div>
                    </header>

                    <!-- Page Content -->
                    <main>
                        <slot/>
                    </main>

                </div>
            </div>
        </div>
    </div>
</template>

