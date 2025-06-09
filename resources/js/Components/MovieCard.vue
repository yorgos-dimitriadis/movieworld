<script setup>
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps(['movie'])
const emit = defineEmits(['selectedUserId'])
const page = usePage()

const handeUserSelection = () => {
    if (props.movie && props.movie.user && props.movie.user.id) {
        emit('selectedUserId', props.movie.user.id)
    } else {
        console.error('Movie or user ID is not available')
    }
}

const formattedDate = computed(() => {
    // Ensure movie and movie.created_at exist and are valid
    if (!props.movie || !props.movie.created_at) return ''
    const date = new Date(props.movie.created_at)
    if (isNaN(date.getTime())) return '' // Invalid date string
    return date.toLocaleString('en-GB', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: false
    })
})

const vote = (type) => {
    if(page.props.auth.user && page.props.auth.user.id === props.movie.user.id) {
      alert('You cannot vote on your own movie.')
      return
    }
    if (type !== 'like' && type !== 'hate') {
        console.error('Invalid vote type:', type)
        return
    }
    if (!page.props.auth.user) {
        alert('You must be logged in to vote.')
        return
    }

    router.post(`/movies/${props.movie.id}/vote`, { type }, { preserveScroll: true })
}
</script>

<template>
    <div class="flex flex-col p-4 border rounded mb-4">
        <div>
          <h2 class="text-xl font-bold">{{ movie.title }}</h2>
          <p>{{ movie.description }}</p>
        </div>

        <div class="flex justify-between items-end gap-4 mt-4">
          <div class="flex items-center gap-2">
            <form @submit.prevent="vote('like')">
              <button class="text-green-600">👍 {{ movie.likes }}</button>
            </form>
            <form @submit.prevent="vote('hate')">
              <button class="text-red-600">👎 {{ movie.hates }}</button>
            </form>
          </div>

          <div class="text-gray-500 text-sm">
            <p class="text-sm text-gray-600">
              Posted by
              <button class="text-blue-600 hover:underline" @click.prevent="handeUserSelection">
                {{ page.props.auth.user && page.props.auth.user.id === movie.user.id ? 'you' : movie.user.name }}
              </button>
              on {{ formattedDate }}
            </p>
          </div>
        </div>
    </div>
</template>
