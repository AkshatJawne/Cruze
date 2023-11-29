import {ref, reactive, computed} from 'vue'
import { defineStore } from 'pinia'

export const useTripStore = defineStore('trip', () => {
    //creating refs necessary
    const id = ref(null)
    const user_id = ref(null)
    const destination_name = ref('')
    const is_started = ref(false)
    const is_complete = ref(false)

    // reactive for origin location
    const origin = reactive({
        lat: null, 
        lng: null
    })
    //reactive for destination location
    const destination = reactive({
        lat: null, 
        lng: null
    })
    //reactive for driver info
    const driver = reactive({
        id: null, 
        year: null, 
        make: null, 
        model: null,
        color: null,
        license_plate: null , 
        user: {
            name: null,
        }
    })
    const driver_location = reactive({
        lat: null, 
        lng: null
    })
    // method to reset all values
    const reset = () => {
        id.value = null 
        user_id.value = null
        is_started.value = false
        is_complete.value = false
        destination_name.value = null
        origin.lat = null
        origin.lng = null
        destination.lat = null
        destination.lng = null
        driver.id = null 
        driver.year = null
        driver.make = null
        driver.model = null
        driver.color = null
        driver.license_plate = null
        driver.user.name = null
        driver_location.lat = null
        driver_location.lng = null
    }

    return { id, user_id, origin, destination, driver_location, destination_name, is_started, is_complete, reset }


})