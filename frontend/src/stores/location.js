import { reactive } from 'vue'
import { defineStore } from 'pinia'

const getUserLocation = async () => {
    return new Promise((resolve, reject ) => {
        navigator.geolocation.getCurrentPosition(resolve, reject)
    })
}

export const useLocationStore = defineStore('location ', () => {
    // creating reactive object for destiantion to access across multiple components
    const destination = reactive({
        name: '', 
        address: '', 
        geometry: {
            lat: null, 
            lng: null, 
        }
    })

    const current = reactive({
        geometry: {
            lat: null, 
            lng: null,
        }
    })

    const updateCurrentLocation = async () => {
        const userLocation = await getUserLocation()
        current.geometry = {
            lat: userLocation.coords.latitude,
            lng: userLocation.coords.longitude
        } 
    }
  return { destination, current, updateCurrentLocation }
})  