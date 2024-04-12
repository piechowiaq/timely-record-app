<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextArea from "@/Components/TextArea.vue";
import DeleteRegistryForm from "@/Pages/Registries/Partials/DeleteRegistryForm.vue";

const props = defineProps(["registry"]);

const projectId = usePage().props.projectId;

const form = useForm({
    name: props.registry.name,
    description: props.registry.description,
    validity_period: props.registry.validity_period,
});

function submit() {
    form.patch(route("registries.update", props.registry.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Workspace" />

    <AuthenticatedLayout>
        <template #header>
            <h2>Create Custom Registry</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2 dark:bg-gray-700 dark:text-gray-400">
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <section class="max-w-xl">
                        <header>
                            <h2
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                Registry Information
                            </h2>

                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Your project's registry information.
                            </p>
                        </header>

                        <form
                            @submit.prevent="submit"
                            method="post"
                            class="mt-6 space-y-6"
                        >
                            <div>
                                <InputLabel for="name" value="Name" />

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.name"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="description"
                                    value="Description"
                                />

                                <TextArea
                                    id="description"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.description"
                                    autofocus
                                    autocomplete="location"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.description"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="validity"
                                    value="Validity period in months"
                                />

                                <TextInput
                                    id="validity"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.validity_period"
                                    autofocus
                                    autocomplete="validity_period"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.validity_period"
                                />
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <PrimaryButton :disabled="form.processing"
                                        >Save
                                    </PrimaryButton>

                                    <Transition
                                        enter-active-class="transition ease-in-out"
                                        enter-from-class="opacity-0"
                                        leave-active-class="transition ease-in-out"
                                        leave-to-class="opacity-0"
                                    >
                                        <p
                                            v-if="form.recentlySuccessful"
                                            class="text-sm text-gray-600 dark:text-gray-400"
                                        >
                                            Saved.
                                        </p>
                                    </Transition>
                                </div>
                                <DeleteRegistryForm
                                    :registry="registry"
                                    class="max-w-xl"
                                />
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
