import 'primeicons/primeicons.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import 'primeflex/primeflex.css'
import './assets/css/main.css'
import './assets/css/base.scss'



const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(PrimeVue, {
    theme: {
        preset: Aura,
        dark : false,
        options: {
            dark: false
        }
    },
    ripple: true,
    locale: {
        accept: 'تأیید',
        reject: 'رد',
        choose: 'انتخاب',
        upload: 'آپلود',
        cancel: 'انصراف',
        dayNames: ["یکشنبه","دوشنبه","سه‌شنبه","چهارشنبه","پنج‌شنبه","جمعه","شنبه"],
        dayNamesShort: ["ی","د","س","چ","پ","ج","ش"],
        monthNames: [
            "ژانویه","فوریه","مارس","آوریل","مه","ژوئن",
            "ژوئیه","اوت","سپتامبر","اکتبر","نوامبر","دسامبر"
        ],
        monthNamesShort: [
            "ژان","فور","مار","آور","مه","ژوئن",
            "ژوئی","اوت","سپت","اکت","نوا","دسا"
        ],
        firstDayOfWeek: 6, // هفته از شنبه شروع بشه
        today: 'امروز',
        clear: 'پاک کردن',
    }
})
app.mount('#app')
