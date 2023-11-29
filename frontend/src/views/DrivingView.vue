<template>
     <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">{{ title }}</h1>
        <div>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left" v-if="!trip.is_complete">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapMap :zoom="14" :center="location.current.geometry" ref="gMap"
                            style="width:100%; height: 256px;">
                            <!-- have different icons for current and destination -->
                            <GMapMarker :position="location.current.geometry" :icon="currentIcon" /> 
                            <GMapMarker :position="location.destination.geometry" :icon="destinationIcon" />
                        </GMapMap>
                    </div>
                    <div class="mt-2">
                        <p class="text-xl">Going to <strong>pick up a passenger</strong></p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <!-- only have the ability to complete trip if the trip has is_started -->
                    <button v-if="trip.is_started"
                        @click="handleCompleteTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        Complete Trip</button>
                    <button v-else
                        @click="handlePassengerPickedUp"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        Passenger Picked Up</button>
                </div>
            </div>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left" v-else>
                <div class="bg-white px-4 py-5 sm:p-6">
                    <Tada />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useLocationStore } from '@/stores/location'
import { useTripStore } from '@/stores/trip'
import { useRouter } from 'vue-router'
import { ref, onMounted, onUnmounted } from 'vue'
import http from '@/helpers/http'
import Tada from '@/components/Tada.vue' 

const location = useLocationStore()
const trip = useTripStore()
const router = useRouter()

const gMap = ref(null)
const intervalRef = ref(null)

const title = ref('Driving to passenger...')

// creating icon based on Google Maps API Configuration for icon object
const currentIcon = {
    url: 'https://openmoji.org/data/color/svg/1F698.svg',
    scaledSize: {
        width: 32,
        height: 32
    }
}
// creating icon based on Google Maps API Configuration for icon object
const destinationIcon = {
    url: 'https://openmoji.org/data/color/svg/1F920.svg',
    scaledSize: {
        width: 24,
        height: 24
    }
}

const updateMapBounds = (mapObject) => {
    // create a LatLngBounds object to extend it with the origin and destination points
    let originPoint = new google.maps.LatLng(location.current.geometry)
    let destinationPoint = new google.maps.LatLng(location.destination.geometry)
    let latLngBounds = new google.maps.LatLngBounds()
    // extend the bounds to include the origin and destination points so that driver can see both
    latLngBounds.extend(originPoint)
    latLngBounds.extend(destinationPoint)
    mapObject.fitBounds(latLngBounds)
}

const broadcastDriverLocation = () => {
    // send a request to the server to update the driver's location
    http().post(`/api/trip/${trip.id}/location`, {
        driver_location: location.current.geometry
    })
        .then((response) => { })
        .catch((error) => {
            console.error(error)
        })
}

const handlePassengerPickedUp = () => {
    // send a request to the server to start the trip
    http().post(`/api/trip/${trip.id}/start`)
        .then((response) => {
            title.value = 'Travelling to destination...'
            location.$patch({
                // update the current location to the destination
                destination: {
                    name: response.data.destination_name,
                    geometry: response.data.destination
                }
            })
            // update the trip store now that passanger has been picked up
            trip.$patch(response.data)
        })
        .catch((error) => {
            console.error(error)
        })
}

const handleCompleteTrip = () => {
    // send a request to the server to complete the trip
    http().post(`/api/trip/${trip.id}/end`)
        .then((response) => {
            title.value = 'Trip completed!'
            // update the trip and location store
            trip.$patch(response.data)
            // wait for 3 seconds before redirecting to the standby view
            setTimeout(() => {
                // reset the trip and location store
                trip.reset()
                location.reset()
                // redirect to the standby view
                router.push({
                    name: 'standby'
                })
            }, 3000)
        })
        .catch((error) => {
            console.error(error)
        })
}

onMounted(() => {
    gMap.value.$mapPromise.then((mapObject) => {
        // update the map bounds to include the origin and destination points from ref
        updateMapBounds(mapObject)
        intervalRef.value = setInterval(async () => {
            // update the driver's current position and update map bounds
            await location.updateCurrentLocation()
            // update the driver's position in the database
            broadcastDriverLocation()
            // update the map bounds to include the origin and destination points from ref
            updateMapBounds(mapObject)
        }, 5000)
    })
})

onUnmounted(() => {
    // clear the interval when the component is unmounted
    clearInterval(intervalRef.value)
    intervalRef.value = null
})
</script>