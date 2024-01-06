<script setup>

import {Head, Link, router, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref, watch} from "vue";
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";

const props = defineProps({
    paginatedUsers: {
        type: Object,
    },
    filters: {
        type: Object,
    },
    users: {
        type: Object,
    },
});

const projectId = usePage().props.auth.user.project_id;

const index = ref({
    search: props.filters.search,
});

watch(index.value, debounce(() => {
    router.get(route('users.index', {project: projectId}), index.value, {
        preserveState: true,
        replace: true
    });
}, 300));

const sort = (field) => {
    index.value.field = field;
    index.value.direction = index.value.direction === 'asc' ? 'desc' : 'asc';
}

const resetSearch = () => {
    index.value.search = '';
}

const getSortIconClass = (field) => {
    if (index.value.field !== field) {
        return 'fa-solid fa-sort fa-xs ml-2'; // Default icon when the field is not the current sort field
    }
    return index.value.direction === 'asc' ? 'fa-solid fa-sort-down fa-xs ml-2' : 'fa-solid fa-sort-up fa-xs ml-2';
};


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
                        <input v-model="index.search" type="text" name="search" placeholder="Search…"
                               class="text-sm h-8 px-6 py-3 border-gray-200 ">
                        <button type="button"
                                class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-cyan-600"
                                @click="resetSearch">Reset
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
                            <th scope="col" class="px-6 py-3" @click="sort('last_name')">
                                Name
                                <i :class="getSortIconClass('last_name')"></i>
                            </th>

                            <th scope="col" class="px-6 py-3" @click="sort('role')">
                                Role
                                <i :class="getSortIconClass('role')"></i>
                            </th>
                            <th scope="col" class="px-6 py-3" @click="sort('email')">
                                Email
                                <i :class="getSortIconClass('email')"></i>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center" @click="sort('email_verified_at')">
                                Registered
                                <i :class="getSortIconClass('email_verified_at')"
                                ></i>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(user, index) in paginatedUsers.data" :key="user.id"
                            :class="{'bg-white dark:bg-gray-800': true, 'border-b dark:border-gray-700': index !== paginatedUsers.data.length - 1}">

                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <Link :href="route('users.edit', [ projectId, user.id])"
                                      class="text-cyan-600 hover:text-cyan-700">
                                    {{ user.first_name }} {{ user.last_name }}
                                </Link>
                            </th>

                            <td class="px-6 py-4">
                                {{ user.role }}
                            </td>
                            <td class="px-6 py-4">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <i v-if="user.email_verified"
                                   class="fa-regular fa-circle-check text-green-600"></i>
                                <i v-else class="fa-regular fa-circle-xmark text-red-600"></i>

                            </td>

                        </tr>

                        </tbody>
                    </table>
                </div>

                <Pagination :links="paginatedUsers.meta.links"
                            class="flex items-center justify-end py-2"></Pagination>
            </div>
        </div>

    </AuthenticatedLayout>

</template>

<style scoped>

</style>
