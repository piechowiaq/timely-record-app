<script setup>
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Pagination from "@/Components/Pagination.vue";
import { useRegistriesStore } from "@/Stores/RegistriesStore.js";
import { computed, ref, watch } from "vue";
import { debounce } from "lodash";
import Search from "@/Components/Search.vue";

const props = defineProps([
    "workspace",
    "registries",
    "filters",
    "registriesIds",
    "workspaceRegistriesIds",
]);

const projectId = usePage().props.projectId;

const page = usePage();

const registriesStore = useRegistriesStore();

if (registriesStore.initialized === false) {
    registriesStore.updateForm({ registriesIds: props.workspaceRegistriesIds });
}

const form = useForm({
    registriesIds: registriesStore.form.registriesIds,
});

const registriesIds = computed({
    get: () => form.registriesIds,
    set: (value) => {
        form.registriesIds = value;
        registriesStore.updateForm({
            ...registriesStore.form,
            registriesIds: value,
        });
    },
});

const selectAll = computed({
    get: () => form.registriesIds.length === props.registries.meta.total,
    set: (value) => {
        form.registriesIds = value ? [...props.registriesIds] : [];
        registriesStore.updateForm({ registriesIds: form.registriesIds });
    },
});

function submit() {
    form.put(route("workspaces.sync-registries", props.workspace.id), {
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
            route("workspaces.index-registries", props.workspace.id),
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
        `/workspaces/${props.workspace.id}/index-registries`
    ) {
        registriesStore.$reset();
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
                Sync Workspace Registries
            </h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2 dark:bg-gray-700 dark:text-gray-400">
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
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
                                            class="px-6 py-2"
                                            @click="sort('validity_period')"
                                        >
                                            Valid | in months
                                            <i
                                                :class="
                                                    getSortIconClass(
                                                        'validity_period',
                                                    )
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
                                            registry, index
                                        ) in registries.data"
                                        :key="registry.id"
                                        :class="{
                                            'bg-white dark:bg-gray-800': true,
                                            'border-b dark:border-gray-700':
                                                index !==
                                                registries.data.length - 1,
                                        }"
                                    >
                                        <tr>
                                            <th
                                                scope="row"
                                                class="w-16 whitespace-nowrap px-6 py-2 font-medium text-gray-900 dark:text-white"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :id="`checkbox-${registry.id}`"
                                                    v-model="registriesIds"
                                                    :value="registry.id"
                                                    class="border-gray-300 font-medium text-cyan-600 shadow-sm focus:ring-transparent"
                                                />
                                            </th>
                                            <th
                                                scope="row"
                                                class="w-16 whitespace-nowrap text-center font-medium text-gray-900 dark:text-white"
                                            >
                                                <!--                                    <i :class="{-->
                                                <!--                  'fa-solid': true,-->
                                                <!--                  'fa-circle-info': true,-->
                                                <!--                  'hover:text-amber-400': true,-->
                                                <!--                  'text-amber-400': selectedRegistryId === registry.id,-->
                                                <!--                  'text-amber-200': selectedRegistryId !== registry.id-->
                                                <!--                }"-->
                                                <!--                                       @click="() => toggleDescription(registry.id)"></i>-->
                                            </th>

                                            <th
                                                scope="row"
                                                class="whitespace-nowrap px-6 py-2 font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ registry.name }}
                                            </th>

                                            <td class="w-1/6 px-6 py-2">
                                                {{ registry.validity_period }}
                                            </td>
                                            <td
                                                class="flex justify-center px-6 py-2 text-center"
                                            >
                                                <ApplicationLogo
                                                    v-if="
                                                        registry.project_id ===
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
                                        <!--                            <tr v-if="selectedRegistryId === registry.id">-->
                                        <!--                                <td colspan="5" class="px-6 py-2 bg-gray-50 text-xs text-justify">-->
                                        <!--                                    {{ registry.description }}-->
                                        <!--                                </td>-->
                                        <!--                            </tr>-->
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
                                    :links="registries.meta.links"
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
