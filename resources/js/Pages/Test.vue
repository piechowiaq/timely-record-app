<script>
import {ref} from "vue";

const globalSelectedRegistries = ref([]);
</script>

<script setup>

import {useForm} from "@inertiajs/vue3";
import {useTestStore} from '@/Stores/TestStore.js';
import Pagination from "@/Components/Pagination.vue";
import {computed, onMounted, ref, watch, watchEffect} from "vue";

const props = defineProps({
  paginatedRegistries: Object,
  allRegistriesIds: Array,
  workspaceRegistriesIds: Array
});

const store = useTestStore();

watchEffect(() => {
  store.initializeSelectedRegistries(props.workspaceRegistriesIds);
});

const form = useForm({
  registries: '',
})

const countOfTotalRegistries = computed(() => props.allRegistriesIds.length);

// Handles the change in 'Select All' checkbox state
const handleSelectAll = (selectAll) => {
  store.setSelectAll(selectAll, props.allRegistriesIds);
};

// Toggles the selection of an individual registry and updates the 'Select All' state
const handleCheckboxChange = (registryId) => {
  store.toggleRegistry(registryId);
  store.updateSelectAllState(countOfTotalRegistries.value);
};

</script>

<template>

  <div>
    {{ countOfTotalRegistries }}

    <br>
    {{ workspaceRegistriesIds }}
  </div>

  <div>

    {{ store.selectAll }}<br>
    {{ store.selectedRegistries }}<br>


    <form @submit.prevent="form.post(route('test', { registries: Array.from(store.selectedRegistries)}) )" class="p-8">

      <div>
        <input type="checkbox" v-model="store.selectAll" @change="handleSelectAll(store.selectAll)">
        Select All
      </div>
      <div v-for="registry in paginatedRegistries.data" :key="registry.id">
        <input
            type="checkbox"
            :value="registry.id"
            :checked="store.selectedRegistries.has(registry.id)"
            @change="() => handleCheckboxChange(registry.id)"
        >
        {{ registry.name }}
      </div>

      <button type="submit">Submit Selected Registries</button>
    </form>

    <Pagination :links="paginatedRegistries.links"
                class="flex items-center justify-end py-2"></Pagination>
  </div>


</template>

<style scoped>

</style>
