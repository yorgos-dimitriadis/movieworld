<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import {ref, computed} from 'vue'
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import MovieCard   from "@/Components/MovieCard.vue";
import {
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
} from '@headlessui/vue'
import Modal from "@/Components/Modal.vue";
import { ChevronDownIcon, Squares2X2Icon } from '@heroicons/vue/20/solid'
import InputError from "@/Components/InputError.vue";
import SuccessNotification from "@/Components/SuccessNotification.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";


const openMovieForm = ref(false)
const showSuccessNotification = ref(false)

const props = defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  movies: Array|Object
});

const sortOptions = [
  { name: 'Date', href: '#'},
  { name: 'Likes', href: '#' },
  { name: 'Hates', href: '#'},
]

const titleInput = ref(null)
const selectedSort = ref(sortOptions[0].name.toLowerCase())
const selectedUserId = ref(null);

const filteredAndSortedMovies = computed(() => {
  if (!props.movies.data) return [];
  let movies = [...props.movies.data];

  // Filter by selected user ID if set
  if (selectedUserId.value) {
    console.log('Filtering movies for user ID:', selectedUserId.value);
    return movies.filter(movie => movie.user.id === selectedUserId.value);
  }

  // Apply sorting based on selectedSort
  if (selectedSort.value === 'likes') {
    return movies.sort((a, b) => b.likes - a.likes);
  } else if (selectedSort.value === 'hates') {
    return movies.sort((a, b) => b.hates - a.hates);
  } else if (selectedSort.value === 'date') {
    return movies.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  }

  return movies;
});

const gridView = ref(true)
const gridV = computed(() => gridView.value ? 'lg:grid-cols-2' : 'grid-cols-1')

const newMovie = useForm({
  title: '',
  description: ''
})
function addMovie() {
  newMovie.post(route('movies.store'), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      closeMovieForm()
      showSuccessNotification.value = true
    },
    onError: (errors) => {
      console.error('Failed to add movie:', errors)
      titleInput.value.focus()
    }
  })
}

const closeMovieForm = () => {
  openMovieForm.value = false
  newMovie.title = ''
  newMovie.description = ''
  newMovie.errors = {}
  newMovie.reset()
}

const closeSuccessNotification = () => {
  showSuccessNotification.value = false
}

const handleSelectedUserId = (userId) => {
  selectedUserId.value = userId;
  console.log('Selected user ID:', userId);
};
</script>

<template>
    <Head title="Welcome" />

    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 selection:bg-red-500 selection:text-white">
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Welcome Back {{ $page.props.auth.user.name }}</Link
            >

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Log in</Link
                >
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ml-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Register</Link
                >
            </template>
        </div>

        <div class="p-6 lg:p-8">
            <div class="flex justify-center">
                <ApplicationLogo
                    class="w-36 h-auto"
                />
            </div>

            <div class="mt-16">

              <div class="bg-white">
                <div>
                  <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-baseline justify-between border-b border-gray-200 pb-6 pt-8">
                      <h1 class="text-4xl font-bold tracking-tight text-gray-900">Movies</h1>

                      <div class="flex items-center">
                        <Menu as="div" class="relative inline-block text-left">
                          <div>
                            <MenuButton class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900">
                              Sort by
                              <ChevronDownIcon class="-mr-1 ml-1 size-5 shrink-0 text-gray-400 group-hover:text-gray-500" aria-hidden="true" />
                            </MenuButton>
                          </div>

                          <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                            <MenuItems class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white shadow-2xl ring-1 ring-black/5 focus:outline-none">
                              <div class="py-1">
                                <MenuItem v-for="option in sortOptions" :key="option.name" v-slot="{ active }">
                                  <a :href="option.href" :class="[option.name.toLowerCase() == selectedSort ? 'font-medium text-gray-900' : 'text-gray-500', active ? 'bg-gray-100 outline-none' : '', 'block px-4 py-2 text-sm']" @click.prevent="selectedSort = option.name.toLowerCase()">{{ option.name }}</a>
                                </MenuItem>
                              </div>
                            </MenuItems>
                          </transition>
                        </Menu>

                        <button type="button" class="-m-2 ml-5 p-2 text-gray-400 hover:text-gray-500 sm:ml-7 hidden lg:block" @click="gridView = !gridView">
                          <span class="sr-only">View grid</span>
                          <Squares2X2Icon class="size-5" aria-hidden="true" />
                        </button>
                      </div>
                    </div>

                    <section aria-labelledby="movies-heading" class="pb-24 pt-6">
                      <h2 id="products-heading" class="sr-only">Movies</h2>

                      <div class="grid grid-cols-1 gap-6 w-full" :class="gridV">
                        <div class="flex justify-between items-center" :class="gridView ? 'lg:col-span-2' : 'col-span-1'">
                          <strong class="font-bold">Found {{ filteredAndSortedMovies.length }} movies</strong>
                          <a href="#" class="text-sm text-gray-500 hover:text-gray-900" @click.prevent="selectedUserId = null">Clear Filter</a>
                          <button v-if="$page.props.auth.user" type="button" class="rounded-md bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-100" @click="openMovieForm = true">New Movie</button>
                        </div>

                        <Modal :show="openMovieForm" @close="closeMovieForm()">
                          <div class="p-6">
                            <div class="space-y-12">
                              <div class="border-b border-gray-900/10 pb-12">
                                <h2 class="text-base/7 font-semibold text-gray-900">Add New Moview</h2>
                                <p class="mt-1 text-sm/6 text-gray-600">This information will be displayed publicly so be careful what you share.</p>
                                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                  <div class="col-span-full">
                                    <InputLabel for="title" value="Title"/>
                                    <TextInput
                                      id="title"
                                      ref="titleInput"
                                      v-model="newMovie.title"
                                      type="text"
                                      class="mt-2 w-full"
                                    />
                                    <InputError :message="newMovie.errors.title" class="mt-2"/>
                                  </div>
                                  <div class="col-span-full">
                                    <label for="description" class="block text-sm/6 font-medium text-gray-900">Description</label>
                                    <div class="mt-2">
                                      <textarea name="description" id="description" v-model="newMovie.description" rows="3" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                    </div>
                                    <p class="mt-3 text-sm/6 text-gray-600">Write a few sentences about movie.</p>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="mt-6 flex items-center justify-end gap-x-6">
                              <button type="button" class="text-sm/6 font-semibold text-gray-900" @click="closeMovieForm()">Cancel</button>
                              <button type="button" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" @click="addMovie()">Save</button>
                            </div>
                          </div>
                        </Modal>

                        <SuccessNotification :show="showSuccessNotification" @close="closeSuccessNotification">
                          <p class="text-sm font-medium text-gray-900">Successfully saved!</p>
                          <p class="mt-1 text-sm text-gray-500">You can now see it in the list.</p>
                        </SuccessNotification>

                        <MovieCard
                          v-for="movie in filteredAndSortedMovies"
                          :key="movie.id"
                          :movie="movie"
                          @selectedUserId="handleSelectedUserId"
                        />

                      </div>
                    </section>
                  </main>
                </div>
              </div>

            </div>

        </div>
    </div>
</template>

<style scoped>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
</style>
