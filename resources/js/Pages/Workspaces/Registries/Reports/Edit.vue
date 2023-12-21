<script setup>

import {reactive} from 'vue'
import {Head, router, useForm, useRemember} from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";


const props = defineProps({
  report: Object,
  workspace: Object,
  registry: Object,
  project: Object,
})

const form = useForm(useRemember(reactive({
      notes: props.report.notes,
      report_date: props.report.report_date,
      registry_id: props.registry.id,
      workspace_id: props.workspace.id,
    }))
)

const update = () => {
  form.patch(route('workspace.registry.reports.update', {
    project: props.project.id,
    workspace: props.workspace.id,
    registry: props.registry.id,
    report: props.report.id
  }));
};

const destroy = (report) => {
  router.delete(route('workspace.registry.reports.destroy', {
    project: props.project.id,
    workspace: props.workspace.id,
    registry: props.registry.id,
    report: props.report.id
  }))
};

</script>

<template>
  <Head title="Workspace"/>

  <AuthenticatedLayout :workspace="workspace">

    <template #header>
      <h2 class="text-white dark:text-gray-700 leading-tight">Create User</h2>
    </template>

    <div class="px-2 pb-2">
      <div class="space-y-2">
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
          <section class="max-w-xl">
            <header>
              <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Upload Report</h2>

              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Please provide required data to create report.
              </p>
            </header>
            <form @submit.prevent="update">

              <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                <TextInput type="date" v-model="form.report_date" :error="form.errors.report_date"
                           class="pb-8 pr-6 w-full lg:w-1/2"
                           label="Date of the report"/>
                <TextInput v-model="form.notes" :error="form.errors.notes" class="pb-8 pr-6 w-full lg:w-1/2"
                           label="Notes"/>
              </div>
              <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                <PrimaryButton v-if="!report.deleted_at" value="Delete"
                               @click.once="destroy(report)" tabindex="-1"
                               type="button" class="text-red-600 hover:underline">Delete Report
                </PrimaryButton>
                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  Edit Registry
                </PrimaryButton>
              </div>

            </form>
          </section>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

</template>


