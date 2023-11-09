<script setup>

import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {Head, useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {computed, ref, watch} from 'vue';


const props = defineProps({
    roles: {
        type: Array,
    },
    workspaces: {
        type: Array,
    }
});

const projectId = usePage().props.auth.user.project_id;


const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    role: '',
    workspacesIds: [],
});

const selectedItems = ref([]);

const selectAll = computed({
    get: () => selectedItems.value.length === props.workspaces.length,
    set: (newValue) => {
        if (newValue) {
            selectedItems.value = props.workspaces.map(workspace => workspace.id);
        } else {
            selectedItems.value = [];
        }
    }
});

// Update form.workspaces whenever selectedItems changes
watch(selectedItems, (newSelectedItems) => {
    form.workspacesIds = newSelectedItems;
});

// A method to update all selections. This changes selectedItems, which will in turn update form.workspaces via the watch.
function toggleSelectAll() {
    if (selectedItems.value.length === props.workspaces.length) {
        selectedItems.value = [];
    } else {
        selectedItems.value = props.workspaces.map(workspace => workspace.id);
    }
}


</script>

<template>
    <Head title="Workspace"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Create User</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Users Information</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Your project's users information.
                            </p>
                        </header>

                        <form @submit.prevent="form.post(route('users.store', projectId))" method="post"
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

                                    <InputError class="mt-2" :message="form.errors.role"/>
                                </div>
                            </div>

                            <!--Workspaces-->
                            <div>
                                <InputLabel for="workspaces" value="Workspaces"/>
                                <div class="border px-2 mt-1 shadow-sm">
                                    <div class="flex items-center pt-2 pb-4">
                                        <input
                                            type="checkbox"
                                            id="select-all"
                                            :checked="selectAll"
                                            @change="toggleSelectAll"
                                            class="font-medium border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"
                                        />
                                        <label for="select-all" class="text-sm ml-2">
                                            Select All
                                        </label>
                                    </div>

                                    <div v-for="(workspace, index) in workspaces" :key="workspace.id"
                                         :class="{'border-b': index !== workspaces.length - 1}"
                                         class="flex items-center py-2"> <!-- Adjusted classes here -->

                                        <input
                                            type="checkbox"
                                            :id="`checkbox-${workspace.id}`"
                                            :value="workspace.id"
                                            v-model="selectedItems"
                                            class="font-medium border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"
                                        />

                                        <div class="text-sm flex flex-col justify-center">
                                            <!-- Adjusted classes here -->
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
                                    <InputError class="mt-2" :message="form.errors.workspaceIds"/>
                                </div>
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
