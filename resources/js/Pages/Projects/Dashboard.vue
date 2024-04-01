<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from "@/Components/Pagination.vue";

defineProps(['workspaces', 'projectsCount', 'workspacesCount', 'usersCount', 'genericRegistriesCount', 'customRegistriesCount']);

const canManageProject = usePage().props.auth.canManageProject;

const projectId = usePage().props.projectId;

const isSuperAdmin = usePage().props.auth.user.roles.map(role => role.name).includes('super-admin');


const getWorkspaceBorderColor = (workspace) => {
    if (workspace.registryMetrics === 100) return 'border-green-500';
    if (workspace.registryMetrics > 90) return 'border-green-300';
    if (workspace.registryMetrics >= 80) return 'border-yellow-300';
    return 'border-red-300';
};

</script>

<template>
    <Head title="Project"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 v-if="isSuperAdmin" class="text-white dark:text-gray-700 leading-tight">Admin Dashboard</h2>
            <h2 v-else class="text-white dark:text-gray-700 leading-tight">Project Dashboard</h2>
        </template>

        <!-- component -->
        <div class="flex items-center text-gray-800 m-2">
            <div class="w-full">
                <div class="grid gap-2" :class="{'grid-cols-9': !isSuperAdmin, 'grid-cols-12': isSuperAdmin}">
                    <div v-if="isSuperAdmin" class="col-span-12 sm:col-span-6 md:col-span-3">
                        <div class="flex flex-row bg-white shadow-sm p-2">
                            <div
                                class="flex items-center justify-center flex-shrink-0 h-12 w-12 bg-gray-300 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                     class="w-6 h-6 stroke-white fill-gray-50 stroke-1">
                                    <path
                                        d="M12 22c-1.02 0-1.38-.158-2.101-.473C7.239 20.365 3 17.63 3 11.991v-1.574c0-3.198 0-4.797.378-5.334C3.755 4.545 5.258 4.03 8.265 3l.573-.196C10.405 2.268 11.188 2 12 2"/>
                                    <path
                                        d="M12 22c1.02 0 1.38-.158 2.101-.473c2.66-1.162 6.9-3.898 6.9-9.536v-1.574c0-3.198 0-4.797-.378-5.334c-.378-.538-1.881-1.053-4.888-2.082l-.573-.196C13.595 2.268 12.812 2 12 2"
                                        opacity=".5"/>
                                </svg>
                            </div>
                            <div class="flex flex-col flex-grow ml-4">
                                <div class="text-sm text-gray-500">Projects</div>
                                <div class="font-bold text-lg">{{ projectsCount }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 md:col-span-3">
                        <div class="flex flex-row bg-white shadow-sm p-2">
                            <div
                                class="flex items-center justify-center flex-shrink-0 h-12 w-12 bg-gray-300 text-white">
                                <i :class="'fa-solid fa-building-shield'"></i>
                            </div>
                            <div class="flex flex-col flex-grow ml-4">
                                <div class="text-sm text-gray-500">Workspaces</div>
                                <div class="font-bold text-lg">{{ workspacesCount }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 md:col-span-3">
                        <div class="flex flex-row bg-white shadow-sm p-2">
                            <div
                                class="flex items-center justify-center flex-shrink-0 h-12 w-12 bg-gray-300 text-white">
                                <i :class="'fa-solid fa-users'"></i>
                            </div>
                            <div class="flex flex-col flex-grow ml-4">
                                <div class="text-sm text-gray-500">Users</div>
                                <div class="font-bold text-lg">{{ usersCount }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 md:col-span-3">
                        <div class="flex flex-row bg-white shadow-sm p-2">
                            <div
                                class="flex items-center justify-center flex-shrink-0 h-12 w-12 bg-gray-300 text-white">
                                <i :class="'fa-solid fa-box-archive'"></i>
                            </div>
                            <div class="flex flex-col flex-grow ml-4">
                                <div class="text-sm text-gray-500">Registries</div>
                                <div class="font-bold text-lg flex justify-between">
                                    <p><span class="text-xs font-light text-gray-600">Generic</span>
                                        {{ genericRegistriesCount }}</p>
                                    <p><span class="text-xs font-light text-gray-600">Custom</span>
                                        {{ customRegistriesCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="px-2 p-2 m-2 bg-white">
            <div v-if="!workspaces.data.length">
                <section
                    class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow flex-grow">

                    <p class="italic text-red-400 text-xs mb-4">
                        No
                        workspaces
                        associated with this project or with your credentials.
                    </p>


                    <Link v-if="canManageProject"
                          :href="route('workspaces.create', projectId )"
                          class="text-cyan-600 hover:text-cyan-700 text-sm">
                        Create Workspace
                    </Link>
                </section>
            </div>
            <div v-else class="grid md:grid-cols-2 gap-2 grid-cols-1">
                <section v-for="workspace in workspaces.data" :key="workspace.id"
                         class="p-4 sm:p-8 bg-gray-50 dark:bg-gray-800 flex-grow justify-between flex">
                    <header>
                        <Link :href="route('workspaces.dashboard', workspace.id )">
                            <h2 class="text-lg font-medium hover:text-cyan-700 text-gray-900 dark:text-gray-100">
                                {{ workspace.name }}
                            </h2>
                        </Link>
                        <p v-if="workspace.location" class="text-sm mb-4 text-gray-400">{{ workspace.location }}</p>
                    </header>
                    <section class="grid grid-cols-2 gap-4 text-center">
                        <div :class="`border-2 p-2 flex flex-col ${getWorkspaceBorderColor(workspace)}`">
                            <header class="text-gray-600 text-sm">Registries</header>
                            <p class="font-medium text-2xl text-gray-500 mt-auto">{{
                                    workspace.registryMetrics
                                }}%</p>

                        </div>
                        <div class="border-2 p-2 bg-gray-50 border-red-50 flex flex-col">
                            <header class="text-gray-200 text-sm">Trainings</header>
                            <p class="font-medium text-2xl text-gray-200  mt-auto">0%</p>
                        </div>
                    </section>


                </section>
            </div>
            <Pagination :links="workspaces.meta.links" class="flex items-center justify-end py-2 "></Pagination>
        </div>
    </AuthenticatedLayout>
</template>

