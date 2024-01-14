<script setup lang="ts">
const form = reactive({
  id: 0,
  title: '',
  event_at: '',
  remind_at: '',
  description: '',
})

const isBusy = reactive({
  update: false,
})

getData()

async function getData() {
  try {
    const { data, error } = await useApiGet(`/reminders/${useRoute().params.id}`, {
      pick: ['ok', 'data'],
    })

    if (!data.value?.ok) {
      throw error
    }

    const reminder = data.value.data

    form.id = reminder.id
    form.title = reminder.title
    form.event_at = unixTimestampToDateTime(reminder.event_at)
    form.remind_at = unixTimestampToDateTime(reminder.remind_at)
    form.description = reminder.description
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

function unixTimestampToDateTime(unixTimestamp: number): string {
  const date = new Date(unixTimestamp * 1000)
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');

  return `${year}-${month}-${day}T${hours}:${minutes}`;
}

async function update() {
  const eventAt = dateStringToUnix(form.event_at)
  const remindAt = dateStringToUnix(form.remind_at)
  const now = dateStringToUnix((new Date()).toLocaleString())

  if (eventAt < now) {
    alert('Event date cannot be in the past')
    return
  }

  if (remindAt < now) {
    alert('Reminder date cannot be in the past')
    return
  }

  if (eventAt < remindAt) {
    alert('Event date cannot be before reminder date')
    return
  }


  try {
    isBusy.update = true

    const { data, error } = await useApiPut(`/reminders/${form.id}`, {
      body: {
        title: form.title,
        event_at: eventAt,
        remind_at: remindAt,
        description: form.description
      },
      pick: ['ok', 'data'],
    })

    if (!data.value?.ok) {
      throw error
    }

    useEventEmit('toast:push', {
      icon: 'success',
      message: 'Reminder updated',
    })
  } catch (error) {
    console.error(error);
    alert('Something went wrong')
  } finally {
    isBusy.update = false
  }
}
</script>

<template>
  <div class="bg-white p-8 rounded-lg mx-2 dark:bg-gray-800">
    <form class="space-y-4 md:space-y-6" @submit.prevent="update">
      <div>
        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Event Name</label>
        <input type="text" name="title" id="title" v-model="form.title" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-nabitu-600 focus:border-nabitu-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Meeting with Bob" required>
      </div>
      <div>
        <label for="event_at" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Event Date & Time</label>
        <input type="datetime-local" name="event_at" id="event_at" v-model="form.event_at" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-nabitu-600 focus:border-nabitu-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
      </div>
      <div>
        <label for="remind_at" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reminder Date & Time</label>
        <input type="datetime-local" name="remind_at" id="remind_at" v-model="form.remind_at" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-nabitu-600 focus:border-nabitu-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
      </div>
      <div>
        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
        <textarea id="description" v-model="form.description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-nabitu-600 focus:border-nabitu-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Discuss about new project related to new system" required></textarea>
      </div>

      <div class="flex justify-center mt-32">
        <button type="submit" :disabled="isBusy.update" class="flex gap-1 w-full text-white bg-nabitu-800 hover:bg-nabitu-700 focus:ring-4 focus:outline-none focus:ring-nabitu-500 font-medium rounded-lg text-sm px-5 py-2.5 justify-center disabled:bg-nabitu-900 disabled:hover:bg-nabitu-900 dark:bg-nabitu-800 dark:hover:bg-nabitu-700 dark:focus:ring-nabitu-800">
          <div v-if="isBusy.update" role="status">
            <svg aria-hidden="true" class="w-5 h-5 text-gray-200 animate-spin dark:text-gray-200 fill-nabitu-700" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
              <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
            </svg>
            <span class="sr-only">Loading...</span>
          </div>
          <span v-else>Update</span>
        </button>
      </div>
    </form>
  </div>
</template>