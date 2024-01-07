<script setup lang="ts">
import { initDropdowns } from 'flowbite';
import { type TReminder } from '~/types/reminder';

const limitOptions = [10, 25, 50, 100]

const filter = reactive({
  limit: 10,
})

const isBusy = reactive({
  getData: false,
})

const data = ref<Array<TReminder>>([])

watch(filter, () => {
  getData()
})

onMounted(() => {
  initDropdowns()

  getData()
})

async function getData() {
  try {
    isBusy.getData = true

    const { data: res, error } = await useApiGet('/reminders', {
      query: filter,
      pick: ['ok', 'data'],
    })

    if (!res.value?.ok) {
      throw error
    }

    data.value = res.value?.data.reminders
  } catch (error) {
    console.error(error);

    alert('Something went wrong')
    return
  } finally {
    isBusy.getData = false
  }
}

function showDeleteModal(reminder: TReminder) {
  useEventEmit('modal:reminder:delete', reminder)
}
</script>

<template>
  <div class="bg-white p-8 rounded-lg mx-2 dark:bg-gray-800">
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
      <div>
        <button id="dropdownRadioButton" data-dropdown-toggle="dropdownRadio" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600" type="button">
          <svg class="w-3 h-3 text-gray-800 dark:text-white me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 16.5h3M2 1h14a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1Z" />
          </svg>
          Show: {{ filter.limit }} items
          <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
          </svg>
        </button>
        <!-- Dropdown menu -->
        <div id="dropdownRadio" class="z-10 hidden w-48 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="top" style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate3d(522.5px, 3847.5px, 0px);">
          <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownRadioButton">
            <li v-for="limit in limitOptions">
              <div class="flex items-center rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                <input :id="`filter-limit-${limit}`" type="radio" :value="limit" v-model="filter.limit" name="filter-limit" class="w-4 h-4 ms-2 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label :for="`filter-limit-${limit}`" class="w-full p-2 text-sm font-medium text-gray-900 rounded hover:cursor-pointer dark:text-gray-300">{{ limit }}</label>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <NuxtLink to="/reminders/create" class="flex justify-center gap-1 text-white bg-nabitu-800 hover:bg-nabitu-700 focus:ring-4 focus:outline-none focus:ring-nabitu-500 font-medium rounded-lg text-sm px-5 py-2.5 justify-center disabled:bg-nabitu-900 disabled:hover:bg-nabitu-900 dark:bg-nabitu-800 dark:hover:bg-nabitu-700 dark:focus:ring-nabitu-800">
        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
        </svg>
        <span>Add Reminder</span>
      </NuxtLink>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left rtl:text-right text-slate-500 dark:text-gray-400" :class="{ 'blur-[1px]': isBusy.getData }">
        <thead class="text-xs text-nabitu-800 uppercase bg-nabitu-200 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-4">
              #
            </th>
            <th scope="col" class="px-6 py-4">
              Event Name
            </th>
            <th scope="col" class="px-6 py-4">
              Reminder Date & Time
            </th>
            <th scope="col" class="px-6 py-4">
              Event Date & Time
            </th>
            <th scope="col" class="px-6 py-4">
              Description
            </th>
            <th scope="col" class="px-6 py-4">
              Action
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!data.length">
            <td colspan="6">
              <div class="text-center font-bold py-3">
                <span v-if="isBusy.getData">Loading</span>
                <span v-else>No data found</span>
              </div>
            </td>
          </tr>
          <tr v-for="(item, index) in data" class="bg-white border-nabitu-100 dark:bg-gray-800 dark:border-gray-700 hover:bg-nabitu-50 dark:hover:bg-gray-600" :class="{ 'border-b': index < data.length - 1 }">
            <th scope="row" class="px-6 py-4 font-medium text-nabitu-900 whitespace-nowrap dark:text-white">
              {{ index + 1 }}
            </th>
            <td class="px-6 py-4 min-w-56">
              {{ item.title }}
            </td>
            <td class="px-6 py-4 min-w-56">
              {{ unixToDateTime(item.remind_at) }}
            </td>
            <td class="px-6 py-4 min-w-56">
              {{ unixToDateTime(item.event_at) }}
            </td>
            <td class="px-6 py-4 max-w-60 truncate">
              {{ item.description }}
            </td>
            <td class="px-6 py-4 flex gap-3">
              <NuxtLink :to="`/reminders/${item.id}`" title="View" class="hover:text-nabitu-600 p-2 dark:hover:text-white">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                  <path d="M17 0h-5.768a1 1 0 1 0 0 2h3.354L8.4 8.182A1.003 1.003 0 1 0 9.818 9.6L16 3.414v3.354a1 1 0 0 0 2 0V1a1 1 0 0 0-1-1Z" />
                  <path d="m14.258 7.985-3.025 3.025A3 3 0 1 1 6.99 6.768l3.026-3.026A3.01 3.01 0 0 1 8.411 2H2.167A2.169 2.169 0 0 0 0 4.167v11.666A2.169 2.169 0 0 0 2.167 18h11.666A2.169 2.169 0 0 0 16 15.833V9.589a3.011 3.011 0 0 1-1.742-1.604Z" />
                </svg>
              </NuxtLink>
              <NuxtLink :to="`/reminders/${item.id}/edit`" title="Edit" class="hover:text-yellow-400 p-2 dark:hover:text-white">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                  <path d="m13.835 7.578-.005.007-7.137 7.137 2.139 2.138 7.143-7.142-2.14-2.14Zm-10.696 3.59 2.139 2.14 7.138-7.137.007-.005-2.141-2.141-7.143 7.143Zm1.433 4.261L2 12.852.051 18.684a1 1 0 0 0 1.265 1.264L7.147 18l-2.575-2.571Zm14.249-14.25a4.03 4.03 0 0 0-5.693 0L11.7 2.611 17.389 8.3l1.432-1.432a4.029 4.029 0 0 0 0-5.689Z" />
                </svg>
              </NuxtLink>
              <button @click="showDeleteModal(item)" title="Delete" class="hover:text-red-600 p-2 dark:hover:text-white">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h16M7 8v8m4-8v8M7 1h4a1 1 0 0 1 1 1v3H6V2a1 1 0 0 1 1-1ZM3 5h12v13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5Z" />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <LazyReminderDeleteModal @success="getData" />
  </div>
</template>