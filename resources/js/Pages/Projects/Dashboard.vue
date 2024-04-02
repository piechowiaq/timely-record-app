<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from "@/Components/Pagination.vue";

defineProps(['workspaces', 'projectsCount', 'workspacesCount', 'usersCount', 'genericRegistriesCount', 'customRegistriesCount']);

const canManageProject = usePage().props.auth.canManageProject;

const projectId = usePage().props.projectId;

const isSuperAdmin = usePage().props.auth.user.roles.map(role => role.name).includes('super-admin');


const getBadgeColor = (workspace) => {
    if (workspace.registryMetrics === 100) return 'text-green-500 fa-solid fa-circle-check';

    if (workspace.registryMetrics > 90) return 'text-green-300 fa-regular fa-circle-check ';
    if (workspace.registryMetrics >= 80) return 'text-yellow-300 fa-regular fa-circle-check';
    return 'text-red-300 fa-regular fa-circle-xmark';

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
                <div class="grid gap-2 grid-cols-12">
                    <div v-if="isSuperAdmin" class="col-span-12 md:col-span-4 xl:col-span-3">
                        <div class="dark:bg-gray-700  dark:text-gray-300 flex flex-row bg-white shadow-sm p-2">
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
                            <div class="flex  flex-col flex-grow ml-4">
                                <div class="text-sm dark:text-gray-300 text-gray-500">Projects</div>
                                <div class="font-bold text-lg">{{ projectsCount }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 "
                         :class="{'md:col-span-6 xl:col-span-4': !isSuperAdmin, 'md:col-span-4 xl:col-span-3': isSuperAdmin}">
                        <div class="dark:bg-gray-700  dark:text-gray-300 flex flex-row bg-white shadow-sm p-2">
                            <div
                                class="flex items-center justify-center flex-shrink-0 h-12 w-12 bg-gray-300 text-white">
                                <i :class="'fa-solid fa-building-shield'"></i>
                            </div>
                            <div class="flex flex-col flex-grow ml-4">
                                <div class="text-sm dark:text-gray-300 text-gray-500">Workspaces</div>
                                <div class="font-bold text-lg">{{ workspacesCount }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12"
                         :class="{'md:col-span-6 xl:col-span-4': !isSuperAdmin, 'md:col-span-4 xl:col-span-3': isSuperAdmin}">
                        <div class="dark:bg-gray-700  dark:text-gray-300 flex flex-row bg-white shadow-sm p-2">
                            <div
                                class="flex items-center justify-center flex-shrink-0 h-12 w-12 bg-gray-300 text-white">
                                <i :class="'fa-solid fa-users'"></i>
                            </div>
                            <div class="flex flex-col flex-grow ml-4">
                                <div class="text-sm dark:text-gray-300 text-gray-500">Users</div>
                                <div class="font-bold text-lg">{{ usersCount }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12" :class="{'xl:col-span-4': !isSuperAdmin, 'xl:col-span-3': isSuperAdmin}">
                        <div class="dark:bg-gray-700  dark:text-gray-300 flex flex-row bg-white shadow-sm p-2">
                            <div
                                class="flex items-center justify-center flex-shrink-0 h-12 w-12 bg-gray-300 text-white">
                                <i :class="'fa-solid fa-box-archive'"></i>
                            </div>
                            <div class="flex flex-col flex-grow ml-4">
                                <div class="text-sm dark:text-gray-300 text-gray-500">Registries</div>
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


        <div v-if="!workspaces.data.length">
            <section
                class=" p-2 bg-gray-50 dark:bg-gray-800 flex-grow">

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
        <div v-else class="grid xl:grid-cols-2 gap-2 grid-cols-1 mx-2">
            <section v-for="workspace in workspaces.data" :key="workspace.id"
                     class=" border bg-gray-50 dark:bg-gray-800 flex-grow grid grid-cols-12 gap-2 justify-between flex">
                <header class="p-2 col-span-8 ">
                    <Link :href="route('workspaces.dashboard', workspace.id )">
                        <h2 class="text-lg font-medium hover:text-cyan-700  text-gray-900 dark:text-gray-100">
                            {{ workspace.name }}
                        </h2>
                    </Link>
                    <p v-if="workspace.location" class="text-sm mb-4 text-gray-400">{{ workspace.location }}</p>
                </header>

                <section
                    class="dark:bg-gray-700  dark:text-gray-300 p-2 grid grid-rows-2 bg-white col-span-4">

                    <div class=" py-2 justify-between border-b flex border-gray-200"
                    >
                        <header class="items-center text-gray-600 flex text-sm">
                            <i class="hidden sm:block" :class="`${getBadgeColor(workspace)}`"></i>
                            <span class="ml-2">Registries</span>
                        </header>
                        <p class="font-medium  text-sm text-gray-500">{{
                                workspace.registryMetrics
                            }}%</p>

                    </div>

                    <div class="py-2 justify-between flex "
                    >
                        <header class="items-center text-gray-300 flex text-sm">
                            <i class="hidden sm:block text-gray-300 fa-regular fa-circle-xmark"></i>
                            <span class="ml-2">Trainings</span>
                        </header>
                        <p class="font-medium  text-sm text-gray-300">0%</p>

                    </div>
                </section>


            </section>
        </div>
        <Pagination :links="workspaces.meta.links"
                    class="flex items-center justify-end m-2 "
        ></Pagination>

    </AuthenticatedLayout>
</template>

