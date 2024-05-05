<script setup>
import { computed, defineProps, ref, watchEffect } from "vue";
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
    TransitionRoot,
} from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";

const props = defineProps(["list"]);
const emit = defineEmits(["update:selected"]);

const list = props.list;

let selected = ref(list[0]);
let query = ref("");

let filteredList = computed(() =>
    query.value === ""
        ? list
        : list.filter((listItem) =>
              listItem.name
                  .toLowerCase()
                  .replace(/\s+/g, "")
                  .includes(query.value.toLowerCase().replace(/\s+/g, "")),
          ),
);

watchEffect(() => {
    emit("update:selected", selected.value);
});
</script>

<template>
    <div class="mt-1 block w-full">
        <Combobox v-model="selected">
            <div class="relative mt-1">
                <div
                    class="relative w-full cursor-default overflow-hidden bg-white text-left sm:text-sm"
                >
                    <ComboboxInput
                        class="w-full border-gray-300 py-2 pl-3 pr-10 leading-5 text-gray-900"
                        :displayValue="(listItem) => listItem.name"
                        @change="query = $event.target.value"
                    />
                    <ComboboxButton
                        class="absolute inset-y-0 right-0 flex items-center pr-2"
                    >
                        <ChevronUpDownIcon
                            class="h-5 w-5 text-gray-400"
                            aria-hidden="true"
                        />
                    </ComboboxButton>
                </div>
                <TransitionRoot
                    leave="transition ease-in duration-100"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                    @after-leave="query = ''"
                >
                    <ComboboxOptions
                        class="absolute mt-1 max-h-60 w-full overflow-auto bg-white py-1 text-base ring-1 ring-black/5 focus:outline-none sm:text-sm"
                    >
                        <div
                            v-if="filteredList.length === 0 && query !== ''"
                            class="relative cursor-default select-none px-4 py-2 text-gray-700"
                        >
                            Nothing found.
                        </div>

                        <ComboboxOption
                            v-for="listItem in filteredList"
                            as="template"
                            :key="listItem.id"
                            :value="listItem"
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
                                    :class="{
                                        'font-medium': selected,
                                        'font-normal': !selected,
                                    }"
                                >
                                    {{ listItem.name }}
                                </span>
                                <span
                                    v-if="selected"
                                    class="absolute inset-y-0 left-0 flex items-center pl-3"
                                    :class="{
                                        'text-white': active,
                                        'text-cyan-600': !active,
                                    }"
                                >
                                    <CheckIcon
                                        class="h-5 w-5"
                                        aria-hidden="true"
                                    />
                                </span>
                            </li>
                        </ComboboxOption>
                    </ComboboxOptions>
                </TransitionRoot>
            </div>
        </Combobox>
    </div>
</template>

<style scoped></style>
