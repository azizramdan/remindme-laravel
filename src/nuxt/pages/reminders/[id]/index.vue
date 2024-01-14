<script setup lang="ts">
import type { TReminder } from '~/types/reminder';

const reminder = ref<TReminder>()

getData()

async function getData() {
  try {
    const { data, error } = await useApiGet(`/reminders/${useRoute().params.id}`, {
      pick: ['ok', 'data'],
    })

    if (!data.value?.ok) {
      throw error
    }

    reminder.value = data.value?.data
  } catch (error: any) {
    if (error.value?.statusCode === 404) {
      throw showError({
        statusCode: 404,
        statusMessage: error.value?.data.msg || 'Page Not Found'
      })
    }

    console.error(error);
    alert('Something went wrong')
  }
}

function showDeleteModal() {
  useEventEmit('modal:reminder:delete', reminder.value!)
}
</script>

<template>
  <div v-if="reminder" class="bg-white p-8 rounded-lg mx-2 dark:bg-gray-800">
    <div class="flex justify-center gap-6">
      <NuxtLink to="/reminders" title="Back to Reminders" class="hover:text-nabitu-600 p-2 dark:hover:text-white">
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4" />
        </svg>
      </NuxtLink>
      <NuxtLink :to="`/reminders/${reminder.id}/edit`" title="Edit" class="hover:text-yellow-400 p-2 dark:hover:text-white">
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="m13.835 7.578-.005.007-7.137 7.137 2.139 2.138 7.143-7.142-2.14-2.14Zm-10.696 3.59 2.139 2.14 7.138-7.137.007-.005-2.141-2.141-7.143 7.143Zm1.433 4.261L2 12.852.051 18.684a1 1 0 0 0 1.265 1.264L7.147 18l-2.575-2.571Zm14.249-14.25a4.03 4.03 0 0 0-5.693 0L11.7 2.611 17.389 8.3l1.432-1.432a4.029 4.029 0 0 0 0-5.689Z" />
        </svg>
      </NuxtLink>
      <button @click="showDeleteModal" title="Delete" class="hover:text-red-600 p-2 dark:hover:text-white">
        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h16M7 8v8m4-8v8M7 1h4a1 1 0 0 1 1 1v3H6V2a1 1 0 0 1 1-1ZM3 5h12v13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5Z" />
        </svg>
      </button>
    </div>

    <div class="mt-10">
      <div class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</div>
      <div>{{ reminder.title }}</div>
    </div>
    <div class="mt-4">
      <div class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reminder Date & Time</div>
      <div>{{ unixToDateTime(reminder.remind_at) }}</div>
    </div>
    <div class="mt-4">
      <div class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Event Date & Time</div>
      <div>{{ unixToDateTime(reminder.event_at) }}</div>
    </div>
    <div class="mt-4">
      <div class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</div>
      <div>{{ reminder.description }}</div>
    </div>

    <LazyReminderDeleteModal @success="navigateTo('/reminders')" />
  </div>
</template>