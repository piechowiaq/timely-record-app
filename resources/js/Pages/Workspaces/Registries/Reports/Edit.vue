<script setup>

import {reactive} from 'vue'
import {Head, Link, router, useForm, usePage, useRemember} from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import DangerButton from "@/Components/DangerButton.vue";


const props = defineProps({
    report: Object,
    workspace: Object,
    registry: Object,
    project: Object,
})

const projectId = usePage().props.auth.user.project_id;

const form = useForm(useRemember(reactive({
        report_date: props.report.report_date,
    }))
)

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
            <h2 class="text-white dark:text-gray-700 leading-tight">
                <Link :href="route('workspace.registries.index',  { project: projectId, workspace: workspace})">
                    Registries &lt
                </Link>
                <Link
                    :href="route('workspace.registries.show',  { project: projectId, workspace: workspace, registry: registry})">
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
                            @submit.prevent="form.patch(route('workspace.registry.reports.update', { project: projectId, workspace: workspace.id,  registry: registry.id, report: report.id }))"
                            class="mt-6 space-y-6">

                            <div>
                                <InputLabel for="report_date" value="Report Date"/>

                                <TextInput
                                    id="report_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.report_date"
                                    required
                                    autofocus
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
                                <DangerButton type="button" @click.once="destroy(report)" tabindex="-1" value="Delete">
                                    Delete Report
                                </DangerButton>
                            </div>


                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>


