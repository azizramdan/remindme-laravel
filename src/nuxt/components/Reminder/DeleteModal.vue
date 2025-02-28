
<script setup lang="ts">
import { Modal, type ModalInterface } from 'flowbite';
import type { TReminder } from '~/types/reminder';

const emits = defineEmits<{
  (e: 'success'): void
}>()


const reminder = ref<TReminder>()
const modal = ref<ModalInterface>();
const isBusy = reactive({
  doDelete: false,
})

onMounted(() => {
  const $modalElement: HTMLElement = document.querySelector('#popup-modal') as HTMLElement;

  modal.value = new Modal($modalElement)

  useEventOn('modal:reminder:delete', (data: TReminder) => {
    if (isBusy.doDelete) {
      modal.value!.show();
      return
    }

    reminder.value = data
    modal.value!.show();
  })
})

onBeforeUnmount(() => {
  useEventOff('modal:reminder:delete')
})

function closeModal() {
  if (isBusy.doDelete) return;

  modal.value?.hide()
}

async function doDelete() {
  try {
    isBusy.doDelete = true

    const { data, error } = await useApiDelete(`/reminders/${reminder.value?.id}`, {
      pick: ['ok'],
    })

    if (!data.value?.ok) {
      throw error
    }

    isBusy.doDelete = false
    closeModal()
    emits('success')
  } catch (error) {
    console.error(error);

    alert('Something went wrong')
  } finally {
    isBusy.doDelete = false
  }
}
</script>

<template>
  <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <button @click="closeModal" type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
          </svg>
          <span class="sr-only">Close modal</span>
        </button>
        <div class="p-8 pb-4 md:p-10 md:pb-4 text-center">
          <svg class="mx-auto text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>

          <h3 class="mt-8 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete <span class="font-bold">{{ reminder?.title }}</span>?</h3>

          <div class="mt-10 flex justify-center">
            <button @click="doDelete" type="button" class="text-white gap-1 bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2" :disabled="isBusy.doDelete">
              <svg v-if="isBusy.doDelete" aria-hidden="true" class="w-5 h-5 text-gray-200 animate-spin dark:text-gray-200 fill-nabitu-700" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
              </svg>
              Yes, I'm sure
            </button>
            <button @click="closeModal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600" :disabled="isBusy.doDelete">No, cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>