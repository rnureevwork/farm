<template>
    <v-app-bar app elevation="2" color="primary" dark>
        <v-container class="d-flex align-center">
            <router-link to="/" class="text-decoration-none">
                <v-toolbar-title class="font-weight-bold">
                    Meteo El-Agro
                </v-toolbar-title>
            </router-link>
            
            <v-spacer></v-spacer>
            
            <ThemeSwitcher class="mx-2" />
            
            <v-menu location="bottom end">
                <template v-slot:activator="{ props }">
                    <v-btn
                        v-bind="props"
                        variant="text"
                        class="mx-2"
                    >
                        Hi, {{ user.name }}
                        <v-icon end>mdi-chevron-down</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item
                        :to="{ name: 'profile.index' }"
                        prepend-icon="mdi-account"
                    >
                        <v-list-item-title>Profile</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        prepend-icon="mdi-cog"
                    >
                        <v-list-item-title>Setting</v-list-item-title>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item
                        @click="logout"
                        prepend-icon="mdi-logout"
                        :disabled="processing"
                    >
                        <v-list-item-title>Logout</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-container>
    </v-app-bar>
</template>

<script setup>
import {computed} from "vue";
import useAuth from "@/composables/auth";
import {useAuthStore} from "@/store/auth";
import ThemeSwitcher from "../../components/ThemeSwitcher.vue";

const auth = useAuthStore()

const user = computed(() => auth.user)
const {processing, logout} = useAuth();
</script>

<style scoped>

</style>
