<script setup>

import {Head, Link, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateWorkspaceRegistriesForm from "@/Pages/Workspaces/Partials/UpdateWorkspaceRegistriesForm.vue";


const props = defineProps({
  workspace: {
    type: Object,
    required: true
  },
  paginatedRegistries: {
    type: Object,
  },
  filters: {
    type: Object,
  },
  registriesIds: {
    type: Array,
  },
  workspaceRegistries: {
    type: Array,
  }
})

const projectId = usePage().props.auth.user.project_id;

</script>

<template>
  <Head title="Workspace"/>

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-white dark:text-gray-700 leading-tight">
        <Link :href="route('workspaces.edit', { project: projectId, workspace: workspace})">Edit Workspace &lt
        </Link>
        Sync Workspace Registries
      </h2>
    </template>

    <div class="px-2 pb-2">

      <div class="space-y-2">


        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">

          <UpdateWorkspaceRegistriesForm :workspace="workspace" :paginatedRegistries="paginatedRegistries"
                                         :filters="filters" :registriesIds="registriesIds"
                                         :workspaceRegistries="workspaceRegistries"/>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>

</template>

