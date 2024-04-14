<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps(["training"]);

const projectId = usePage().props.projectId;
</script>

<template>
    <Head title="Workspace" />

    <AuthenticatedLayout>
        <template #header>
            <h2>Create Custom Training</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2 dark:bg-gray-700 dark:text-gray-400">
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <section class="max-w-xl">
                        <header>
                            <h2
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                Training Information
                            </h2>

                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Timely Record generic training.
                            </p>
                        </header>

                        <div class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="name" value="Name" />

                                <p
                                    id="name"
                                    class="mt-1 block w-full text-gray-600 dark:text-gray-300"
                                >
                                    {{ training.name }}
                                </p>
                            </div>

                            <div>
                                <InputLabel
                                    for="description"
                                    value="Description"
                                />

                                <p
                                    id="description"
                                    class="mt-1 block w-full text-gray-600 dark:text-gray-300"
                                >
                                    {{ training.description }}
                                </p>
                            </div>

                            <div>
                                <InputLabel
                                    for="validity"
                                    value="Validity period in months"
                                />

                                <p
                                    id="validity"
                                    class="mt-1 block w-full text-gray-600 dark:text-gray-300"
                                >
                                    {{ training.validity_period }}
                                </p>
                            </div>

                            <div class="flex items-center gap-4">
                                <Link
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
                                    :href="route('trainings.index')"
                                    >Back
                                </Link>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                </Transition>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
