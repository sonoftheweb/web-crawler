<script setup>
import {Head, Link, useForm} from '@inertiajs/inertia-vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const form = useForm({
	url: null,
});

const crawlSite = () => {
	form.post('/crawl-site');
}
</script>

<template>
    <Head title="Crawl sites" />

    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            <Link v-if="$page.props.user" :href="route('dashboard')" class="text-sm text-gray-700 dark:text-gray-500 underline">Dashboard</Link>

            <template v-else>
                <Link :href="route('login')" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</Link>

                <Link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</Link>
            </template>
        </div>

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center text-5xl text-green-400 font-bold">
                Laravel Crawler
            </div>

					<form @submit.prevent="crawlSite" class="mt-8 sm:flex sm:items-center md:bg-white md:rounded md:border-gray-300 md:shadow-sm">
						<div class="w-[500px]">
							<label for="url" class="sr-only">URL</label>
							<input type="url" name="url" id="url" class="search-input" v-model="form.url" placeholder="you@example.com" />
						</div>
						<button :disabled="form.processing" type="submit" class="crawl-button">Begin Crawl</button>
					</form>
					<div class="my-3 text-red-500 text-sm px-3" v-if="form.errors.url">{{ form.errors.url }}</div>
        </div>
    </div>
</template>
