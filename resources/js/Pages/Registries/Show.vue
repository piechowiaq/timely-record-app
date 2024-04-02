<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import {Head, Link, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps(['registry']);

const projectId = usePage().props.projectId;

</script>

<template>
    <Head title="Workspace"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Create Custom Registry</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="dark:bg-gray-700 dark:text-gray-400 space-y-2">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Registry Information</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Timely Record generic registry.
                            </p>
                        </header>

                        <div
                            class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="name" value="Name"/>

                                <p
                                    id="name"

                                    class="mt-1 block w-full dark:text-gray-300 text-gray-600"

                                >{{ registry.name }}</p>


                            </div>

                            <div>
                                <InputLabel for="description" value="Description"/>

                                <p
                                    id="description"

                                    class="mt-1 block w-full dark:text-gray-300 text-gray-600"

                                >{{ registry.description }}</p>


                            </div>

                            <div>
                                <InputLabel for="validity" value="Validity period in months"/>

                                <p
                                    id="validity"

                                    class="mt-1 block w-full dark:text-gray-300 text-gray-600"

                                >{{ registry.validity_period }}</p>


                            </div>

                            <div class="flex items-center gap-4">

                                <Link
                                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                                    :href="route('registries.index')">Back
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

