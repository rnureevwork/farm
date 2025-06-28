<template>
    <v-app>
        <div v-if="debug" style="padding: 20px; background: #f0f0f0;">
            <h3>Debug Info:</h3>
            <p>App loaded successfully!</p>
            <p>Current route: {{ $route.path }}</p>
            <p>Show navbar: {{ showNavbar }}</p>
        </div>
        <Nav v-if="showNavbar" />
        <v-main>
            <router-view />
        </v-main>
    </v-app>
</template>

<script>
import Nav from './components/Nav.vue'
import useAuth from './composables/auth'

export default {
    name: 'App',
    components: {
        Nav
    },
    data() {
        return {
            debug: false // Отключаем отладочную информацию
        }
    },
    computed: {
        showNavbar() {
            // Скрываем навигацию только в админке
            const isAdminRoute = this.$route.path.startsWith('/admin')
            return !isAdminRoute
        }
    },
    created() {
        console.log('App.vue created')
        useAuth().getUser()
    },
    mounted() {
        console.log('App.vue mounted')
    }
}
</script> 