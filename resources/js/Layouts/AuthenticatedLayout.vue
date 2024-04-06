<script setup>
import { onMounted, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useNavigationStore } from "@/Stores/NavigationStore.js";
import FlashMessages from "@/Components/FlashMessages.vue";

import { useDark, useToggle } from "@vueuse/core";

const isDark = useDark();
const toggleDark = useToggle(isDark);

const props = defineProps(["workspace"]);

const showingNavigationDropdown = ref(false);

const projectId = usePage().props.projectId;
const projectName = usePage().props.project?.name ?? "Project Dashboard";

const navigation = useNavigationStore();
const user = usePage().props.auth.user;

const superAdmin = user.project_id === null;
const impersonate = usePage().props.impersonate;

const canManageProject =
    usePage().props.permissions.canManageProject || superAdmin;
const canViewProject = usePage().props.permissions.canViewProject || superAdmin;

onMounted(() => {
    navigation.updateCanManageProject(canManageProject);
});

const userHasNoWorkspace =
    Boolean(!user.workspaces.length) && Boolean(!superAdmin);
const page = usePage().props.route;

const showWorkspaceNavigation = Boolean(props.workspace);
const showProjectNavigation =
    (Boolean(props.workspace) && page.endsWith("/edit")) ||
    !Boolean(props.workspace);
</script>

