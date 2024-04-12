<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed, ref, watch } from "vue";
import Pagination from "@/Components/Pagination.vue";
import { debounce } from "lodash";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    TransitionRoot,
} from "@headlessui/vue";
import InputLabel from "@/Components/InputLabel.vue";
import Search from "@/Components/Search.vue";

const props = defineProps(["filters", "trainings", "projects"]);

const projectId = usePage().props.projectId;

const index = ref({
    search: props.filters.search,
});

const superAdmin = usePage().props.projectId === null;

watch(
    index.value,
    debounce(() => {
        router.get(route("trainings.index"), index.value, {
            preserveState: true,
            replace: true,
        });
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

const augmentedProjects = computed(() => [
    { id: "", name: "All Projects" },
    ...props.projects,
]);

const selected = ref({ id: "", name: "All Projects" });

watch(
    () => selected.value,
    (newValue) => {
        if (newValue) {
            index.value.projectId = newValue.id;
        } else {
            index.value.projectId = null;
        }
    },
);

let query = ref("");

let filteredProjects = computed(() =>
    query.value === ""
        ? augmentedProjects.value
        : augmentedProjects.value.filter((project) =>
              project.name
                  .toLowerCase()
                  .replace(/\s+/g, "")
                  .includes(query.value.toLowerCase().replace(/\s+/g, "")),
          ),
);

const isSuperAdmin = usePage()
    .props.auth.user.roles.map((role) => role.name)
    .includes("super-admin");
</script>

<template>
    <Head title="Project" />

    <AuthenticatedLayout>
        <template #header>
            <h2>Trainings</h2>
        </template>
        <div class="px-2 pb-2">
            <div
                class="overflow-x-auto bg-white p-6 shadow dark:bg-gray-800 dark:text-gray-400"
            >
                <div class="flex items-center justify-between">
                    <Search
                        v-model="index.search"
                        @update:model-value="index.search = $event"
                        @reset="resetSearch"
                    />
                    <div v-if="superAdmin" class="mb-2 flex items-center">
                        <Link
                            :href="route('trainings.create')"
                            class="border-r-2 pr-4 text-sm text-cyan-600 hover:text-cyan-700"
                        >
                            Create Training
                        </Link>
                        <Combobox v-model="selected">
                            <div class="z-10 flex items-center">
                                <InputLabel
                                    class="border-gray-200 px-4 text-sm"
                                    for="projects"
                                    value="Project"
                                />
                                <div class="relative">
                                    <div>
                                        <ComboboxInput
                                            id="projects"
                                            :displayValue="
                                                (project) => project.name
                                            "
                                            class="h-8 border-gray-200 px-6 py-3 text-sm"
                                            @change="
                                                query = $event.target.value
                                            "
                                        />
                                        <ComboboxButton
                                            class="absolute inset-y-0 right-0 flex items-center pr-3"
                                        >
                                            <i
                                                class="fa-solid fa-chevron-down"
                                            ></i>
                                        </ComboboxButton>
                                    </div>
                                    <TransitionRoot
                                        leave="transition ease-in duration-100"
                                        leaveFrom="opacity-100"
                                        leaveTo="opacity-0"
                                        @after-leave="query = ''"
                                    >
                                        <ComboboxOptions
                                            class="absolute mt-1 max-h-60 w-full overflow-auto bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
                                        >
                                            <div
                                                v-if="
                                                    filteredProjects.length ===
                                                        0 && query !== ''
                                                "
                                                class="relative cursor-default select-none px-4 py-2 text-gray-700"
                                            >
                                                Nothing found.
                                            </div>

                                            <ComboboxOption
                                                v-for="project in filteredProjects"
                                                :key="project.id"
                                                v-slot="{ selected, active }"
                                                :value="project"
                                                as="template"
                                            >
                                                <li
                                                    :class="{
                                                        'bg-cyan-600 text-white':
                                                            active,
                                                        'text-gray-500':
                                                            !active,
                                                    }"
                                                    class="relative cursor-default select-none py-2 pl-10 pr-4"
                                                >
                                                    <span
                                                        :class="{
                                                            'font-medium':
                                                                selected,
                                                            'font-normal':
                                                                !selected,
                                                        }"
                                                        class="block truncate"
                                                    >
                                                        {{ project.name }}
                                                    </span>
                                                    <span
                                                        v-if="selected"
                                                        :class="{
                                                            'text-white':
                                                                active,
                                                            'text-cyan-600':
                                                                !active,
                                                        }"
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                    >
                                                        <i
                                                            class="fa-solid fa-check"
                                                        ></i>
                                                    </span>
                                                </li>
                                            </ComboboxOption>
                                        </ComboboxOptions>
                                    </TransitionRoot>
                                </div>
                            </div>
                        </Combobox>
                    </div>

                    <Link
                        v-else
                        :href="route('trainings.create')"
                        class="text-sm text-cyan-600 hover:text-cyan-700"
                    >
                        Create Custom Training
                    </Link>
                </div>
                <div class="relative overflow-x-auto">
                    <table
                        class="w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400"
                    >
                        <thead
                            class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                        >
                            <tr>
                                <th
                                    class="px-6 py-2"
                                    scope="col"
                                    @click="sort('name')"
                                >
                                    Name
                                    <i :class="getSortIconClass('name')"></i>
                                </th>

                                <th
                                    class="px-6 py-2"
                                    scope="col"
                                    @click="sort('validity_period')"
                                >
                                    Valid in months
                                    <i
                                        :class="
                                            getSortIconClass('validity_period')
                                        "
                                    ></i>
                                </th>

                                <th
                                    class="px-6 py-2 text-center"
                                    scope="col"
                                    @click="sort('project_id')"
                                >
                                    Type
                                    <i
                                        :class="getSortIconClass('project_id')"
                                    ></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(training, index) in trainings.data"
                                :key="training.id"
                                :class="{
                                    'bg-white dark:bg-gray-800': true,
                                    'border-b dark:border-gray-700':
                                        index !== trainings.data.length - 1,
                                }"
                            >
                                <th
                                    class="whitespace-nowrap px-6 py-2 font-medium text-gray-900 dark:text-white"
                                    scope="row"
                                >
                                    <Link
                                        v-if="
                                            isSuperAdmin &&
                                            training.project_id === null
                                        "
                                        :href="
                                            route('trainings.edit', training.id)
                                        "
                                        class="text-cyan-600 hover:text-cyan-700"
                                    >
                                        {{ training.name }}
                                    </Link>
                                    <Link
                                        v-else-if="training.project_id === null"
                                        :href="
                                            route('trainings.show', training.id)
                                        "
                                        class="text-cyan-600 hover:text-cyan-700"
                                    >
                                        {{ training.name }}
                                    </Link>

                                    <Link
                                        v-else
                                        :href="
                                            route('trainings.edit', training.id)
                                        "
                                        class="text-cyan-600 hover:text-cyan-700"
                                    >
                                        {{ training.name }}
                                    </Link>
                                </th>

                                <td class="px-6 py-2">
                                    {{ training.validity_period }}
                                </td>
                                <td
                                    class="flex justify-center px-6 py-2 text-center"
                                >
                                    <ApplicationLogo
                                        v-if="training.project_id === null"
                                        class="h-4 w-4 fill-white stroke-2"
                                    ></ApplicationLogo>
                                    <p v-else class="text-xs italic">custom</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination
                    :links="trainings.meta.links"
                    class="flex items-center justify-end py-2"
                ></Pagination>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
