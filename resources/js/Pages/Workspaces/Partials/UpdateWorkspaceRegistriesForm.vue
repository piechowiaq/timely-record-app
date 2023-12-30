<script setup>

import {router, useForm, usePage,} from "@inertiajs/vue3";
import {computed, ref, watch, watchEffect} from "vue";
import {debounce} from "lodash";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {useRegistriesStore} from "@/Stores/RegistriesStore.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";


const props = defineProps({
  workspace: Object,
  paginatedRegistries: Object,
  allRegistriesIds: Array,
  workspaceRegistriesIds: Array,
  filters: Object,
});

const registriesStore = useRegistriesStore();

// Use watchEffect to initialize selected registries as soon as the component is mounted.
watchEffect(() => {
  registriesStore.initializeWorkspaceRegistries(props.workspaceRegistriesIds);
});

// Computed property to calculate the total count of registries.
const countOfTotalRegistries = computed(() => props.allRegistriesIds.length);

// Function to handle changes in 'Select All' checkbox.
const handleSelectAll = (selectAll) => {
  registriesStore.setSelectAll(selectAll, props.allRegistriesIds);
};

// Function to handle changes in individual registry selection.
const handleCheckboxChange = (registryId) => {
  registriesStore.toggleRegistry(registryId);
  registriesStore.updateSelectAllState(countOfTotalRegistries.value);
};


const selectedRegistryId = ref(null);

function toggleDescription(id) {

  this.selectedRegistryId = this.selectedRegistryId === id ? null : id;

}


// From submitting functionality
const projectId = usePage().props.auth.user.project_id;

const form = useForm({
  registriesIds: '',
});

function submit() {
  form.patch(route('workspaces.sync-registries', {project: projectId, workspace: props.workspace.id}));
}

// Search and direction functionality

const index = ref({
  search: props.filters.search,
});

watch(index.value, debounce(() => {
  router.get(route('workspaces.edit-registries', {project: projectId, workspace: props.workspace.id}), index.value, {
    preserveState: true,
    preserveScroll: true,
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


</script>

<template>
  <header>
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Workspace Registries</h2>

    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
      Synchronize workspace registries. <br>
      {{ allRegistriesIds }}
      <br>
      {{ countOfTotalRegistries }}

      <br>
      {{ workspaceRegistriesIds }}
    </p>
  </header>

  <div class="flex items-center justify-between mt-6 ">
    <div class=" flex items-center">
      <input v-model="index.search" type="text" name="search" placeholder="Search…"
             class="text-sm h-8 px-6 py-2 border-gray-200 ">
      <button type="button"
              class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-cyan-600"
              @click="resetSearch">Reset
      </button>


    </div>

  </div>
  <div class="relative overflow-x-auto">
    <form
        @submit.prevent="submit"
        method="post"
        class="mt-6 space-y-6">
      <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>

          <th scope="col" class="px-6 py-2">


            <input
                type="checkbox"
                v-model="registriesStore.selectAll"
                @change="handleSelectAll(registriesStore.selectAll)"
                class="font-medium border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"

            />


          </th>
          <th scope="col" class="px-6 py-2">

          </th>

          <th scope="col" class="px-6 py-2" @click="sort('name')">
            Name
            <i :class="getSortIconClass('name')"></i>
          </th>

          <th scope="col" class="px-6 py-2" @click="sort('validity_period')">
            Valid in months
            <i :class="getSortIconClass('validity_period')"></i>
          </th>

          <th scope="col" class="px-6 py-2 text-center" @click="sort('project_id')">
            Type
            <i :class="getSortIconClass('project_id')"></i>
          </th>

        </tr>
        </thead>
        <tbody>
        <template v-for="(registry, index) in paginatedRegistries.data" :key="registry.id"
                  :class="{'bg-white dark:bg-gray-800': true, 'border-b dark:border-gray-700': index !== paginatedRegistries.data.length - 1}">
          <tr>


            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
              <input
                  type="checkbox"
                  :value="registry.id"
                  :checked="registriesStore.selectedRegistries.has(registry.id)"
                  @change="() => handleCheckboxChange(registry.id)"
                  class="font-medium border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"
              />
            </th>
            <th scope="row"
                class="px-6 pr-2 font-medium text-gray-900 whitespace-nowrap dark:text-white flex justify-end ">
              <i class="fa-solid fa-plus text-amber-400 " @click="toggleDescription(registry.id)"></i>
            </th>


            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">


              {{ registry.name }}


            </th>

            <td class="px-6 py-2">
              {{ registry.validity_period }}
            </td>
            <td class="px-6 py-2 text-center flex justify-center">
              <ApplicationLogo v-if="registry.project_id === null"
                               class="w-4 h-4 fill-white stroke-2"></ApplicationLogo>
              <p v-else class="italic text-xs">custom</p>
            </td>


          </tr>

          <tr v-if="selectedRegistryId === registry.id">

            <td colspan="5" class="px-6 py-2 bg-gray-50 text-xs">
              {{ registry.description }}
            </td>
          </tr>

        </template>


        </tbody>
      </table>
      <div class="flex justify-between">
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


        <Pagination :links="paginatedRegistries.links"
                    class="flex items-center justify-end py-2"></Pagination>
      </div>

    </form>

  </div>


</template>

<style scoped>

</style>
