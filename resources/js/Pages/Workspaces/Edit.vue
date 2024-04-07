<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UpdateWorkspaceInformationForm from "@/Pages/Workspaces/Partials/UpdateWorkspaceInformationForm.vue";
import DeleteWorkspaceForm from "@/Pages/Workspaces/Partials/DeleteWorkspaceForm.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    workspace: {
        type: Object,
        required: true,
    },
});

const projectId = usePage().props.auth.user.project_id;
</script>

<template>
    <Head title="Workspace" />

    <AuthenticatedLayout>
        <template #header>
            <h2>Edit Workspace</h2>
        </template>

        <div class="px-2 pb-2">
            <div
                class="scroll-region space-y-2 dark:bg-gray-700 dark:text-gray-400"
            >
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <UpdateWorkspaceInformationForm
                        :workspace="workspace"
                        class="max-w-xl"
                    />
                </div>
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <div class="flex max-w-xl justify-between">
                        <header>
                            <h2
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                Workspace Registries
                            </h2>

                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Synchronize workspace registries.
                            </p>
                        </header>

                        <Link
                            :href="
                                route(
                                    'workspaces.index-registries',
                                    workspace.id,
                                )
                            "
                        >
                            <SecondaryButton
                                class="border-amber-600 hover:border-amber-600"
                            >
                                Sync Registries
                            </SecondaryButton>
                        </Link>
                    </div>
                </div>
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <DeleteWorkspaceForm
                        :workspace="workspace"
                        class="max-w-xl"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
