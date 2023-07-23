<template>
	<NcAppNavigation>
		<template #list>
			<NcAppNavigationNewItem
				:title="t('outdoors', 'Create route')"
				@new-item="$emit('create-route', $event)">
				<template #icon>
					<PlusIcon />
				</template>
			</NcAppNavigationNewItem>
			<h2 v-if="loading"
				class="icon-loading-small loading-icon" />
			<NcEmptyContent v-else-if="sortedRoutes.length === 0"
				:title="t('outdoors', 'No routes yet')">
				<template #icon>
					<NoteIcon :size="20" />
				</template>
			</NcEmptyContent>
			<NcAppNavigationItem v-for="route in sortedRoutes"
				:key="route.id"
				:name="route.description"
				:class="{ selectedRoute: route.id === selectedRouteId }"
				:force-display-actions="true"
				:force-menu="false"
				@click="$emit('click-route', route.id)">
				<template #icon>
					<NoteIcon />
				</template>
				<template #actions>
					<NcActionButton
						:close-after-click="true"
						@click="$emit('export-route', route.id)">
						<template #icon>
							<FileExportIcon />
						</template>
						{{ t('outdoors', 'Re-export to GPX file') }}
					</NcActionButton>
					<NcActionButton
						:close-after-click="true"
						@click="$emit('delete-route', route.id)">
						<template #icon>
							<DeleteIcon />
						</template>
						{{ t('outdoors', 'Delete') }}
					</NcActionButton>
				</template>
			</NcAppNavigationItem>
		</template>
	</NcAppNavigation>
</template>

<script>
import FileExportIcon from 'vue-material-design-icons/FileExport.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import DeleteIcon from 'vue-material-design-icons/Delete.vue'

import NoteIcon from './icons/NoteIcon.vue'

import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation.js'
import NcEmptyContent from '@nextcloud/vue/dist/Components/NcEmptyContent.js'
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem.js'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton.js'
import NcAppNavigationNewItem from '@nextcloud/vue/dist/Components/NcAppNavigationNewItem.js'

import ClickOutside from 'vue-click-outside'

export default {
	name: 'MyNavigation',

	components: {
		NoteIcon,
		NcAppNavigation,
		NcEmptyContent,
		NcAppNavigationItem,
		NcActionButton,
		NcAppNavigationNewItem,
		PlusIcon,
		DeleteIcon,
		FileExportIcon,
	},

	directives: {
		ClickOutside,
	},

	props: {
		routes: {
			type: Object,
			required: true,
		},
		selectedRouteId: {
			type: Number,
			default: 0,
		},
		loading: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			creating: false,
		}
	},
	computed: {
		sortedRoutes() {
			return Object.values(this.routes).sort((a, b) => {
				const { tsA, tsB } = { tsA: a.last_modified, tsB: b.last_modified }
				return tsA > tsB
					? -1
					: tsA < tsB
						? 1
						: 0
			})
		},
	},
	beforeMount() {
	},
	methods: {
		onCreate(value) {
			console.debug('create new route')
		},
	},
}
</script>
<style scoped lang="scss">
.addRouteItem {
	position: sticky;
	top: 0;
	z-index: 1000;
	border-bottom: 1px solid var(--color-border);
	:deep(.app-navigation-entry) {
		background-color: var(--color-main-background-blur, var(--color-main-background));
		backdrop-filter: var(--filter-background-blur, none);
		&:hover {
			background-color: var(--color-background-hover);
		}
	}
}

:deep(.selectedRoute) {
	> .app-navigation-entry {
		background: var(--color-primary-light, lightgrey);
	}

	> .app-navigation-entry a {
		font-weight: bold;
	}
}
</style>
