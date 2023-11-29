<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">Here's your trip</h1>
        <div>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapMap v-if="location.destination.name !== ''" :zoom="13" :center="location.destination.geometry"
                            ref="gMap"
                            style="width: 100%; height: 256px;">
                            <!-- <GMapMarker :position ="location.destination.geometry"/>  -->
                        </GMapMap>
                    </div>
                    <div class="mt-2">
                        <p class="text-xl">Going to <strong>{{ location.destination.name }}</strong></p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button
                        @click="handleConfirmTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        Let's Go!</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useLocationStore } from '@/stores/location'
import { useTripStore } from '@/stores/trip'
import { onMounted } from 'vue';
import { useRouter } from 'vue-router'
import { ref } from 'vue'
import http from '@/helpers/http'

const location = useLocationStore()
const trip = useTripStore()

const router = useRouter()

const gMap = ref(null)

onMounted(async () => {
    if (location.destination.name === '') {
        router.push({
            name: 'location'
        })
    }

    // lets get the users current location
    await location.updateCurrentLocation()

    // draw a path on the map from current location to destination
    gMap.value.$mapPromise.then((mapObject) => {
        let currentPoint = new google.maps.LatLng(location.origin.geometry),
            destinationPoint = new google.maps.LatLng(location.destination.geometry),
            directionsService = new google.maps.DirectionsService,
            directionsDisplay = new google.maps.DirectionsRenderer({
                map: mapObject
            })
        directionsService.route({
            origin: currentPoint,
            destination: destinationPoint,
            avoidTolls: false,
            avoidHighways: false,
            travelMode: google.maps.TravelMode.DRIVING
        }, (res, status) => {
            if (status === google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(res)
            } else {
                console.error(status)
            }
        })
    })
})

const handleConfirmTrip = () => {
    http().post('/api/trip', {
        origin: location.current.geometry, 
        destination: location.destination.geometry, 
        destinationName: location.destination.name 
    }).then((response) => {
        trip.$patch(response.data)
        router.push({
            name: 'trip'
        })
    }).catch((error) => {
        console.error(error)
    })
}

</script>