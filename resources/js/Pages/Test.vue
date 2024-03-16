<script setup>


import Pagination from "@/Components/Pagination.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {router} from "@inertiajs/vue3";
import {computed, reactive} from "vue";
import {useUserStore} from "@/Stores/UserStore.js";

const props = defineProps(['workspaces']);

const userStore = useUserStore();

const form = reactive({
    first_name: '',
    last_name: userStore.getLastName,
});

const firstName = computed({
    get: () => form.first_name,// Assuming getFirstName is a method
    set: (value) => {
        userStore.setFirstName(value);
        form.first_name = value; // Assuming you want to update the form as well
    }
});

const lastName = computed({
    get: () => userStore.getLastName, // Assuming getLastName is a method
    set: (value) => {
        userStore.setLastName(value);
        form.last_name = value; // Assuming you want to update the form as well
    }
});

function submit() {
    router.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();

        },
    });
}
</script>

<template>

    {{ firstName }}
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
                    <form scroll-region @submit.prevent="submitForm" method="post"
                          class="mt-6 space-y-6">
                        <div>
                            <InputLabel for="name" value="First Name"/>

                            <TextInput
                                id="first_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="firstName"
                                required
                                autocomplete="first_name"
                            />


                        </div>
                        <div>
                            <InputLabel for="last_name" value="Last Name"/>

                            <TextInput
                                id="last_name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="lastName"
                                required
                                autocomplete="last_name"
                            />


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
                            <!--                            <InputError class="mt-2" :message="form.errors.workspacesIds"/>-->
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


</template>

<style scoped>

</style>
