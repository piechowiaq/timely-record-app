<script setup>

import {Head, Link, router, usePage} from "@inertiajs/vue3";

import {computed, ref, watch} from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {debounce} from "lodash";

const props = defineProps({
    workspace: {
        type: Object,
    },
    registry: {
        type: Object,
    },
    historicalReports: {
        type: Object,
    },
    mostCurrentReport: {
        type: Object
    },
    reports: {
        type: Array
    },
    filters: {
        type: Object
    },
})

const index = ref(
    props.filters
)


const sort = (field) => {

    if (index.value.field !== field) {
        return 'fa-solid fa-sort fa-xs ml-2'; // Default icon when the field is not the current sort field
    }
    return index.value.direction === 'asc' ? 'fa-solid fa-sort-down fa-xs ml-2' : 'fa-solid fa-sort-up fa-xs ml-2';
};

const projectId = usePage().props.auth.user.project_id;

const canCreateReport = usePage().props.permissions.canCreateReport;

watch(index, debounce(function () {
    router.get(route('workspace.registries.show', {
        project: projectId,
        workspace: props.workspace,
        registry: props.registry
    }), index.value, {
        preserveState: true,
        replace: true
    });
}, 150), {deep: true});

const validFor = computed(() => {
    if (props.registry.validity_period < 12) {
        return `${props.registry.validity_period} ${props.registry.validity_period === 1 ? 'month' : 'months'}`;
    } else {
        const years = Math.floor(props.registry.validity_period / 12);
        const months = props.registry.validity_period % 12;

        let yearText = `${years} ${years === 1 ? 'year' : 'years'}`;
        let monthText = months ? `${months} ${months === 1 ? 'month' : 'months'}` : '';

        return months ? `${yearText} and ${monthText}` : yearText;
    }
});

const isReportExpired = (expiry_date) => {
    const currentDate = new Date();
    const expiryDate = new Date(expiry_date);
    return expiryDate <= currentDate;
}

