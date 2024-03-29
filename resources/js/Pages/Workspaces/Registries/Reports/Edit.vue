<script setup>

import {Head, Link, router, useForm, usePage} from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import DeleteReportForm from "@/Pages/Workspaces/Registries/Reports/Partials/DeleteReportForm.vue";


const props = defineProps({
    report: Object,
    workspace: Object,
    registry: Object,
})

const projectId = usePage().props.projectId;

const form = useForm({
        report_date: props.report.report_date,
    }
)

const destroy = (report) => {
    router.delete(route('workspaces.registries.reports.destroy', {
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
            <h2 class="text-white dark:text-gray-700 leading-tight">
                <Link :href="route('workspaces.registries.index',  workspace.id)">
                    Registries &lt
                </Link>
                <Link
                    :href="route('workspaces.registries.show',  [workspace.id, registry.id])">
                    {{ registry.name }} &lt
                </Link>
                Edit Report
            </h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Report</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Please provide required data to edit report.
                            </p>
                        </header>
                        <form
                            @submit.prevent="form.put(route('workspaces.registries.reports.update', [workspace.id, registry.id,report.id]))"
                            class="mt-6 space-y-6">

                            <div>
                                <InputLabel for="report_date" value="Report Date"/>

                                <TextInput
                                    id="report_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.report_date"
                                    required
                                    autocomplete="report_date"
                                />

                                <InputError class="mt-2" :message="form.errors.report_date"/>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <PrimaryButton :disabled="form.processing">Update</PrimaryButton>
                                    <Transition
                                        enter-active-class="transition ease-in-out"
                                        enter-from-class="opacity-0"
                                        leave-active-class="transition ease-in-out"
                                        leave-to-class="opacity-0"
                                    >
                                        <p v-if="form.recentlySuccessful"
                                           class="text-sm text-gray-600 dark:text-gray-400">
                                            Updated.</p>
                                    </Transition>

                                </div>

                                <DeleteReportForm :report="report" :workspace="workspace" :registry="registry"
                                                  class="max-w-xl"/>
                            </div>


                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>


