<template>
    <v-app>
        <v-navigation-drawer
            v-model="drawer"
            :rail="rail"
            permanent
            @click="rail = false"
        >
            <v-list-item
                prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
                :title="user?.name || 'Admin'"
                nav
            >
                <template v-slot:append>
                    <v-btn
                        variant="text"
                        icon="mdi-chevron-left"
                        @click.stop="rail = !rail"
                    ></v-btn>
                </template>
            </v-list-item>

            <v-divider></v-divider>

            <v-list density="compact" nav>
                <v-list-item
                    to="/admin/dashboard"
                    prepend-icon="mdi-chart-line"
                    title="Метео дашборд"
                    value="meteo-dashboard"
                ></v-list-item>
                
                <v-list-item
                    to="/admin/farms"
                    prepend-icon="mdi-domain"
                    title="Фермы"
                    value="farms"
                ></v-list-item>
                
                <v-list-item
                    to="/admin/stations"
                    prepend-icon="mdi-weather-partly-cloudy"
                    title="Метеостанции"
                    value="stations"
                ></v-list-item>
                
                <v-list-item
                    to="/admin/fields"
                    prepend-icon="mdi-map-marker"
                    title="Поля"
                    value="fields"
                ></v-list-item>
                
                <v-list-item
                    to="/admin/crops"
                    prepend-icon="mdi-sprout"
                    title="Культуры"
                    value="crops"
                ></v-list-item>
                
                <v-list-item
                    to="/admin/alerts"
                    prepend-icon="mdi-alert"
                    title="Уведомления"
                    value="alerts"
                ></v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-main>
            <v-container fluid>
                <v-breadcrumbs :items="breadcrumbs" class="mb-4"></v-breadcrumbs>
                
                <v-card>
                    <v-card-text>
                        <Suspense>
                            <router-view></router-view>
                        </Suspense>
                    </v-card-text>
                </v-card>
            </v-container>
        </v-main>
    </v-app>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/store/auth";

const route = useRoute();
const authStore = useAuthStore();
const drawer = ref(true);
const rail = ref(false);

const user = computed(() => authStore.user);

const breadcrumbs = computed(() => {
    let pathArray = route.path.split('/')
    pathArray.shift()
    const breadCrumbs = [{ title: 'Dashboard', to: '/admin' }]
    
    let breadcrumb = ''
    let lastIndexFound = 0
    for (let i = 0; i < pathArray.length; ++i) {
        breadcrumb = `${breadcrumb}${'/'}${pathArray[i]}`
        if (route.matched[i] &&
            Object.hasOwnProperty.call(route.matched[i], 'meta') &&
            Object.hasOwnProperty.call(route.matched[i].meta, 'breadCrumb')) {
            breadCrumbs.push({
                title: route.matched[i].meta.breadCrumb || pathArray[i],
                to: breadcrumb,
                disabled: i + 1 === pathArray.length
            })
            lastIndexFound = i
            breadcrumb = ''
        }
    }
    return breadCrumbs
});
</script>

<style scoped>
/* Стили теперь управляются Vuetify */
</style>