const isReportExpiringInLessThanAMonth = (expiry_date) => {
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
        return "Awaiting upload"
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

const toDateString = (dateString) => {
    return new Date(dateString).toISOString().split('T')[0];
}


</script>

<template>
    <Head title="Project"/>

    <AuthenticatedLayout :workspace="workspace">

        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">
                <Link :href="route('workspace.registries.index',  { project: projectId, workspace: workspace})">
                    Registries &lt
                </Link>
                {{ registry.name }}
            </h2>
        </template>


        <div class="md:flex md:flex-grow md:overflow-hidden  m-2">
            <div class=" md:flex-1 md:overflow-y-auto">

                <div class="p-4 bg-white">
                    <div class="flex items-center justify-between mb-2">
                        <h1 class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ registry.name }}</h1>
                        <Link v-if="canCreateReport"
                              :href="route('workspace.registry.reports.create', { project: projectId, workspace: workspace, registry: registry.id } )"
                              class="text-cyan-600 hover:text-cyan-700 text-sm">
                            Submit Report
                        </Link>
                    </div>


                    <div class="font-medium text-gray-900 dark:text-gray-100"> Description:</div>

                    <div class=" text-sm mb-2 text-gray-600 dark:text-gray-400">{{ registry.description }}</div>
                    <div class="font-medium text-gray-900 dark:text-gray-100"> Valid for: <span
                        class="text-sm text-gray-600 dark:text-gray-400">{{ validFor }}</span></div>
                </div>

                <div class="shadow overflow-x-auto p-2 mt-2 bg-white">

                    <table class="w-full text-sm text-gray-500 dark:text-gray-400 ">
                        <caption
                            class="font-bold text-xs text-gray-700 uppercase pb-2 dark:bg-gray-700 dark:text-gray-400">
                            Most
                            current
                            report
                        </caption>
                        <thead v-if="mostCurrentReport"
                               class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="text-start flex p-2">
                                Data Przeglądu

                            </th>
                            <th class="p-2"></th>
                            <th class="p-2 flex">
                                Wygasa dnia | za

                            </th>
                            <th class="p-2">Pobierz</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="!mostCurrentReport">
                            <td class="p-2 text-red-600" colspan="4">Awaiting upoload.</td>
                        </tr>
                        <tr v-else>
                            <td class=" p-2 w-2/3 truncate ">


                                <Link
                                    :href="route('workspace.registry.reports.edit', { project: projectId, workspace: workspace.id, registry: registry.id,  report: mostCurrentReport.id })"
                                    class="hover:text-cyan-700 text-sm  font-medium text-cyan-600 ">
                                    {{ mostCurrentReport.report_date }}
                                </Link>
                                <span class="text-xs text-gray-400 italic  ml-6">
                                        Created: {{ toDateString(mostCurrentReport.created_at) }} -
                                        {{
                                        mostCurrentReport?.created_by_user?.first_name
                                    }} {{ mostCurrentReport?.created_by_user?.last_name }}
                                    <span
                                        v-if="new Date(mostCurrentReport.updated_at) > new Date(mostCurrentReport.created_at)">
                                    | Updated: {{ toDateString(mostCurrentReport.updated_at) }} -
                                        {{
                                            mostCurrentReport?.updated_by_user?.first_name
                                        }} {{ mostCurrentReport?.updated_by_user?.last_name }}
                                        </span>


                                    </span>

                            </td>
                            <td class=" p-2 px-2 w-16">

                                <i class="fa-solid fa-bell text-red-500"
                                   v-if="isReportExpired(mostCurrentReport.expiry_date)"></i>
                                <i class="fa-solid fa-bell text-yellow-500"
                                   v-else-if="isReportExpiringInLessThanAMonth(mostCurrentReport.expiry_date)"></i>


                            </td>
                            <td class="p-2 text-sm font-medium truncate">
                                {{ mostCurrentReport.expiry_date }} <span class="ml-2 text-xs italic text-gray-400"> {{
                                    timeLeftUntilExpiryDate(mostCurrentReport.expiry_date)
                                }} </span>
                            </td>
                            <td class=" p-2 w-24">


                                <a v-if="mostCurrentReport.expiry_date"
                                   class="group flex justify-center items-center bg-yellow-500 hover:bg-yellow-600"
                                   :href="route('workspace.registry.reports.show', { project: projectId, workspace: workspace.id, registry: registry.id, report: mostCurrentReport.id })"
                                   target="_blank"
                                >
                                    <i class="fa-solid fa-download p-2 text-black"></i>
                                </a>


                            </td>
                        </tr>

                        </tbody>
                    </table>

                </div>
                <div class="bg-white shadow overflow-x-auto p-2 mt-2">


                    <table class="w-full bg-gray-100 text-sm text-gray-500 dark:text-gray-400 ">
                        <caption
                            class="font-bold text-xs text-gray-700 uppercase pb-2 dark:bg-gray-700 dark:text-gray-400">
                            Hisorical
                            reports
                        </caption>
                        <thead v-if="historicalReports.length !== 0"
                               class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="text-start flex items-center p-2" @click="sort('report_date')">
                                Data Przeglądu
                                <i :class="sort('report_date')"></i>

                            </th>
                            <th class="p-2"></th>
                            <th class="p-2 flex items-center" @click="sort('expiry_date')">
                                Wygasa dnia | za
                                <i :class="sort('expiry_date')"></i>

                            </th>
                            <th class="p-2">Pobierz</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="historicalReports.length === 0">
                            <td class="p-2 text-red-600" colspan="4">No other reports.</td>
                        </tr>
                        <tr v-else v-for="report of historicalReports" :key="report.id">
                            <td class="p-2 w-2/3 truncate ">
                                <Link
                                    :href="route('workspace.registry.reports.edit', { project: projectId, workspace: workspace.id, registry: registry.id,  report: report.id })"
                                    class="text-sm hover:text-cyan-700 font-medium text-cyan-600 ">
                                    {{ report.report_date }}
                                </Link>
                                <span class="text-xs text-gray-400 italic  ml-6">
                                        Created: {{ toDateString(report.created_at) }} -
                                        {{ report?.created_by_user?.first_name }} {{
                                        report?.created_by_user?.last_name
                                    }}
                                        <span v-if="new Date(report.updated_at) > new Date(report.created_at)">
                                        / Updated: {{ toDateString(report.updated_at) }} -
                                            {{
                                                report?.updated_by_user?.first_name
                                            }} {{ report?.updated_by_user?.last_name }}
                                        </span>
                                    </span>

                            </td>
                            <td class=" p-2 px-2 w-16">


                                <i class="fa-solid fa-bell text-red-200"
                                   v-if="isReportExpired(report.expiry_date)"/>

                                <i v-else-if="isReportExpiringInLessThanAMonth(report.expiry_date)"

                                   class="fa-solid fa-bell text-yellow-500"/>

                            </td>
                            <td class="p-2 text-sm truncate ... ">
                                {{ report.expiry_date }} <span
                                class="ml-2 text-xs italic text-gray-400"> {{
                                    timeLeftUntilExpiryDate(report.expiry_date)
                                }} </span>
                            </td>

                            <td class=" p-2 w-24">


                                <a v-if="report.expiry_date"
                                   class="group flex justify-center items-center bg-yellow-200 hover:bg-yellow-400"
                                   :href="route('workspace.registry.reports.show', { project: projectId, workspace: workspace.id, registry: registry.id, report: mostCurrentReport.id })"
                                   target="_blank"
                                >
                                    <i class="fa-solid fa-download p-2 text-gray-400"></i>
                                </a>


                            </td>
                        </tr>

                        </tbody>
                    </table>

                </div>

            </div>
        </div>

    </AuthenticatedLayout>

</template>

<style scoped>

</style>
