<script setup>

import {Head, Link, router, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref, watch} from "vue";
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";

const props = defineProps({
    workspace: {
        type: Object,
    },
    paginatedRegistries: {
        type: Object,
    },
    filters: {
        type: Object
    },
})


const projectId = usePage().props.auth.user.project_id;

const index = ref({
    search: props.filters.search,
});

const canCreateReport = usePage().props.permissions.canCreateReport;

watch(index.value, debounce(() => {
    router.get(route('workspace.registries.index', {project: projectId, workspace: props.workspace.id}), index.value, {
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

const isRegistryExpired = (expiry_date) => {
    const today = new Date();
    const expiryDate = new Date(expiry_date);
    return expiryDate <= today;
}

const isRegistryExpiringInLessThanAMonth = (expiry_date) => {
    const today = new Date();
    const expiryDate = new Date(expiry_date);
    const diffTime = expiryDate - today;
    const daysUntilExpiry = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return daysUntilExpiry > 0 && daysUntilExpiry < 30;
}

const timeLeftUntilExpiryDate = (expiry_date) => {
    const today = new Date();
    const expiryDate = new Date(!expiry_date ? today : expiry_date);
    const diffTime = expiryDate - today;
    let days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (!expiry_date) {
        return "Awaiting upload."
    } else if (days <= 0) {
        return "Expired";
    } else if (days < 30) {
        return `${days} day(s)`;
    } else if (days < 365) {
        const months = Math.floor(days / 30);
        return `${months} month(s)`;
    } else {
        const years = Math.floor(days / 365);
        const remainingMonths = Math.floor((days % 365) / 30);
        return `${years} year(s) ${remainingMonths ? `${remainingMonths} month(s)` : ''}`.trim();
    }
};


</script>

<template>
    <Head title="Project"/>

    <AuthenticatedLayout :workspace="workspace">
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Registries</h2>
        </template>
        <div class="px-2 pb-2 ">
            <div class="p-6 shadow overflow-x-auto bg-white">
                <div class="flex items-center justify-between">
                    <div class="mb-2 flex items-center">
                        <input v-model="index.search" type="text" name="search" placeholder="Search…"
                               class="text-sm h-8 px-6 py-2 border-gray-200 ">
                        <button type="button"
                                class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-cyan-600"
                                @click="resetSearch">Reset
                        </button>

                    </div>


                    <Link v-if="paginatedRegistries.data.length > 0 && canCreateReport"
                          :href="route('workspace.registry.reports.create', {project: projectId, workspace: workspace.id})"
                          class="text-cyan-600 hover:text-cyan-700 text-sm">
                        Upload Report
                    </Link>
                </div>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-2" @click="sort('name')">
                                Name
                                <i :class="getSortIconClass('name')"></i>
                            </th>

                            <th scope="col" class="px-6 py-2">

                            </th>
                            <th scope="col" class="px-6 py-2" @click="sort('expiry_date')">
                                Wygasa dnia | za
                                <i :class="getSortIconClass('expiry_date')"></i>
                            </th>
                            <th scope="col" class="px-6 py-2 text-center">
                                Pobierz
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="registry of paginatedRegistries.data" :key="registry.id"
                            :class="{'bg-white dark:bg-gray-800': true, 'border-b dark:border-gray-700': index !== paginatedRegistries.data.length - 1}">

                            <th scope="row"
                                class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                <span
                    class="text-cyan-600 hover:text-cyan-700">
                 <Link
                     :href="route('workspace.registries.show', { project: projectId, workspace: workspace, registry: registry.registry_id})">
                {{ registry.name }}
              </Link>
                </span>
                            </th>

                            <td class="px-6 py-2">
                                <i class="fa-solid fa-bell text-red-500"
                                   v-if="isRegistryExpired(registry.expiry_date)"></i>
                                <i class="fa-solid fa-bell text-yellow-500"
                                   v-else-if="isRegistryExpiringInLessThanAMonth(registry.expiry_date)"></i>

                            </td>
                            <td class="px-6 py-2">
                                {{ registry.expiry_date }} <span
                                class="ml-2 text-xs italic text-gray-400"> {{
                                    timeLeftUntilExpiryDate(registry.expiry_date)
                                }} </span>
                            </td>
                            <td class="px-6 py-2 text-center">

                                <i v-if="registry.expiry_date"
                                   class="fa-regular fa-circle-check text-green-600"></i>
                                <i v-else class="fa-regular fa-circle-xmark text-red-600"></i>
                            </td>
                        </tr>
                        <tr v-if="paginatedRegistries.data.length === 0">
                            <td class="p-2 border-t text-red-600" colspan="4">No registries assign to this workspace.
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <Pagination :links="paginatedRegistries.links" class="flex items-center justify-end py-2"></Pagination>
            </div>
        </div>

    </AuthenticatedLayout>

</template>

<style scoped>

</style>