<template>
    <div>
        <div v-if="impersonate" class="relative bg-red-400">
            <div class="max-w-screen-xl px-3 sm:px-6 lg:px-8 mx-auto py-2">
                <div class="pr-16 sm:text-center sm:px-16">
                    <p class="font-medium text-white">
                        <span class="md:hidden">
                            You are impersonating {{ user.first_name }}
                            {{ user.last_name }}
                        </span>
                        <span class="hidden md:inline">
                            You are impersonating {{ user.first_name }}
                            {{ user.last_name }}
                        </span>
                        <span class="block sm:ml-2 sm:inline-block">
                            <Link
                                :href="route('users.leave-impersonation')"
                                class="text-white font-bold underline"
                            >
                                Leave Impersonation &rarr;
                            </Link>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="min-h-screen flex-col dark:bg-gray-900 flex bg-gray-100">
            <nav
                class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700"
            >
                <!-- Primary Navigation Menu -->
                <div class="px-4">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div
                                class="shrink-0 flex items-center justify-center w-56 -ml-4"
                            >
                                <Link :href="route('projects.dashboard')">
                                    <div
                                        class="flex justify-start items-center"
                                    >
                                        <ApplicationLogo class="w-10 h-10" />

                                        <p
                                            class="ml-2 font-bold whitespace-nowrap tracking-widest text-gray-600"
                                        >
                                            <span class="text-cyan-600"
                                                >TIMELY</span
                                            >
                                            RECORD
                                        </p>
                                    </div>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                v-if="showWorkspaceNavigation"
                                class="hidden items-center sm:-my-px sm:ml-10 sm:flex space-x-8"
                            >
                                <Link
                                    :href="
                                        route(
                                            'workspaces.dashboard',
                                            workspace.id,
                                        )
                                    "
                                    class="hover:text-cyan-700 text-cyan-600 text-sm"
                                >
                                    {{ workspace.name }}
                                </Link>
                            </div>
                            <div
                                v-else-if="superAdmin"
                                class="hidden space-x-8 items-center sm:-my-px sm:ml-10 sm:flex"
                            >
                                <p
                                    class="text-red-400 uppercase text-xs font-semibold"
                                >
                                    Admin Panel
                                </p>
                            </div>
                            <div
                                v-else
                                class="hidden space-x-8 items-center sm:-my-px sm:ml-10 sm:flex"
                            >
                                <p
                                    class="text-amber-400 uppercase text-xs font-semibold"
                                >
                                    {{ projectName }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <button @click="toggleDark()">
                                <svg
                                    v-if="isDark"
                                    class="w-6 h-6 text-amber-400"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="w-6 h-6 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>

                            <div class="hidden sm:flex sm:items-center">
                                <!-- Settings Dropdown -->
                                <div class="ml-3 relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <span
                                                class="inline-flex rounded-md"
                                            >
                                                <button
                                                    class="whitespace-nowrap inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                                    type="button"
                                                >
                                                    {{
                                                        $page.props.auth.user
                                                            .first_name
                                                    }}
                                                    {{
                                                        $page.props.auth.user
                                                            .last_name
                                                    }}

                                                    <svg
                                                        class="ml-2 -mr-0.5 h-4 w-4"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                    >
                                                        <path
                                                            clip-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            fill-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <DropdownLink
                                                :href="route('profile.edit')"
                                            >
                                                Profile
                                            </DropdownLink>
                                            <DropdownLink
                                                v-if="
                                                    canViewProject || superAdmin
                                                "
                                                :href="
                                                    route('projects.dashboard')
                                                "
                                            >
                                                Project Dashboard
                                            </DropdownLink>
                                            <DropdownLink
                                                :href="route('logout')"
                                                as="button"
                                                method="post"
                                            >
                                                Log Out
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="-mr-2 flex items-center sm:hidden">
                                <button
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                                    @click="
                                        showingNavigationDropdown =
                                            !showingNavigationDropdown
                                    "
                                >
                                    <svg
                                        class="h-6 w-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            :class="{
                                                hidden: showingNavigationDropdown,
                                                'inline-flex':
                                                    !showingNavigationDropdown,
                                            }"
                                            d="M4 6h16M4 12h16M4 18h16"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                        />
                                        <path
                                            :class="{
                                                hidden: !showingNavigationDropdown,
                                                'inline-flex':
                                                    showingNavigationDropdown,
                                            }"
                                            d="M6 18L18 6M6 6l12 12"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div
                        v-if="showWorkspaceNavigation"
                        class="pt-2 pb-3 space-y-1"
                    >
                        <ul>
                            <li
                                v-for="option in navigation.workspaceOptions"
                                :key="option.route"
                            >
                                <ResponsiveNavLink
                                    :active="route().current(option.route)"
                                    :disabled="userHasNoWorkspace"
                                    :href="route(option.route, workspace.id)"
                                    as="button"
                                >
                                    {{ option.name }}
                                </ResponsiveNavLink>
                            </li>
                        </ul>
                    </div>

                    <div
                        v-else-if="showProjectNavigation"
                        class="pt-2 pb-3 space-y-1"
                    >
                        <ul>
                            <li
                                v-for="option in navigation.projectOptions"
                                :key="option.route"
                            >
                                <ResponsiveNavLink
                                    :active="route().current(option.route)"
                                    :disabled="userHasNoWorkspace"
                                    :href="route(option.route)"
                                    as="button"
                                >
                                    {{ option.name }}
                                </ResponsiveNavLink>
                            </li>
                        </ul>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600"
                    >
                        <div class="px-4">
                            <div
                                class="font-medium text-base text-gray-800 dark:text-gray-200"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('projects.dashboard')"
                            >
                                Project Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                as="button"
                                method="post"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="flex flex-grow overflow-auto">
                <!-- Side Navigation Menu -->
                <aside
                    v-if="showWorkspaceNavigation"
                    class="dark:bg-gray-800 flex-shrink-0 hidden pt-2 sm:block w-56 p-2 bg-white"
                >
                    <ul>
                        <li
                            v-for="option in navigation.workspaceOptions"
                            :key="option.route"
                            class="pb-2"
                        >
                            <NavLink
                                :active="route().current(option.route)"
                                :disabled="userHasNoWorkspace"
                                :href="route(option.route, workspace.id)"
                                :iconName="option.iconName"
                                as="button"
                            >
                                {{ option.name }}
                            </NavLink>
                        </li>
                    </ul>
                </aside>
                <aside
                    v-else-if="showProjectNavigation"
                    class="dark:bg-gray-800 flex-shrink-0 hidden pt-2 sm:block w-56 p-2 bg-white"
                >
                    <ul>
                        <li
                            v-for="option in navigation.projectOptions"
                            :key="option.route"
                            class="pb-2"
                        >
                            <NavLink
                                :active="route().current(option.route)"
                                :disabled="userHasNoWorkspace"
                                :href="route(option.route)"
                                :iconName="option.iconName"
                                as="button"
                            >
                                {{ option.name }}
                            </NavLink>
                        </li>
                    </ul>
                </aside>
                <div class="flex-grow">
                    <!-- Page Heading -->
                    <header
                        v-if="$slots.header"
                        class="bg-gray-500 shadow m-2 text-white leading-tight"
                    >
                        <div
                            class="justify-between sm:h-10 container items-center px-4 py-2 sm:flex mx-auto"
                        >
                            <slot name="header" />
                            <FlashMessages />
                        </div>
                    </header>

                    <!-- Page Content -->
                    <main class="">
                        <slot />
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
