<template>
	<NcContent app-name="outdoors">
		<MyNavigation
			:routes="displayedRoutesById"
			:selected-route-id="state.selected_route_id"
			@click-route="onClickRoute"
			@export-route="onExportRoute"
			@create-route="onCreateRoute"
			@delete-route="onDeleteRoute" />
		<NcAppContent>
			<MyMainContent v-if="selectedRoute"
				:route="selectedRoute"
				@edit-route="onEditRoute" />
			<NcEmptyContent v-else
				:title="t('tutorial_5', 'Select a route')">
				<template #icon>
					<NoteIcon :size="20" />
				</template>
			</NcEmptyContent>
		</NcAppContent>
	</NcContent>
</template>

<script>
import NcContent from '@nextcloud/vue/dist/Components/NcContent.js'
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent.js'
import NcEmptyContent from '@nextcloud/vue/dist/Components/NcEmptyContent.js'

import NoteIcon from '../components/icons/NoteIcon.vue'

import MyNavigation from '../components/MyNavigation.vue'
import MyMainContent from '../components/MyMap.vue'

import axios from '@nextcloud/axios'
import { generateOcsUrl, generateUrl } from '@nextcloud/router'
import { showSuccess, showError, showUndo } from '@nextcloud/dialogs'
import { loadState } from '@nextcloud/initial-state'

import { Timer } from '../utils.js'

export default {
	name: 'App',

	components: {
		NoteIcon,
		NcContent,
		NcAppContent,
		NcEmptyContent,
		MyMainContent,
		MyNavigation,
	},

	props: {
	},

	data() {
		return {
			state: loadState('outdoors', 'outdoors-initial-state'),
		}
	},

	computed: {
		allRoutes() {
			return this.state.routes
		},
		routesToDisplay() {
			return this.state.routes.filter(n => !n.trash)
		},
		displayedRoutesById() {
			const nbi = {}
			this.routesToDisplay.forEach(n => {
				nbi[n.id] = n
			})
			return nbi
		},
		routesById() {
			const nbi = {}
			this.allRoutes.forEach(n => {
				nbi[n.id] = n
			})
			return nbi
		},
		selectedRoute() {
			return this.displayedRoutesById[this.state.selected_route_id]
		},
	},

	watch: {
	},

	mounted() {
	},

	beforeDestroy() {
	},

	methods: {
		onEditRoute(routeId, content) {
			const options = {
				content,
			}
			const url = generateOcsUrl('apps/outdoors/api/v1/routes/{routeId}', { routeId })
			axios.put(url, options).then(response => {
				this.routesById[routeId].content = content
				this.routesById[routeId].last_modified = response.data.ocs.data.last_modified
			}).catch((error) => {
				showError(t('outdoors', 'Error saving route content'))
				console.error(error)
			})
		},
		onCreateRoute(name) {
			console.debug('create route', name)
			const options = {
				name,
			}
			const url = generateOcsUrl('apps/outdoors/api/v1/routes')
			axios.post(url, options).then(response => {
				this.state.routes.push(response.data.ocs.data)
				this.onClickRoute(response.data.ocs.data.id)
			}).catch((error) => {
				showError(t('outdoors', 'Error creating route'))
				console.error(error)
			})
		},
		onDeleteRoute(routeId) {
			console.debug('delete route', routeId)
			this.$set(this.routesById[routeId], 'trash', true)
			// cancel or delete
			const deletionTimer = new Timer(() => {
				this.deleteRoute(routeId)
			}, 10000)
			showUndo(
				t('outdoors', '{name} deleted', { name: this.routesById[routeId].name }),
				() => {
					deletionTimer.pause()
					this.routesById[routeId].trash = false
				},
				{ timeout: 10000 }
			)
		},
		deleteRoute(routeId) {
			const url = generateOcsUrl('apps/outdoors/api/v1/routes/{routeId}', { routeId })
			axios.delete(url).then(response => {
				const indexToDelete = this.state.routes.findIndex(n => n.id === routeId)
				if (indexToDelete !== -1) {
					this.state.routes.splice(indexToDelete, 1)
				}
			}).catch((error) => {
				showError(t('outdoors', 'Error deleting route'))
				console.error(error)
			})
		},
		onClickRoute(routeId) {
			console.debug('click route', routeId)
			this.state.selected_route_id = routeId
			const options = {
				values: {
					selected_route_id: routeId,
				},
			}
			const url = generateUrl('apps/outdoors/config')
			axios.put(url, options).then(response => {
			}).catch((error) => {
				showError(t('outdoors', 'Error saving selected route'))
				console.error(error)
			})
		},
		onExportRoute(routeId) {
			const url = generateOcsUrl('apps/outdoors/api/v1/routes/{routeId}/export', { routeId })
			axios.get(url).then(response => {
				showSuccess(t('outdoors', 'Route exported in {path}', { path: response.data.ocs.data }))
			}).catch((error) => {
				showError(t('outdoors', 'Error deleting route'))
				console.error(error)
			})
		},
	},
}
</script>

<style scoped lang="scss">
// nothing yet
</style>
