<script setup>
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps(['movie'])
const page = usePage()

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
    <div class="p-4 border rounded mb-4">
        <h2 class="text-xl font-bold">{{ movie.title }}</h2>
        <p>{{ movie.description }}</p>
        <p class="text-sm text-gray-600">
          Posted by <Link :href="route('movies.byUser', movie.user.id)">
            {{ page.props.auth.user && page.props.auth.user.id === movie.user.id ? 'you' : movie.user.name }}
          </Link>
          on {{ formattedDate }}
        </p>

        <div class="flex gap-4 mt-2">
            <form @submit.prevent="vote('like')">
                <button class="text-green-600">👍 {{ movie.likes_count }}</button>
            </form>
            <form @submit.prevent="vote('hate')">
                <button class="text-red-600">👎 {{ movie.hates_count }}</button>
            </form>
        </div>
    </div>
</template>
