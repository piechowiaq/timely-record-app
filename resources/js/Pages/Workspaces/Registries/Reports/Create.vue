<script setup>

import {Head, useForm, usePage} from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";


const props = defineProps(['workspace', 'registry']);

const projectId = usePage().props.auth.user.project_id;

const form = useForm({
        report_path: null,
        report_date: ''
    }
)

const store = () => {
    form.post(route('workspaces.registries.reports.store', {
            workspace: props.workspace.id,
            registry: props.registry.id
        }), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
            }
        }
    )
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
                        <form @submit.prevent="store" method="post"
                              class="mt-6 space-y-6">


                            <div>
                                {{ registry.name }}
                            </div>

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
                            <div>
                                <InputLabel for="report_path" value="Upload Report"/>

                                <input type="file" id="report_path" name="report_path"
                                       @input="form.report_path = $event.target.files[0]"
                                       class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 dark:focus:border-cyan-600 focus:ring-cyan-500 dark:focus:ring-cyan-600 shadow-sm"
                                />
                                <InputError class="mt-2" :message="form.errors.report_path"/>


                                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                    {{ form.progress.percentage }}%
                                </progress>

                            </div>


                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">
                                        Saved.</p>
                                </Transition>
                            </div>

                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>


