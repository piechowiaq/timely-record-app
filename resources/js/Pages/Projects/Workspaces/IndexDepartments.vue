<script setup>
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Pagination from "@/Components/Pagination.vue";
import { computed, ref, watch } from "vue";
import { debounce } from "lodash";
import Search from "@/Components/Search.vue";
import { useDepartmentsStore } from "@/Stores/DepartmentsStore.js";

const props = defineProps([
    "workspace",
    "departments",
    "filters",
    "departmentsIds",
    "workspaceDepartmentsIds",
]);

const projectId = usePage().props.projectId;

const page = usePage();

const departmentsStore = useDepartmentsStore();

if (departmentsStore.initialized === false) {
    departmentsStore.updateForm({
        departmentsIds: props.workspaceDepartmentsIds,
    });
}

const form = useForm({
    departmentsIds: departmentsStore.form.departmentsIds,
});

const departmentsIds = computed({
    get: () => form.departmentsIds,
    set: (value) => {
        form.departmentsIds = value;
        departmentsStore.updateForm({
            ...departmentsStore.form,
            departmentsIds: value,
        });
    },
});

const selectAll = computed({
    get: () => form.departmentsIds.length === props.departments.meta.total,
    set: (value) => {
        form.departmentsIds = value ? [...props.departmentsIds] : [];
        departmentsStore.updateForm({ departmentsIds: form.departmentsIds });
    },
});

function submit() {
    form.put(route("workspaces.sync-departments", props.workspace.id), {
        preserveScroll: true,
    });
}

const index = ref({
    search: props.filters.search,
});

watch(
    index.value,
    debounce(() => {
        router.get(
            route("workspaces.index-departments", props.workspace.id),
            index.value,
            {
                // preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 300),
);

const sort = (field) => {
    index.value.field = field;
    index.value.direction = index.value.direction === "asc" ? "desc" : "asc";
};

const resetSearch = () => {
    index.value.search = "";
};

const getSortIconClass = (field) => {
    if (index.value.field !== field) {
        return "fa-solid fa-sort fa-xs ml-2"; // Default icon when the field is not the current sort field
    }
    return index.value.direction === "asc"
        ? "fa-solid fa-sort-down fa-xs ml-2"
        : "fa-solid fa-sort-up fa-xs ml-2";
};

router.on("start", (event) => {
    if (
        event.detail.visit.url.pathname !==
        `/workspaces/${props.workspace.id}/index-departments`
    ) {
        departmentsStore.$reset();
    }
});
</script>

<template>
    <Head title="Workspace" />

    <AuthenticatedLayout>
        <template #header>
            <h2>
                <Link :href="route('workspaces.edit', workspace.id)"
                    >Edit Workspace &lt
                </Link>
                Sync Workspace Departments
            </h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2 dark:bg-gray-700 dark:text-gray-400">
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <header>
                        <h2
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            Workspace Departments
                        </h2>

                        <p
                            class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                        >
                            Synchronize workspace Departments.
                        </p>
                    </header>

                    <div class="mt-6 flex items-center justify-between">
                        <Search
                            v-model="index.search"
                            @update:model-value="index.search = $event"
                            @reset="resetSearch"
                        />
                    </div>
                    <div class="relative overflow-x-auto">
                        <form
                            @submit.prevent="submit"
                            method="post"
                            class="space-y-6"
                        >
                            <table
                                class="w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400"
                            >
                                <thead
                                    class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400"
                                >
                                    <tr>
                                        <th scope="col" class="px-6 py-2">
                                            <input
                                                type="checkbox"
                                                id="select-all"
                                                v-model="selectAll"
                                                class="border-gray-300 font-medium text-cyan-600 shadow-sm focus:ring-transparent"
                                            />
                                        </th>
                                        <th scope="col" class="py-2"></th>

                                        <th
                                            scope="col"
                                            class="px-6 py-2"
                                            @click="sort('name')"
                                        >
                                            Name
                                            <i
                                                :class="
                                                    getSortIconClass('name')
                                                "
                                            ></i>
                                        </th>

                                        <th
                                            scope="col"
                                            class="px-6 py-2 text-center"
                                            @click="sort('project_id')"
                                        >
                                            Type
                                            <i
                                                :class="
                                                    getSortIconClass(
                                                        'project_id',
                                                    )
                                                "
                                            ></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template
                                        v-for="(
                                            department, index
                                        ) in departments.data"
                                        :key="department.id"
                                        :class="{
                                            'bg-white dark:bg-gray-800': true,
                                            'border-b dark:border-gray-700':
                                                index !==
                                                departments.data.length - 1,
                                        }"
                                    >
                                        <tr>
                                            <th
                                                scope="row"
                                                class="w-16 whitespace-nowrap px-6 py-2 font-medium text-gray-900 dark:text-white"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :id="`checkbox-${department.id}`"
                                                    v-model="departmentsIds"
                                                    :value="department.id"
                                                    class="border-gray-300 font-medium text-cyan-600 shadow-sm focus:ring-transparent"
                                                />
                                            </th>
                                            <th
                                                scope="row"
                                                class="w-16 whitespace-nowrap text-center font-medium text-gray-900 dark:text-white"
                                            ></th>

                                            <th
                                                scope="row"
                                                class="whitespace-nowrap px-6 py-2 font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ department.name }}
                                            </th>

                                            <td
                                                class="flex justify-center px-6 py-2 text-center"
                                            >
                                                <ApplicationLogo
                                                    v-if="
                                                        department.project_id ===
                                                        null
                                                    "
                                                    class="h-4 w-4 fill-white stroke-2"
                                                ></ApplicationLogo>
                                                <p
                                                    v-else
                                                    class="text-xs italic"
                                                >
                                                    custom
                                                </p>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <div class="flex justify-between">
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

                                <Pagination
                                    :links="departments.meta.links"
                                    class="flex items-center justify-end py-2"
                                ></Pagination>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
