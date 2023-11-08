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
            <div v-if="!users">
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


        <div class="mb-6 flex items-center">
            <input type="text" name="search" placeholder="Search…"
                   class="text-sm w-1/4 h-8 px-6 py-3 border-gray-200 ">
            <button type="button" class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-cyan-600"
                    @click="">Reset
            </button>
        </div>

        <div class="shadow overflow-x-auto p-2">
            <table class="w-full">
                <thead>
                <tr>
                    <th class="text-start flex p-2" @click="">
                        Imię
                        <!--                        <Icon name="sorting" class="block m-auto ml-2 text-gray-300"/>-->
                    </th>
                    <th class="p-2"></th>
                    <th class="p-2 flex" @click="sort('expiry_date')">
                        Wygasa dnia | za
                        <!--                        <Icon name="sorting" class="block m-auto ml-2 text-gray-300"/>-->
                    </th>
                    <th class="p-2">Pobierz</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td class="border-b p-2 w-2/3 truncate ...">

                        {{ user.first_name }}


                    </td>
                    <td class="border-b p-2 px-2 w-16">

                    </td>
                    <td class="border-b p-2 text-sm truncate ... ">
                        Hello
                    </td>
                    <td class="border-b p-2 w-24">

                        ikona


                    </td>
                </tr>
                <tr v-if="!users">
                    <td class="p-2 border-t text-red-600" colspan="4">No registries assigned.</td>
                </tr>

                </tbody>
            </table>
            <!--            <Pagination :links="registries.links" class="flex flex-wrap pt-2"></Pagination>-->
        </div>
    </AuthenticatedLayout>

</template>

<style scoped>

</style>
