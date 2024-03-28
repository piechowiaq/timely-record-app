<script setup>
import {Head, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RegistriesCard from "@/Pages/Workspaces/Partials/RegistriesCard.vue";
import TrainingsCard from "@/Pages/Workspaces/Partials/TrainingsCard.vue";

const props = defineProps(['workspaceId',
    'nonCompliantRegistries',
    'expiringRegistries',
    'upToDateRegistriesCount',
    'registriesCount',
    'registryMetrics',
    'workspace'
]);

const page = usePage();

const hasRegistries = props.registriesCount > 0;

const projectId = usePage().props.projectId;

</script>

<template>
    <Head title="Workspace"/>

    <AuthenticatedLayout :workspace="workspace">
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Dashboard</h2>
        </template>


        <div class="px-2 pb-2 sm:flex sm:space-x-2">

            <RegistriesCard :workspaceId="workspaceId"
                            :upToDateRegistriesCount="upToDateRegistriesCount"
                            :nonCompliantRegistriesCount="registriesCount-upToDateRegistriesCount"
                            :hasRegistries="hasRegistries"
                            :expiringRegistries="expiringRegistries"
                            :nonCompliantRegistries="nonCompliantRegistries"
                            :registriesCount="registriesCount"
                            :registryMetrics="registryMetrics" class=" sm:w-1/2 "/>

            <!-- Trainings Section -->
            <TrainingsCard class="sm:w-1/2 opacity-25"/>

        </div>
    </AuthenticatedLayout>
</template>

