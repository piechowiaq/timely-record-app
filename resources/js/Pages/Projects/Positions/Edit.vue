<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DeletePositionForm from "@/Pages/Projects/Positions/Partials/DeletePositionForm.vue";
import Combobox from "@/Components/Combobox.vue";
import { ref, watchEffect } from "vue";

const props = defineProps(["position", "departments"]);

const projectId = usePage().props.projectId;

const form = useForm({
    name: props.position.name,
    department_id: props.position.department_id,
});

function submit() {
    form.patch(route("positions.update", props.position.id), {
        preserveScroll: true,
    });
}

function handleSelection(department) {
    form.department_id = department ? department.id : null;
}

const initialDepartment = ref(null);

watchEffect(() => {
    initialDepartment.value =
        props.departments.find(
            (department) => department.id === props.position.department_id,
        ) || null;
    if (initialDepartment.value) {
        handleSelection(initialDepartment.value);
    }
});
</script>

<template>
    <Head title="Workspace" />

    <AuthenticatedLayout>
        <template #header>
            <h2>Edit position</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2 dark:bg-gray-700 dark:text-gray-400">
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <section class="max-w-xl">
                        <header>
                            <h2
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                Position Information
                            </h2>
                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Your project's position information.
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
                                    for="department"
                                    value="Department"
                                />
                                <Combobox
                                    id="department"
                                    :list="props.departments"
                                    :selected="initialDepartment"
                                    @update:selected="handleSelection"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.department_id"
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
                                <DeletePositionForm
                                    :position="props.position"
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
