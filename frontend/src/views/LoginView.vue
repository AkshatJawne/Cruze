<template>
    <div class = "pt-16">
        <h1 class = "text-4xl font-bold mb-8">Welcome to Cruze</h1>
        <h2 class = "text-3xl font-semibold my-8">Enter your phone number</h2>
        <!-- only show this form if waitingOnVerification is false, which means user has not logged in -->
        <form v-if = "!waitingOnVerification" action = "#" @submit.prevent="handleLogin">
            <div class = "overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class = "bg-white px-4 py-5 sm:p-6">
                    <div>
                        <!-- use v-maska and data-maska to ensure valid data input -->
                        <input type="text" v-maska data-maska = '# (###)-###-####' name = "phone" id = "phone" 
                        v-model = "credentials.phone" placeholder="1 (234)-567-8910"
                        class = "mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm"/>
                    </div>
                </div>
                <div class = "bg-gray-50 px-4 py-3 text-right sm:p-6">
                    <button type = "submit" @submit.prevent = "handleLogin" class = "inline-flex justify-center rounded-md border border-transparent bg-black
                    py-2 text-white px-4">Continue</button>
                </div>
            </div>
        </form>
        <form v-else action = "#" @submit.prevent="handleVerification">
                <div class = "overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                    <div class = "bg-white px-4 py-5 sm:p-6">
                        <div>
                        <!-- use v-maska and data-maska to ensure valid data input -->
                            <input type="text" v-maska data-maska = '#####' name = "login_code" id = "login_code" 
                            v-model = "credentials.login_code" placeholder="1 (234)-567-8910"
                            class = "mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm"/>
                        </div>
                    </div>
                    <div class = "bg-gray-50 px-4 py-3 text-right sm:p-6">
                        <button type = "submit" @submit.prevent = "handleLogin"  class = "inline-flex justify-center rounded-md border border-transparent bg-black
                        py-2 text-white px-4">Verify</button>
                    </div>
                </div>
            </form>
    </div>
</template>

<script setup> 
import {vMaska} from 'maska'
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios' 
import { useRouter } from 'vue-router';

//router to help programatically navigate to other pages
const router = useRouter()

//reactive credentials object to store user information
const credentials  = reactive({
    phone: null, 
    login_code: null
})

const waitingOnVerification = ref(false)

onMounted(() => {
    if (localStorage.getItem('token')) { // if the user has already logged in before, redirect them to the landing page
        router.push({
            name: 'landing' // route that allows user to determine whether they will be a user or a driver
        })
    }
})

const getFormatedCredentials = () => {
    //format the user's phone number and login code to be sent to the backend
    return {
        phone: credentials.phone.replace('( ', '').replace(')', '').replaceAll('-', '').replaceAll(' ', ''),
        login_code: credentials.login_code
    }
}

const handleLogin = () => { 
    //make a post request to the backend to send a verification code to the user
     axios.post('http://localhost:8000/api/login', getFormatedCredentials).then((response) => {
        console.log(response.data)
        waitingOnVerification.value = true
     }).catch((error) => {
        console.error(error)
        alert(error.response.data.message)
     })
}

const handleVerification = () => {
    //make a post request to the backend to verify the user's login code
    axios.post('https://localhost:8000/api/login', getFormatedCredentials).then((response) => {
        console.log(response)
        localStorage.setItem('token', response.data)
        router.push({
            name: 'landing' // route that allows user to determine whether they will be a user or a driver
        })
    }).catch((error) => {
        console.error(error)
        alert(error.response.data.message)
    })
}
</script>
