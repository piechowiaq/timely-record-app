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

        <div class="px-2 pb-2 ">
            <div class="p-6 shadow overflow-x-auto bg-white">
                <div class="flex items-center justify-between">
                    <div class="mb-2 flex items-center">
                        <input type="text" name="search" placeholder="Search…"
                               class="text-sm h-8 px-6 py-3 border-gray-200 ">
                        <button type="button" class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-cyan-600"
                                @click="">Reset
                        </button>
                    </div>
                    <Link :href="route('users.create', projectId)" class="text-cyan-600 hover:text-cyan-700 text-sm">
                        Create
                        User
                    </Link>
                </div>


                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Imię i nazwisko
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Role
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="user in users" :key="user.id"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ user.first_name }} {{ user.last_name }}
                            </th>
                            <td class="px-6 py-4">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4">
                                Manager
                            </td>

                        </tr>

                        </tbody>
                    </table>
                </div>


                <!--            <Pagination :links="registries.links" class="flex flex-wrap pt-2"></Pagination>-->
            </div>
        </div>


    </AuthenticatedLayout>

</template>

<style scoped>

</style>
