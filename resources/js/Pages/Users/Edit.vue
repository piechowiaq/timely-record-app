<script setup>

import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {Head, useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {computed, onMounted, watch, watchEffect} from 'vue';
import {useWorkspacesStore} from "@/Stores/WorkspacesStore.js";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    roles: {
        type: Array,
    },
    paginatedWorkspaces: {
        type: Object,
    },
    workspacesIds: {
        type: Array,
    },
    user: {
        type: Object,
    }
});
const workspacesStore = useWorkspacesStore();

const selectedWorkspacesIds = computed(() => workspacesStore.selectedWorkspacesIds);

const form = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    role: props.user.role,
    workspacesIds: selectedWorkspacesIds.value,
});

onMounted(() => {
    // Select each workspace ID from the props when the component is mounted
    props.user.workspacesIds?.forEach(workspaceId => {
        workspacesStore.selectWorkspace(workspaceId);
    });
});


const projectId = usePage().props.auth.user.project_id;


const isAllSelected = computed({
    get: () => workspacesStore.isAllSelected,
    set: (value) => {
        if (value) {
            workspacesStore.selectAllWorkspaces();
        } else {
            workspacesStore.deselectAllWorkspaces();
        }
    }
});

watchEffect(() => {
    if (props.paginatedWorkspaces?.data) {
        workspacesStore.setWorkspacesData(props.paginatedWorkspaces.data);
    }
    if (props.workspacesIds) {
        workspacesStore.setAllWorkspaceIds(props.workspacesIds);
    }
});

watch(selectedWorkspacesIds, (newSelectedIds) => {
    form.workspacesIds = newSelectedIds;
}, {deep: true});

function toggleWorkspaceSelection(workspaceId) {
    if (selectedWorkspacesIds.value.includes(workspaceId)) {
        workspacesStore.deselectWorkspace(workspaceId);
    } else {
        workspacesStore.selectWorkspace(workspaceId);
    }
}

</script>

<template>
    <Head title="Workspace"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Edit User</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit User Form</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Please provide required data to edit user.
                            </p>
                        </header>
                        <form @submit.prevent="form.put(route('users.update',  [ user.id, projectId  ]))" method="post"
                              class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="name" value="First Name"/>

                                <TextInput
                                    id="first_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.first_name"
                                    required
                                    autofocus
                                    autocomplete="first_name"
                                />

                                <InputError class="mt-2" :message="form.errors.first_name"/>
                            </div>
                            <div>
                                <InputLabel for="last_name" value="Last Name"/>

                                <TextInput
                                    id="last_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.last_name"
                                    required
                                    autofocus
                                    autocomplete="last_name"
                                />

                                <InputError class="mt-2" :message="form.errors.last_name"/>
                            </div>
                            <div>
                                <InputLabel for="email" value="Email"/>

                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                    autocomplete="username"
                                />

                                <InputError class="mt-2" :message="form.errors.email"/>
                            </div>
                            <!--Role-->
                            <div>
                                <InputLabel for="role" value="Role"/>
                                <div class="border px-2 mt-1 shadow-sm">

                                    <div v-for="(role, index) in roles" :key="role"
                                         :class="{'border-b': index !== roles.length - 1}"
                                         class="flex items-center py-2">
                                        <input
                                            type="radio"
                                            :id="`radio-${role}`"
                                            :value="role"
                                            v-model="form.role"
                                            class="border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"
                                        />
                                        <label :for="`radio-${role}`" class="ml-2 cursor-pointer text-sm">
                                            {{ role }}
                                        </label>
                                    </div>


                                </div>
                                <InputError class="mt-2" :message="form.errors.role"/>
                            </div>
                            <!--Workspaces-->

                            <div>
                                <InputLabel for="workspaces" value="Workspaces"/>
                                <div class="border px-2 mt-1 shadow-sm">
                                    <div class="flex pt-2 pb-4 justify-between items-center">

                                        <div class="flex">
                                            <input
                                                type="checkbox"
                                                id="select-all"
                                                :checked="isAllSelected"
                                                @change="isAllSelected = $event.target.checked"
                                                class="font-medium border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"

                                            />
                                            <label for="select-all" class="text-sm ml-2">
                                                Select All
                                            </label>
                                        </div>
                                        <Pagination :links="paginatedWorkspaces.links"
                                                    class="flex items-center justify-end py-2"></Pagination>
                                    </div>


                                    <div v-for="(workspace, index) in workspacesStore.workspacesData"
                                         :key="workspace.id"
                                         :class="{'border-b': index !== workspacesStore.workspacesData.length - 1}"
                                         class="flex items-center py-2">
                                        <input
                                            type="checkbox"
                                            :id="`checkbox-${workspace.id}`"
                                            :value="workspace.id"
                                            :checked="workspacesStore.selectedWorkspacesIds.includes(workspace.id)"
                                            v-model="form.workspacesIds"
                                            @change="() => toggleWorkspaceSelection(workspace.id)"
                                            class="font-medium border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"
                                        />
                                        <div class="text-sm flex flex-col justify-center">
                                            <label :for="`checkbox-${workspace.id}`"
                                                   class="font-medium text-gray-900 dark:text-gray-300 ml-2">
                                                {{ workspace.name }}
                                                <span v-if="workspace.location"
                                                      class="text-xs font-normal text-gray-500 dark:text-gray-300">
                                                {{ workspace.location }}
                                            </span>
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <InputError class="mt-2" :message="form.errors.workspacesIds"/>
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


<style scoped>

</style>
