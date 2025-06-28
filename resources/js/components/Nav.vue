<template>
    <v-app-bar app elevation="2">
        <v-container class="d-flex align-center">
            <router-link to="/" class="text-decoration-none">
                <v-toolbar-title class="text-primary font-weight-bold">
                    Meteo El-Agro
                </v-toolbar-title>
            </router-link>
            
            <v-spacer></v-spacer>
            
            <v-btn
                to="/"
                variant="text"
                class="mx-2"
            >
                {{ $t('home') }}
            </v-btn>
            
            <ThemeSwitcher class="mx-2" />
            
            <template v-if="!user?.name">
                <v-btn
                    to="/login"
                    variant="text"
                    class="mx-2"
                >
                    {{ $t('login') }}
                </v-btn>
                <v-btn
                    to="/register"
                    variant="text"
                    class="mx-2"
                >
                    {{ $t('register') }}
                </v-btn>
            </template>
            
            <v-menu v-if="user?.name" location="bottom end">
                <template v-slot:activator="{ props }">
                    <v-btn
                        v-bind="props"
                        variant="text"
                        class="mx-2"
                    >
                        {{ user.name }}
                        <v-icon end>mdi-chevron-down</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item
                        to="/admin"
                        prepend-icon="mdi-view-dashboard"
                    >
                        <v-list-item-title>Admin</v-list-item-title>
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
import useAuth from "@/composables/auth";
import {computed} from "vue";
import {useAuthStore} from "@/store/auth";
import ThemeSwitcher from "./ThemeSwitcher.vue";

const auth = useAuthStore()

const user = computed(() => auth.user);
const { processing, logout } = useAuth();
</script>
