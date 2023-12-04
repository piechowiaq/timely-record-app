<script setup>

import {Link, router, useForm, usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref, watch, watchEffect} from "vue";
import {debounce} from "lodash";
import Pagination from "@/Components/Pagination.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {useRegistriesStore} from "@/Stores/RegistriesStore.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";


const props = defineProps({
  paginatedRegistries: {
    type: Object,
  },
  filters: {
    type: Object,
  },
  workspace: {
    type: Object
  },
  registriesIds: {
    type: Array,
  },
});

onMounted(() => {
  // Select each workspace ID from the props when the component is mounted
  props.registriesIds?.forEach(registryId => {
    registriesStore.selectRegistry(registryId);
  });
});

const registriesStore = useRegistriesStore();

const selectedRegistriesIds = computed(() => registriesStore.selectedRegistriesIds);


const form = useForm({
  registriesIds: selectedRegistriesIds.value,
});


const projectId = usePage().props.auth.user.project_id;

const index = ref({
  search: props.filters.search,
});

watch(index.value, debounce(() => {
  router.get(route('workspaces.edit', {project: projectId, workspace: props.workspace.id}), index.value, {
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


const isAllSelected = computed({
  get: () => registriesStore.isAllSelected,
  set: (value) => {
    if (value) {
      registriesStore.selectAllRegistries();
    } else {
      registriesStore.deselectAllRegistries();
    }
  }
});

watchEffect(() => {
  if (props.paginatedRegistries?.data) {
    registriesStore.setRegistriesData(props.paginatedRegistries.data);
  }
  if (props.workspacesIds) {
    registriesStore.setAllRegistryIds(props.registriesIds);
  }
});

watch(selectedRegistriesIds, (newSelectedIds) => {
  form.registriesIds = newSelectedIds;
}, {deep: true});

function toggleRegistrySelection(registryId) {
  if (selectedRegistriesIds.value.includes(registryId)) {
    registriesStore.deselectRegistry(registryId);
  } else {
    registriesStore.selectRegistry(registryId);
  }
}


</script>

<template>


  <div class="flex items-center justify-between">
    <div class="mb-2 flex items-center">
      <input v-model="index.search" type="text" name="search" placeholder="Search…"
             class="text-sm h-8 px-6 py-3 border-gray-200 ">
      <button type="button"
              class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-cyan-600"
              @click="resetSearch">Reset
      </button>
      {{ registriesIds }}{{ isAllSelected }}{{ form.errors }} {{ form.registriesIds }}
    </div>
    <Pagination :links="paginatedRegistries.links" class="flex items-center justify-end py-2"></Pagination>
  </div>
  <div class="relative overflow-x-auto">
    <form
        @submit.prevent="form.patch(route('workspaces.registries.update', { project: projectId, workspace: workspace.id }))"
        method="post"
        class="mt-6 space-y-6">
      <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>

          <th scope="col" class="px-6 py-3">


            <input
                type="checkbox"
                id="select-all"
                :checked="isAllSelected"
                @change="isAllSelected = $event.target.checked"
                class="font-medium border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"

            />


          </th>

          <th scope="col" class="px-6 py-3" @click="sort('name')">
            Name
            <i :class="getSortIconClass('name')"></i>
          </th>

          <th scope="col" class="px-6 py-3" @click="sort('validity_period')">
            Valid in months
            <i :class="getSortIconClass('validity_period')"></i>
          </th>

          <th scope="col" class="px-6 py-3 text-center" @click="sort('project_id')">
            Type
            <i :class="getSortIconClass('project_id')"></i>
          </th>

        </tr>
        </thead>
        <tbody>
        <tr v-for="(registry, index) in paginatedRegistries.data" :key="registry.id"
            :class="{'bg-white dark:bg-gray-800': true, 'border-b dark:border-gray-700': index !== paginatedRegistries.data.length - 1}">


          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            <input
                type="checkbox"
                :id="`checkbox-${registry.id}`"
                :value="registry.id"
                :checked="registriesStore.selectedRegistriesIds.includes(registry.id)"
                v-model="form.registriesIds"
                @change="() => toggleRegistrySelection(registry.id)"
                class="font-medium border-gray-300 text-cyan-600 shadow-sm focus:ring-transparent"
            />
          </th>


          <th scope="row"
              class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            <Link v-if="registry.project_id === null"
                  :href="route('project.registries.show', [ projectId, registry.id])"
                  class="text-cyan-600 hover:text-cyan-700">
              {{ registry.name }}
            </Link>

            <Link v-else :href="route('project.registries.edit', [ projectId, registry.id])"
                  class="text-cyan-600 hover:text-cyan-700">
              {{ registry.name }}
            </Link>


          </th>

          <td class="px-6 py-4">
            {{ registry.validity_period }}
          </td>
          <td class="px-6 py-4 text-center flex justify-center">
            <ApplicationLogo v-if="registry.project_id === null"
                             class="w-4 h-4 fill-white stroke-2"></ApplicationLogo>
            <p v-else class="italic text-xs">custom</p>
          </td>


        </tr>

        </tbody>
      </table>
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

  </div>


</template>

<style scoped>

</style>
