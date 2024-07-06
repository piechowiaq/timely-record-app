<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DeleteDepartmentForm from "@/Pages/Projects/Departments/Partials/DeleteDepartmentForm.vue";
import { useUserStore } from "@/Stores/UserStore.js";
import { computed } from "vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps(["department", "workspaces", "workspacesIds"]);

const projectId = usePage().props.projectId;

const userStore = useUserStore();

if (userStore.initialized === false) {
    userStore.updateForm(props.user);
}

const form = useForm({
    name: props.department.name,
    workspacesIds: userStore.form.workspacesIds,
});

const workspacesIds = computed({
    get: () => form.workspacesIds,
    set: (value) => {
        form.workspacesIds = value;
        userStore.updateForm({ ...userStore.form, workspacesIds: value });
    },
});

const selectAll = computed({
    get: () => form.workspacesIds.length === props.workspaces.meta.total,
    set: (value) => {
        form.workspacesIds = value ? [...props.workspacesIds] : [];
        userStore.updateForm({ workspacesIds: form.workspacesIds });
    },
});

const superAdmin = computed(() => projectId === null);

function submit() {
    form.patch(route("departments.update", props.department.id), {
        preserveScroll: true,
    });
}

router.on("start", (event) => {
    if (
        event.detail.visit.url.pathname !==
        `/departments/${props.department.id}/edit`
    ) {
        userStore.$reset();
    }
});
</script>

<template>
    <Head title="Workspace" />

    <AuthenticatedLayout>
        <template #header>
            <h2>Edit Department</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2 dark:bg-gray-700 dark:text-gray-400">
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <section class="max-w-xl">
                        <header>
                            <h2
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                Department Information
                            </h2>

                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Your project's department information.
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
                            <div v-if="!superAdmin">
                                <InputLabel
                                    for="workspaces"
                                    value="Workspaces"
                                />
                                <div
                                    class="mt-1 border border-gray-300 px-2 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-cyan-600 dark:focus:ring-cyan-600"
                                >
                                    <div
                                        class="flex items-center justify-between pb-4 pt-2"
                                    >
                                        <div class="flex">
                                            <input
                                                type="checkbox"
                                                id="select-all"
                                                v-model="selectAll"
                                                class="border-gray-300 font-medium text-cyan-600 shadow-sm focus:ring-transparent"
                                            />
                                            <label
                                                for="select-all"
                                                class="ml-2 text-sm"
                                            >
                                                Select All
                                            </label>
                                        </div>
                                        <Pagination
                                            :links="workspaces.meta.links"
                                            class="flex items-center justify-end py-2"
                                        ></Pagination>
                                    </div>

                                    <div
                                        v-for="(
                                            workspace, index
                                        ) in workspaces.data"
                                        :key="workspace.id"
                                        :class="{
                                            'border-b border-gray-300 dark:border-gray-700':
                                                index !==
                                                workspaces.data.length - 1,
                                        }"
                                        class="flex items-center py-2"
                                    >
                                        <input
                                            type="checkbox"
                                            :id="`checkbox-${workspace.id}`"
                                            v-model="workspacesIds"
                                            :value="workspace.id"
                                            class="border-gray-300 font-medium text-cyan-600 shadow-sm focus:ring-transparent"
                                        />
                                        <div
                                            class="flex flex-col justify-center text-sm"
                                        >
                                            <label
                                                :for="`checkbox-${workspace.id}`"
                                                class="ml-2 font-medium text-gray-900 dark:text-gray-300"
                                            >
                                                {{ workspace.name }}
                                                <span
                                                    v-if="workspace.location"
                                                    class="text-xs font-normal text-gray-500 dark:text-gray-300"
                                                >
                                                    {{ workspace.location }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.workspacesIds"
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
                                <DeleteDepartmentForm
                                    :department="department"
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
