<script setup>

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';


const props = defineProps({
  email: {
    type: String,
    required: true,
  },
  token: {
    type: String,
    required: true,
  },
  name: {
    type: String,
    required: true,
  }
});

const form = useForm({

  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('password.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>

  <Head title="User Registration"/>

  <div class=" lg:flex lg:justify-center grid grid-cols-1">
    <div class="p-6 lg:w-1/2 bg-white dark:bg-gray-800 shadow rounded-lg">
      <div class="flex justify-between">
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-500" viewBox="0 0 24 24">
            <path fill="currentColor"
                  d="M3 17h18c.55 0 1 .45 1 1s-.45 1-1 1H3c-.55 0-1-.45-1-1s.45-1 1-1zm-.5-4.43c.36.21.82.08 1.03-.28l.47-.82l.48.83c.21.36.67.48 1.03.28c.36-.21.48-.66.28-1.02l-.49-.84h.95c.41 0 .75-.34.75-.75s-.34-.75-.75-.75H5.3l.47-.82c.21-.36.09-.82-.27-1.03a.764.764 0 0 0-1.03.28L4 8.47l-.47-.82a.764.764 0 0 0-1.03-.28c-.36.21-.48.67-.27 1.03l.47.82h-.95c-.41 0-.75.34-.75.75s.34.75.75.75h.95l-.48.83c-.2.36-.08.82.28 1.02zm8 0c.36.21.82.08 1.03-.28l.47-.82l.48.83c.21.36.67.48 1.03.28c.36-.21.48-.66.28-1.02l-.48-.83h.95c.41 0 .75-.34.75-.75s-.34-.75-.75-.75h-.96l.47-.82a.76.76 0 0 0-.27-1.03a.746.746 0 0 0-1.02.27l-.48.82l-.47-.82a.742.742 0 0 0-1.02-.27c-.36.21-.48.67-.27 1.03l.47.82h-.96a.74.74 0 0 0-.75.74c0 .41.34.75.75.75h.95l-.48.83c-.2.36-.08.82.28 1.02zM23 9.97c0-.41-.34-.75-.75-.75h-.95l.47-.82a.76.76 0 0 0-.27-1.03a.746.746 0 0 0-1.02.27l-.48.83l-.47-.82a.742.742 0 0 0-1.02-.27c-.36.21-.48.67-.27 1.03l.47.82h-.95a.743.743 0 0 0-.76.74c0 .41.34.75.75.75h.95l-.48.83a.74.74 0 0 0 .28 1.02c.36.21.82.08 1.03-.28l.47-.82l.48.83c.21.36.67.48 1.03.28c.36-.21.48-.66.28-1.02l-.48-.83h.95c.4-.01.74-.35.74-.76z"/>
          </svg>

          <div class="ml-4 text-lg leading-7 font-semibold dark:text-white">Register User</div>
        </div>
        <Link :href="route('welcome')" preserve-scroll>
          <svg xmlns="http://www.w3.org/2000/svg" class="m-4 w-4 h-4 text-gray-500" viewBox="0 0 24 24">
            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M18 6L6 18M6 6l12 12"/>
          </svg>
        </Link>
      </div>
      <div class="mx-12">
        <form @submit.prevent="submit">
          <div>
            <p class="mt-1 block w-full">Welcome {{ props.name }}. Please set your password.</p>
            <InputError class="mt-2" :message="form.errors.email"/>
          </div>

          <div class="mt-4">
            <InputLabel for="password" value="Password"/>

            <TextInput
                id="password"
                type="password"
                class="mt-1 block w-full"
                v-model="form.password"
                required
                autocomplete="new-password"
            />

            <InputError class="mt-2" :message="form.errors.password"/>
          </div>

          <div class="mt-4">
            <InputLabel for="password_confirmation" value="Confirm Password"/>

            <TextInput
                id="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                v-model="form.password_confirmation"
                required
                autocomplete="new-password"
            />

            <InputError class="mt-2" :message="form.errors.password_confirmation"/>
          </div>

          <div class="flex items-center justify-end mt-4">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              Reset Password
            </PrimaryButton>
          </div>

        </form>
      </div>
    </div>
  </div>

</template>
