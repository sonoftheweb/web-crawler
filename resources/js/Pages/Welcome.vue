<script setup>
import { EllipsisVerticalIcon, CheckIcon } from '@heroicons/vue/20/solid'
import {Head} from "@inertiajs/inertia-vue3";
import {ref} from "vue";

defineProps({
	pages: Array,
});

const form = ref({
	url: 'https://agencyanalytics.com/',
	count: 6
});

const data = ref(null);
const loading = ref(false);

const crawlSite = () => {
	loading.value = true;
	axios.post('/crawl-site', form.value).then(res => {
		data.value = res.data;
		loading.value = false;
	});
}
</script>

<template>
    <Head title="Crawl sites" />

    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center text-5xl text-green-400 font-bold">
                Laravel Crawler
            </div>

					<form @submit.prevent="crawlSite" class="mt-8 sm:flex sm:items-center justify-between md:bg-white md:rounded md:border-gray-300 md:shadow-sm">
						<div class="w-[410px]">
							<label for="url" class="sr-only">URL</label>
							<input type="url" name="url" id="url" class="search-input" disabled='disabled' v-model="form.url" placeholder="you@example.com" />
						</div>
						<div class="w-[90px]">
							<label for="count" class="sr-only">Count</label>
							<input type="number" name="count" id="count" class="search-input border-l border-l-grey rounded-none" v-model="form.count" placeholder="you@example.com" />
						</div>
						<button :disabled="form.processing" type="submit" class="crawl-button">Begin Crawl</button>
					</form>

					<div class="text-center dark:text-gray-400" v-if="loading">Loading...</div>

					<div class="mt-10" v-if="data">
						<h3 class="text-3xl font-bold text-center dark:text-gray-400">Details</h3>
						<ul role="list" class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-2">
							<li class="col-span-1 flex rounded-md shadow-sm">
								<div class="flex-shrink-0 flex items-center justify-center w-16 text-white text-sm font-medium rounded-l-md bg-green-300">
									<CheckIcon class="h-5 w-5" aria-hidden="true" />
								</div>
								<div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">
									<div class="flex-1 truncate px-4 py-2 text-sm">
										<p class="font-medium text-gray-900 hover:text-gray-600">Pages Crawled</p>
										<p class="text-gray-500">{{ data.pages.length }} pages</p>
									</div>
									<div class="flex-shrink-0 pr-2">
										<button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
											<span class="sr-only">Open options</span>
											<EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
										</button>
									</div>
								</div>
							</li>
							<li class="col-span-1 flex rounded-md shadow-sm">
								<div class="flex-shrink-0 flex items-center justify-center w-16 text-white text-sm font-medium rounded-l-md bg-green-300">
									<CheckIcon class="h-5 w-5" aria-hidden="true" />
								</div>
								<div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">
									<div class="flex-1 truncate px-4 py-2 text-sm">
										<p class="font-medium text-gray-900 hover:text-gray-600">Unique images</p>
										<p class="text-gray-500">{{ data.unique_images }} images</p>
									</div>
									<div class="flex-shrink-0 pr-2">
										<button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
											<span class="sr-only">Open options</span>
											<EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
										</button>
									</div>
								</div>
							</li>
							<li class="col-span-1 flex rounded-md shadow-sm">
								<div class="flex-shrink-0 flex items-center justify-center w-16 text-white text-sm font-medium rounded-l-md bg-green-300">
									<CheckIcon class="h-5 w-5" aria-hidden="true" />
								</div>
								<div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">
									<div class="flex-1 truncate px-4 py-2 text-sm">
										<p class="font-medium text-gray-900 hover:text-gray-600">Unique Links</p>
										<p class="text-gray-500">{{ data.unique_links }} links</p>
									</div>
									<div class="flex-shrink-0 pr-2">
										<button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
											<span class="sr-only">Open options</span>
											<EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
										</button>
									</div>
								</div>
							</li>
							<li class="col-span-1 flex rounded-md shadow-sm">
								<div class="flex-shrink-0 flex items-center justify-center w-16 text-white text-sm font-medium rounded-l-md bg-green-300">
									<CheckIcon class="h-5 w-5" aria-hidden="true" />
								</div>
								<div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">
									<div class="flex-1 truncate px-4 py-2 text-sm">
										<p class="font-medium text-gray-900 hover:text-gray-600">Unique External Links</p>
										<p class="text-gray-500">{{ data.unique_external_links }} links</p>
									</div>
									<div class="flex-shrink-0 pr-2">
										<button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
											<span class="sr-only">Open options</span>
											<EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
										</button>
									</div>
								</div>
							</li>
							<li class="col-span-1 flex rounded-md shadow-sm">
								<div class="flex-shrink-0 flex items-center justify-center w-16 text-white text-sm font-medium rounded-l-md bg-green-300">
									<CheckIcon class="h-5 w-5" aria-hidden="true" />
								</div>
								<div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">
									<div class="flex-1 truncate px-4 py-2 text-sm">
										<p class="font-medium text-gray-900 hover:text-gray-600">Average Load Time</p>
										<p class="text-gray-500">{{ data.average_load_times }} Seconds</p>
									</div>
									<div class="flex-shrink-0 pr-2">
										<button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
											<span class="sr-only">Open options</span>
											<EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
										</button>
									</div>
								</div>
							</li>
							<li class="col-span-1 flex rounded-md shadow-sm">
								<div class="flex-shrink-0 flex items-center justify-center w-16 text-white text-sm font-medium rounded-l-md bg-green-300">
									<CheckIcon class="h-5 w-5" aria-hidden="true" />
								</div>
								<div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">
									<div class="flex-1 truncate px-4 py-2 text-sm">
										<p class="font-medium text-gray-900 hover:text-gray-600">Average Word Count</p>
										<p class="text-gray-500">{{ data.average_word_count }} Words</p>
									</div>
									<div class="flex-shrink-0 pr-2">
										<button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
											<span class="sr-only">Open options</span>
											<EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
										</button>
									</div>
								</div>
							</li>
							<li class="col-span-1 flex rounded-md shadow-sm">
								<div class="flex-shrink-0 flex items-center justify-center w-16 text-white text-sm font-medium rounded-l-md bg-green-300">
									<CheckIcon class="h-5 w-5" aria-hidden="true" />
								</div>
								<div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white">
									<div class="flex-1 truncate px-4 py-2 text-sm">
										<p class="font-medium text-gray-900 hover:text-gray-600">Average Title Length</p>
										<p class="text-gray-500">{{ data.average_title_length }} Characters</p>
									</div>
									<div class="flex-shrink-0 pr-2">
										<button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
											<span class="sr-only">Open options</span>
											<EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
										</button>
									</div>
								</div>
							</li>
						</ul>

						<h3 class="mt-16 text-3xl font-bold text-center dark:text-gray-400">List of crawled pages</h3>
						<div class="px-4 sm:px-6 lg:px-8">
							<div class="mt-4 flex flex-col">
								<div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
									<div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
										<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
											<table class="min-w-full divide-y divide-gray-300">
												<thead class="bg-gray-50">
												<tr>
													<th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">URL</th>
													<th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Load Time (s)</th>
													<th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
												</tr>
												</thead>
												<tbody class="divide-y divide-gray-200 bg-white">
												<tr v-for="page in data.pages" :key="page.href">
													<td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ page.href }}</td>
													<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ page.load_time }}</td>
													<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ page.http_code }}</td>
												</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
        </div>
    </div>
</template>
