<script setup>

import TextInput from "@/Components/TextInput.vue";
import {Head, useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {computed, onUnmounted, watch} from 'vue';
import Pagination from "@/Components/Pagination.vue";
import {useUserStore} from "@/Stores/UserStore.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps(['roles', 'workspaces', 'workspacesIds']);

const projectId = usePage().props.projectId;

const userStore = useUserStore();

const form = useForm(userStore.form);

watch(form, () => {
    userStore.updateForm(form);
}, {deep: true});

const selectAll = computed({
    get: () => form.workspacesIds.length === props.workspaces.meta.total,
    set: (value) => {
        if (value) {
            form.workspacesIds = props.workspacesIds;
            userStore.setWorkspacesIds(props.workspacesIds)
        } else
            form.workspacesIds = [];
        userStore.setWorkspacesIds([]);
    }
});

const page = usePage();

onUnmounted(() => {
    if (page.props.ziggy.location !== route('users.create')) {
        userStore.$reset()
    }
});

function submit() {
    form.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            userStore.$reset()
        },
    })
}

</script>

<template>
    <Head title="Create User"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Create User</h2>
        </template>
        <div class="px-2 pb-2">
            <div class="space-y-2">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <section class="max-w-xl">

                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create User Form</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Please provide required data to create new user.
                            </p>
                        </header>
                        <form @submit.prevent="submit" method="post"
                              class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="name" value="First Name"/>

                                <TextInput
                                    id="first_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.first_name"
                                    required
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

                                    <div v-for="(role, index) in roles" :key="role.id"
                                         :class="{'border-b': index !== roles.length - 1}"
                                         class="flex items-center py-2">
                                        <input
                                            type="radio"
                                            :id="`radio-${role.id}`"
                                            :value="role.name"
                                            v-model="form.role"
                                            class="border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"
                                        />
                                        <label :for="`radio-${role.id}`" class="ml-2 cursor-pointer text-sm">
                                            {{ role.name }}
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
                                                v-model="selectAll"
                                                class="font-medium border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"
                                            />
                                            <label for="select-all" class="text-sm ml-2">
                                                Select All
                                            </label>
                                        </div>

                                        <Pagination :links="workspaces.meta.links"
                                                    class="flex items-center justify-end py-2"
                                        ></Pagination>
                                    </div>


                                    <div v-for="(workspace, index) in workspaces.data"
                                         :key="workspace.id"
                                         :class="{'border-b': index !== workspaces.data.length - 1}"
                                         class="flex items-center py-2">
                                        <input
                                            type="checkbox"
                                            :id="`checkbox-${workspace.id}`"
                                            v-model="form.workspacesIds"
                                            :value="workspace.id"
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
