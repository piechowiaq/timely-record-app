<script setup>
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { computed, watch } from "vue";
import Pagination from "@/Components/Pagination.vue";

import RegistrationLink from "@/Pages/Users/Partials/RegistrationLink.vue";
import DeleteUserForm from "@/Pages/Users/Partials/DeleteUserForm.vue";
import { useUserStore } from "@/Stores/UserStore.js";

const props = defineProps(["user", "roles", "workspaces", "workspacesIds"]);

const projectId = usePage().props.projectId;

const userStore = useUserStore();

if (userStore.initialized === false) {
    userStore.updateForm(props.user);
}

const form = useForm({
    first_name: userStore.form.first_name,
    last_name: userStore.form.last_name,
    email: userStore.form.email,
    role: userStore.form.role,
    workspacesIds: userStore.form.workspacesIds,
});

const firstName = computed({
    get: () => form.first_name,
    set: (value) => {
        form.first_name = value;
        userStore.updateForm({ ...userStore.form, first_name: value });
    },
});

const lastName = computed({
    get: () => form.last_name,
    set: (value) => {
        form.last_name = value;
        userStore.updateForm({ ...userStore.form, last_name: value });
    },
});

const email = computed({
    get: () => form.email,
    set: (value) => {
        form.email = value;
        userStore.updateForm({ ...userStore.form, email: value });
    },
});

const selectedRole = computed({
    get: () => form.role,
    set: (value) => {
        form.role = value;
        userStore.updateForm({ ...userStore.form, role: value });
    },
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
const projectAdmin = computed(() => form.role === "project-admin");

watch(projectAdmin, (newValue) => {
    if (newValue) {
        selectAll.value = newValue;
    }
});

function submit() {
    form.put(route("users.update", props.user.id), {
        preserveScroll: true,
    });
}

router.on("start", (event) => {
    if (event.detail.visit.url.pathname !== `/users/${props.user.id}/edit`) {
        userStore.$reset();
    }
});
</script>

<template>
    <Head title="Workspace" />

    <AuthenticatedLayout>
        <template #header>
            <h2>Edit User</h2>
        </template>
        <div class="px-2 pb-2">
            <div class="space-y-2 dark:bg-gray-700 dark:text-gray-400">
                <div
                    v-if="!user.email_verified"
                    class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8"
                >
                    <RegistrationLink :user="user" class="max-w-xl" />
                </div>

                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <section class="max-w-xl">
                        <header>
                            <h2
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                Edit User Form
                            </h2>

                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Please provide required data to edit user.
                            </p>
                        </header>

                        <form
                            @submit.prevent="submit"
                            method="post"
                            class="mt-6 space-y-6"
                        >
                            <div>
                                <InputLabel
                                    for="first_name"
                                    value="First Name"
                                />

                                <TextInput
                                    id="first_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="firstName"
                                    autocomplete="first_name"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.first_name"
                                />
                            </div>
                            <div>
                                <InputLabel for="last_name" value="Last Name" />

                                <TextInput
                                    id="last_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="lastName"
                                    required
                                    autocomplete="last_name"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.last_name"
                                />
                            </div>
                            <div>
                                <InputLabel for="email" value="Email" />

                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="email"
                                    required
                                    autocomplete="username"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.email"
                                />
                            </div>
                            <!--Role-->
                            <div>
                                <InputLabel for="role" value="Role" />
                                <div
                                    class="mt-1 border border-gray-300 px-2 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-cyan-600 dark:focus:ring-cyan-600"
                                >
                                    <div
                                        v-for="(role, index) in roles"
                                        :key="role.id"
                                        :class="{
                                            'border-b border-gray-300 dark:border-gray-700':
                                                index !== roles.length - 1,
                                        }"
                                        class="flex items-center py-2"
                                    >
                                        <input
                                            type="radio"
                                            :id="`radio-${role.id}`"
                                            :value="role.name"
                                            v-model="selectedRole"
                                            class="border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"
                                        />
                                        <label
                                            :for="`radio-${role.id}`"
                                            class="ml-2 cursor-pointer text-sm"
                                        >
                                            {{ role.name }}
                                        </label>
                                    </div>
                                </div>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.role"
                                />
                            </div>
                            <!--Workspaces-->

                            <div>
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
                                                :disabled="projectAdmin"
                                                type="checkbox"
                                                id="select-all"
                                                v-model="selectAll"
                                                :class="{
                                                    'text-gray-100':
                                                        projectAdmin,
                                                    'text-cyan-600':
                                                        !projectAdmin,
                                                }"
                                                class="border-gray-300 font-medium shadow-sm focus:ring-transparent"
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
                                            :disabled="projectAdmin"
                                            type="checkbox"
                                            :id="`checkbox-${workspace.id}`"
                                            v-model="workspacesIds"
                                            :value="workspace.id"
                                            :class="{
                                                'text-gray-200': projectAdmin,
                                                'text-cyan-600': !projectAdmin,
                                            }"
                                            class="border-gray-300 font-medium shadow-sm focus:ring-transparent"
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
                                <DeleteUserForm :user="user" class="max-w-xl" />
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
