<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";

defineProps([
    "workspaces",
    "projectsCount",
    "workspacesCount",
    "usersCount",
    "genericRegistriesCount",
    "customRegistriesCount",
]);

const canManageProject = usePage().props.auth.canManageProject;

const projectId = usePage().props.projectId;

const isSuperAdmin = usePage()
    .props.auth.user.roles.map((role) => role.name)
    .includes("super-admin");

const getBadgeColor = (workspace) => {
    if (workspace.registryMetrics === 100)
        return "text-green-500 fa-solid fa-circle-check";

    if (workspace.registryMetrics > 90)
        return "text-green-300 fa-regular fa-circle-check ";
    if (workspace.registryMetrics >= 80)
        return "text-yellow-300 fa-regular fa-circle-check";
    return "text-red-300 fa-regular fa-circle-xmark";
};
</script>

<template>
    <Head title="Project" />

    <AuthenticatedLayout>
        <template #header>
            <h2 v-if="isSuperAdmin">Admin Dashboard</h2>
            <h2 v-else>Project Dashboard</h2>
        </template>

        <!-- component -->
        <div class="m-2 flex items-center text-gray-600 dark:text-gray-400">
            <div class="w-full">
                <div class="grid grid-cols-12 gap-2">
                    <div
                        v-if="isSuperAdmin"
                        class="col-span-12 md:col-span-4 xl:col-span-3"
                    >
                        <div
                            class="flex flex-row bg-white p-2 shadow-sm dark:bg-gray-800"
                        >
                            <div
                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center bg-gray-500 text-gray-100"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    class="h-6 w-6 fill-gray-50 stroke-white stroke-1"
                                >
                                    <path
                                        d="M12 22c-1.02 0-1.38-.158-2.101-.473C7.239 20.365 3 17.63 3 11.991v-1.574c0-3.198 0-4.797.378-5.334C3.755 4.545 5.258 4.03 8.265 3l.573-.196C10.405 2.268 11.188 2 12 2"
                                    />
                                    <path
                                        d="M12 22c1.02 0 1.38-.158 2.101-.473c2.66-1.162 6.9-3.898 6.9-9.536v-1.574c0-3.198 0-4.797-.378-5.334c-.378-.538-1.881-1.053-4.888-2.082l-.573-.196C13.595 2.268 12.812 2 12 2"
                                        opacity=".5"
                                    />
                                </svg>
                            </div>
                            <div class="ml-4 flex flex-grow flex-col">
                                <div class=" ">Projects</div>
                                <div class="text-sm font-bold">
                                    {{ projectsCount }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-span-12"
                        :class="{
                            'md:col-span-6 xl:col-span-4': !isSuperAdmin,
                            'md:col-span-4 xl:col-span-3': isSuperAdmin,
                        }"
                    >
                        <div
                            class="flex flex-row bg-white p-2 shadow-sm dark:bg-gray-800"
                        >
                            <div
                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center bg-gray-500 text-gray-100"
                            >
                                <i :class="'fa-solid fa-building-shield'"></i>
                            </div>
                            <div class="ml-4 flex flex-grow flex-col">
                                <div class=" ">Workspaces</div>
                                <div class="text-sm font-bold">
                                    {{ workspacesCount }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-span-12"
                        :class="{
                            'md:col-span-6 xl:col-span-4': !isSuperAdmin,
                            'md:col-span-4 xl:col-span-3': isSuperAdmin,
                        }"
                    >
                        <div
                            class="flex flex-row bg-white p-2 shadow-sm dark:bg-gray-800"
                        >
                            <div
                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center bg-gray-500 text-gray-100"
                            >
                                <i :class="'fa-solid fa-users'"></i>
                            </div>
                            <div class="ml-4 flex flex-grow flex-col">
                                <div class="">Users</div>
                                <div class="text-sm font-bold">
                                    {{ usersCount }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-span-12"
                        :class="{
                            'xl:col-span-4': !isSuperAdmin,
                            'xl:col-span-3': isSuperAdmin,
                        }"
                    >
                        <div
                            class="flex flex-row bg-white p-2 shadow-sm dark:bg-gray-800"
                        >
                            <div
                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center bg-gray-500 text-gray-100"
                            >
                                <i :class="'fa-solid fa-box-archive'"></i>
                            </div>
                            <div class="ml-4 flex flex-grow flex-col">
                                <div class="">Registries</div>
                                <div
                                    class="flex justify-between text-sm font-bold"
                                >
                                    <p>
                                        <span class="font-light">Generic</span>
                                        {{ genericRegistriesCount }}
                                    </p>
                                    <p>
                                        <span class="font-light">Custom</span>
                                        {{ customRegistriesCount }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="!workspaces.data.length">
            <section class="flex-grow dark:bg-gray-800">
                <p class="mb-4 italic">
                    No workspaces associated with this project or with your
                    credentials.
                </p>

                <Link
                    v-if="canManageProject"
                    :href="route('workspaces.create', projectId)"
                    class=""
                >
                    Create Workspace
                </Link>
            </section>
        </div>
        <div
            v-else
            class="mx-2 grid grid-cols-1 gap-2 text-gray-900 dark:text-gray-100 xl:grid-cols-2"
        >
            <section
                v-for="workspace in workspaces.data"
                :key="workspace.id"
                class="flex flex-grow grid-cols-12 justify-between gap-2 bg-white dark:bg-gray-800"
            >
                <header class="col-span-8 p-2">
                    <Link :href="route('workspaces.dashboard', workspace.id)">
                        <h2 class="font-medium">
                            {{ workspace.name }}
                        </h2>
                    </Link>
                    <p
                        v-if="workspace.location"
                        class="mb-4 text-xs text-gray-600 dark:text-gray-400"
                    >
                        {{ workspace.location }}
                    </p>
                </header>

                <section
                    class="col-span-4 grid w-1/4 grid-rows-2 p-2 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-400"
                >
                    <div
                        class="flex justify-between border-b border-gray-200 py-2"
                    >
                        <header class="flex items-center">
                            <i
                                class="hidden sm:block"
                                :class="`${getBadgeColor(workspace)}`"
                            ></i>
                            <span class="ml-2">Registries</span>
                        </header>
                        <p class="ml-2">{{ workspace.registryMetrics }}%</p>
                    </div>

                    <div class="flex justify-between py-2">
                        <header class="flex items-center">
                            <i
                                class="fa-regular fa-circle-xmark hidden sm:block"
                            ></i>
                            <span class="ml-2">Trainings</span>
                        </header>
                        <p class="ml-2 font-medium">0%</p>
                    </div>
                </section>
            </section>
        </div>
        <Pagination
            :links="workspaces.meta.links"
            class="m-2 flex items-center justify-end"
        ></Pagination>
    </AuthenticatedLayout>
</template>
