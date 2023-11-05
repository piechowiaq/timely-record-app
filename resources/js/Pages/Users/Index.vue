<script setup>

import {Head, Link, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({
    users: {
        type: Object,
    }
});

const projectId = usePage().props.auth.user.project_id;

</script>

<template>
    <Head title="Project"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Users</h2>
        </template>

        <div class="px-2 pb-2">

            <div v-if="!users.length">
                <section
                    class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow flex-grow">

                    <p class="italic text-red-400 text-xs mb-4">
                        No other
                        users
                        associated with this project.
                    </p>

                    <Link :href="route('users.create', projectId )"
                          class="text-cyan-600 hover:text-cyan-700 text-sm">
                        Create User
                    </Link>
                </section>
            </div>
            <div v-else class="grid md:grid-cols-2 gap-2 grid-cols-1">
                <section v-for="user in users" :key="user.id"
                         class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow flex-grow justify-between flex">
                    <header>
                        <Link :href="route('users.edit', { project: projectId, user: user.id} )">
                            <h2 class="text-lg font-medium hover:text-cyan-700 text-gray-900 dark:text-gray-100">
                                {{ user.first_name }}
                            </h2>
                        </Link>
                    </header>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<style scoped>

</style>
