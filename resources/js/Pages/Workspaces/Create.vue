<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const projectId = usePage().props.auth.user.project_id;

const form = useForm({
    name: '',
    location: '',
});

</script>

<template>
    <Head title="Workspace"/>

    <AuthenticatedLayout>
        <template #header>
            <h2>Create Workspace</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="dark:bg-gray-700 dark:text-gray-400 space-y-2">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Workspace Information</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Your project's workspace information.
                            </p>
                        </header>

                        <form @submit.prevent="form.post(route('workspaces.store', projectId))" method="post"
                              class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="name" value="Name"/>

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />

                                <InputError class="mt-2" :message="form.errors.name"/>
                            </div>

                            <div>
                                <InputLabel for="location" value="Location"/>

                                <TextInput
                                    id="location"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.location"
                                    autofocus
                                    autocomplete="location"
                                />

                                <InputError class="mt-2" :message="form.errors.location"/>
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

