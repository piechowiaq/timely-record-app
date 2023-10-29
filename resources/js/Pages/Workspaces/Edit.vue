<script setup>

import {useForm, Head, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateWorkspaceInformationForm from "@/Pages/Workspaces/Partials/UpdateWorkspaceInformationForm.vue";
import DeleteWorkspaceForm from "@/Pages/Workspaces/Partials/DeleteWorkspaceForm.vue";



const props = defineProps({
    workspace: {
        type: Object,
        required: true
    }
})


</script>

<template>
    <Head title="Workspace"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Create Workspace</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <UpdateWorkspaceInformationForm :workspace="workspace" class="max-w-xl"/>
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
                    <DeleteWorkspaceForm :workspace="workspace"  class="max-w-xl"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

