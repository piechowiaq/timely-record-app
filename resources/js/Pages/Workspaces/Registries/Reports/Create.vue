<script setup>

import {computed, reactive, ref, watch} from 'vue'
import {Head, useForm, usePage, useRemember} from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {
  Combobox,
  ComboboxButton,
  ComboboxInput,
  ComboboxOption,
  ComboboxOptions,
  TransitionRoot,
} from '@headlessui/vue'


const props = defineProps({
  workspace: Object,
  registries: Array,

})
const projectId = usePage().props.auth.user.project_id;

const registries = props.registries

let selected = ref(registries[0])
let query = ref('')

let filteredRegistries = computed(() =>
    query.value === ''
        ? registries
        : registries.filter((registry) =>
            registry.name
                .toLowerCase()
                .replace(/\s+/g, '')
                .includes(query.value.toLowerCase().replace(/\s+/g, ''))
        )
)

const form = useForm(useRemember(reactive({
      notes: '',
      report_date: '',
      registry_id: selected.value ? selected.value.id : '',
      workspace_id: props.workspace.id,
    }))
)

watch(() => selected.value, (newValue) => {
  if (newValue) {
    form.registry_id = newValue.id;
  }
}, {immediate: true});


const store = () => {

  form.post(route('workspace.registry.reports.store', {
    project: projectId,
    workspace: props.workspace.id,
  }));
};

</script>

<template>
  <Head title="Workspace"/>

  <AuthenticatedLayout :workspace="workspace">

    <template #header>
      <h2 class="text-white dark:text-gray-700 leading-tight">Create User</h2>
    </template>

    <div class="px-2 pb-2">
      <div class="space-y-2">
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow">
          <section class="max-w-xl">
            <header>
              <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Upload Report</h2>

              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Please provide required data to create report.
              </p>
            </header>
            <form @submit.prevent="store" method="post"
                  class="mt-6 space-y-6">
            
              <div class="">
                <Combobox v-model="selected">
                  <InputLabel for="registries" value="Registry"/>
                  <div class="relative">
                    <div
                        class=" mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 dark:focus:border-cyan-600 focus:ring-cyan-500 dark:focus:ring-cyan-600 shadow-sm relative w-full cursor-default overflow-hidden bg-white text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-cyan-300 sm:text-sm"
                    >
                      <ComboboxInput
                          id="registries"
                          class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 dark:focus:border-cyan-600 focus:ring-cyan-500 dark:focus:ring-cyan-600 shadow-sm w-full border py-3 pl-3 pr-10 leading-5 text-gray-900 focus:ring-0"

                          :displayValue="(registry) => registry.name"
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
                          class="absolute mt-1 max-h-60 w-full overflow-auto bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
                      >
                        <div
                            v-if="filteredRegistries.length === 0 && query !== ''"
                            class="relative cursor-default select-none px-4 py-2 text-gray-700"
                        >
                          Nothing found.
                        </div>

                        <ComboboxOption
                            v-for="registry in filteredRegistries"
                            as="template"
                            :key="registry.id"
                            :value="registry"
                            v-slot="{ selected, active }"
                        >
                          <li
                              class="relative cursor-default select-none py-2 pl-10 pr-4"
                              :class="{
                  'bg-cyan-600 text-white': active,
                  'text-gray-900': !active,
                }"
                          >
                <span
                    class="block truncate"
                    :class="{ 'font-medium': selected, 'font-normal': !selected }"
                >
                  {{ registry.name }}
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
                  <InputError class="mt-2" :message="form.errors.registry_id"/>
                </Combobox>
              </div>

              <div>
                <InputLabel for="report_date" value="Report Date"/>

                <TextInput
                    id="report_date"
                    type="date"
                    class="mt-1 block w-full"
                    v-model="form.report_date"
                    required
                    autofocus
                    autocomplete="report_date"
                />

                <InputError class="mt-2" :message="form.errors.report_date"/>
              </div>
              <div>
                <InputLabel for="notes" value="Notes"/>

                <TextInput
                    id="notes"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.notes"
                    required
                    autofocus
                    autocomplete="notes"
                />

                <InputError class="mt-2" :message="form.errors.notes"/>
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
  </AuthenticatedLayout>

</template>


