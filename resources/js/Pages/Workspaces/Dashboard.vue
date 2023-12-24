<script setup>
import {Head, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RegistriesCard from "@/Pages/Workspaces/Partials/RegistriesCard.vue";
import TrainingsCard from "@/Pages/Workspaces/Partials/TrainingsCard.vue";

defineProps({
  workspace: {
    type: Object,
  },
  project: {
    type: Object
  },
  mostOutdatedRegistries: {
    type: Array,
  },
  expiringSoonRegistries: {
    type: Array,
  },
  recentlyUpdatedRegistries: {
    type: Array,
  },
  countOfUpToDateRegistries: {
    type: Number
  },
  countOfExpiredRegistries: {
    type: Number
  },
  percentageOfUpToDate: {
    type: Number
  }
})

const page = usePage();

const projectId = usePage().props.auth.user.project_id;

</script>

<template>
  <Head title="Workspace"/>

  <AuthenticatedLayout :workspace="workspace">
    <template #header>
      <h2 class="text-white dark:text-gray-700 leading-tight">Dashboard</h2>
    </template>

    <div class="px-2 pb-2 sm:flex sm:space-x-2">

      <!-- Registries Section -->

      <RegistriesCard :workspace="workspace"
                      :percentageOfUpToDate="percentageOfUpToDate"
                      :countOfUpToDateRegistries="countOfUpToDateRegistries"
                      :countOfExpiredRegistries="countOfExpiredRegistries"
                      :mostOutdatedRegistries="mostOutdatedRegistries"
                      :recentlyUpdatedRegistries="recentlyUpdatedRegistries"
                      :expiringSoonRegistries="expiringSoonRegistries" class=" sm:w-1/2 "/>

      <!-- Trainings Section -->
      <TrainingsCard class="sm:w-1/2 opacity-25"/>

    </div>
  </AuthenticatedLayout>
</template>

