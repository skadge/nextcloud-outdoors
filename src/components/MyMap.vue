<template>
	<div class="map-container">
		<LMap ref="map"
			:zoom="zoom"
			:options="{zoomControl:false}"
			:center="center"
			@ready="onReady"
			@locationfound="onLocationFound">
			<template v-if="location">
				<LCircleMarker :lat-lng="location.latlng" :fill-opacity="1" :radius="10" />
				<LCircle :lat-lng="location.latlng"
					:fill-opacity="0.3"
					:options="{radius:location.accuracy}"
					:stroke="false" />
			</template>
			<LTileLayer :url="url" :attribution="attribution" />
			<LControlZoom position="bottomright" />
			<LControlScale position="topright" :imperial="false" :metric="true" />
			<template v-if="location">
				<LControl position="topright">
					{{ location.latlng }}
				</LControl>
			</template>
			<LMarker :lat-lng="markerLatLng" />
		</LMap>
	</div>
</template>

<script>

import { LMap, LTileLayer, LControl, LMarker, LCircleMarker, LCircle, LControlZoom, LControlScale } from 'vue2-leaflet'

import { delay } from '../utils.js'

import { Icon } from 'leaflet'

delete Icon.Default.prototype._getIconUrl
Icon.Default.mergeOptions({
	iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
	iconUrl: require('leaflet/dist/images/marker-icon.png'),
	shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
})

export default {
	name: 'MyMap',

	components: {
		    LMap,
		    LTileLayer,
		    LControl,
		    LControlScale,
		    LControlZoom,
		    LMarker,
		    LCircleMarker,
		    LCircle,
	},

	data() {
		return {
			map: null,
			location: null,
			url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
			attribution: '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
		        zoom: 15,
			center: [51.505, -0.159],
			markerLatLng: [51.504, -0.159],
		}
	},

	computed: {
	},

	watch: {
	},

	mounted() {
	},

	beforeDestroy() {
	},

	methods: {
		onUpdateValue(newValue) {
			delay(() => {
				this.$emit('edit-route', this.route.id, newValue)
			}, 2000)()
		},
		onReady() {
		    this.map = this.$refs.map.mapObject
		    this.map.locate()
		},
		onLocationFound(l) {
		    // console.log(l)
		    this.location = l
		    this.map.flyTo(this.location.latlng)
		},
	},
}
</script>

<style lang="scss">
@import '~leaflet/dist/leaflet.css';
/*
@import '~leaflet.markercluster/dist/MarkerCluster.css';
@import '~leaflet.markercluster/dist/MarkerCluster.Default.css';
*/

.leaflet-tooltip {
	white-space: normal !important;
}

.leaflet-container {
	background: var(--color-main-background);
}

.leaflet-control-layers-base {
	line-height: 30px;
}

.leaflet-control-layers-selector {
	min-height: 0;
}

.leaflet-control-layers-toggle {
	background-size: 75% !important;
}

.leaflet-control-layers:not(.leaflet-control-layers-expanded) {
	width: 33px;
	height: 37px;
}

.leaflet-control-layers:not(.leaflet-control-layers-expanded) > a {
	width: 100%;
	height: 100%;
}

.leaflet-marker-favorite, .leaflet-marker-favorite-cluster {
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;

	.favorite-marker,
	.favorite-cluster-marker {
		cursor: pointer;
		background: var(--maps-icon-favorite-star) no-repeat 50% 50%;
		border-radius: 50%;
		box-shadow: 0 0 4px #888;
	}

	.favorite-marker {
		height: 18px;
		width: 18px;
		background-size: 12px 12px;
	}

	.favorite-cluster-marker {
		height: 26px;
		width: 26px;
		background-size: 16px 16px;
	}
}

.leaflet-marker-favorite-cluster {
	.label {
		position: absolute;
		top: 0;
		right: 0;
		color: #fff;
		background-color: #333;
		border-radius: 9px;
		height: 18px;
		min-width: 18px;
		line-height: 12px;
		text-align: center;
		padding: 3px;
	}
}

.leaflet-touch {
	.leaflet-control-layers,
	.leaflet-bar {
		border: none;
		border-radius: var(--border-radius);
	}
}

.leaflet-control-attribution.leaflet-control {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	max-width: 50vw;
}

.leaflet-popup {
	.leaflet-popup-content-wrapper {
		border-radius: 4px;
	}

	.leaflet-popup-close-button {
		top: 9px;
		right: 9px;
	}
}
</style>

<style lang="scss" scoped>
.map-container {
	position: relative;
	height: 100%;
	width: 100%;
}
</style>
