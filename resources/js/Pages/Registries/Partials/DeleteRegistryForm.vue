<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm, usePage} from '@inertiajs/vue3';
import {nextTick, ref} from 'vue';

const confirmingRegistryDeletion = ref(false);
const passwordInput = ref(null);

const props = defineProps({
  registry: {
    type: Object,
  },
});

const form = useForm({
  password: '',
});

const projectId = usePage().props.auth.user.project_id;

const confirmRegistryDeletion = () => {
  confirmingRegistryDeletion.value = true;

  nextTick(() => passwordInput.value.focus());
};

const deleteRegistry = () => {
  form.delete(route('project.registries.destroy', {project: projectId, registry: props.registry}), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingRegistryDeletion.value = false;

  form.reset();
};
</script>

<template>
  <section>


    <DangerButton @click="confirmRegistryDeletion">Delete Registry</DangerButton>

    <Modal :show="confirmingRegistryDeletion" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          Are you sure you want to delete this Registry?
        </h2>

        <div class="mt-6">
          <InputLabel for="password" value="Password" class="sr-only"/>

          <TextInput
              id="password"
              ref="passwordInput"
              v-model="form.password"
              type="password"
              class="mt-1 block w-3/4"
              placeholder="Password"
              @keyup.enter="deleteRegistry"
          />

          <InputError :message="form.errors.password" class="mt-2"/>
        </div>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal"> Cancel</SecondaryButton>

          <DangerButton
              class="ml-3"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="deleteRegistry"
          >
            Delete Account
          </DangerButton>
        </div>
      </div>
    </Modal>
  </section>
</template>
