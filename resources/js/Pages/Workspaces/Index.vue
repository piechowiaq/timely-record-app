<script setup>

import {Head, Link, router, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {computed, ref, watch} from "vue";
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    TransitionRoot
} from "@headlessui/vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps(['workspaces', 'filters', 'projects']);

const projectId = usePage().props.projectId;

const superAdmin = usePage().props.projectId === null

const index = ref({
    search: props.filters.search,
});

watch(index.value, debounce(() => {
    router.get(route('workspaces.index', {project: projectId}), index.value, {
        preserveState: true,
        replace: true
    });
}, 300));

const sort = (field) => {
    index.value.field = field;
    index.value.direction = index.value.direction === 'asc' ? 'desc' : 'asc';
}

const resetSearch = () => {
    index.value.search = '';
}

const getSortIconClass = (field) => {
    if (index.value.field !== field) {
        return 'fa-solid fa-sort fa-xs ml-2'; // Default icon when the field is not the current sort field
    }
    return index.value.direction === 'asc' ? 'fa-solid fa-sort-down fa-xs ml-2' : 'fa-solid fa-sort-up fa-xs ml-2';
};


const augmentedProjects = computed(() => [
    {id: '', name: 'All Projects'},
    ...props.projects
]);

const selected = ref({id: '', name: 'All Projects'});

watch(() => selected.value, (newValue) => {

    if (newValue) {
        index.value.projectId = newValue.id;
    } else {

        index.value.projectId = null;
    }
});

let query = ref('')

let filteredProjects = computed(() =>
    query.value === ''
        ? augmentedProjects.value
        : augmentedProjects.value.filter((project) =>
            project.name
                .toLowerCase()
                .replace(/\s+/g, '')
                .includes(query.value.toLowerCase().replace(/\s+/g, ''))
        )
);


</script>

<template>
    <Head title="Project"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Workspaces</h2>
        </template>
        <div class="px-2 pb-2 ">
            <div class="p-6 shadow overflow-x-auto bg-white">
                <div class="flex items-center justify-between">
                    <div class="mb-2 flex items-center">
                        <input v-model="index.search" type="text" name="search" placeholder="Search…"
                               class="text-sm h-8 px-6 py-2 border-gray-200 ">
                        <button type="button"
                                class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-cyan-600"
                                @click="resetSearch">Reset
                        </button>

                    </div>
                    <div v-if="superAdmin" class="z-10">
                        <Combobox v-model="selected">
                            <div class="mb-2 flex items-center">
                                <InputLabel for="projects" value="Project"
                                            class="text-sm px-4 border-gray-200 "/>
                                <div class="relative">
                                    <div>
                                        <ComboboxInput
                                            id="projects"
                                            class="text-sm h-8 px-6 py-3 border-gray-200 "

                                            :displayValue="(project) => project.name"
                                            @change="query = $event.target.value"
                                        />
                                        <ComboboxButton
                                            class="absolute inset-y-0 right-0 flex items-center pr-3"
                                        >
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </ComboboxButton>
                                    </div>
                                    <TransitionRoot
                                        leave="transition ease-in duration-100"
                                        leaveFrom="opacity-100"
                                        leaveTo="opacity-0"
                                        @after-leave="query = ''"
                                    >
                                        <ComboboxOptions
                                            class="absolute mt-1 max-h-60 w-full overflow-auto bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                                            <div
                                                v-if="filteredProjects.length === 0 && query !== ''"
                                                class="relative cursor-default select-none px-4 py-2 text-gray-700"
                                            >
                                                Nothing found.
                                            </div>


                                            <ComboboxOption
                                                v-for="project in filteredProjects"
                                                as="template"
                                                :key="project.id"
                                                :value="project"
                                                v-slot="{ selected, active }"
                                            >
                                                <li
                                                    class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                    :class="{'bg-cyan-600 text-white': active, 'text-gray-500': !active }"
                                                >
                                                    <span
                                                        class="block truncate"
                                                        :class="{ 'font-medium': selected, 'font-normal': !selected }"
                                                    >
                                                      {{ project.name }}
                                                    </span>
                                                    <span
                                                        v-if="selected"
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                        :class="{ 'text-white': active, 'text-cyan-600': !active }"
                                                    >
                                                <i class="fa-solid fa-check"></i>
                                                </span>
                                                </li>
                                            </ComboboxOption>
                                        </ComboboxOptions>
                                    </TransitionRoot>
                                </div>
                            </div>


                        </Combobox>
                    </div>
                    <Link v-else :href="route('workspaces.create', projectId)"
                          class="text-cyan-600 hover:text-cyan-700 text-sm">
                        Create
                        Workspaces
                    </Link>
                </div>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-2" @click="sort('name')">
                                Name
                                <i :class="getSortIconClass('name')"></i>
                            </th>

                            <th scope="col" class="px-6 py-2" @click="sort('location')">
                                Location
                                <i :class="getSortIconClass('location')"></i>
                            </th>

                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(workspace, index) in workspaces.data" :key="workspace.id"
                            :class="{'bg-white dark:bg-gray-800': true, 'border-b dark:border-gray-700': index !== workspaces.data.length - 1}">

                            <th scope="row"
                                class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <Link :href="route('workspaces.edit',  workspace.id)"
                                      class="text-cyan-600 hover:text-cyan-700">
                                    {{ workspace.name }}
                                </Link>
                            </th>

                            <td class="px-6 py-2">
                                {{ workspace.location }}
                            </td>


                        </tr>

                        </tbody>
                    </table>
                </div>

                <Pagination :links="workspaces.meta.links" class="flex items-center justify-end py-2"></Pagination>
            </div>
        </div>

    </AuthenticatedLayout>

</template>

<style scoped>

</style>
