<template>
    <v-btn
        icon
        @click="toggleTheme"
        :title="isDark ? 'Переключить на светлую тему' : 'Переключить на темную тему'"
    >
        <v-icon>
            {{ isDark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}
        </v-icon>
    </v-btn>
</template>

<script>
export default {
    name: 'ThemeSwitcher',
    data() {
        return {
            isDark: false
        }
    },
    mounted() {
        // Проверяем сохраненную тему
        const savedTheme = localStorage.getItem('theme')
        if (savedTheme) {
            this.isDark = savedTheme === 'dark'
        } else {
            // Проверяем системную тему
            this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        }
        this.applyTheme()
    },
    methods: {
        toggleTheme() {
            this.isDark = !this.isDark
            this.applyTheme()
            localStorage.setItem('theme', this.isDark ? 'dark' : 'light')
        },
        applyTheme() {
            // Применяем тему к Vuetify
            this.$vuetify.theme.global.name = this.isDark ? 'dark' : 'light'
            
            // Применяем тему к body для Bootstrap
            document.body.setAttribute('data-bs-theme', this.isDark ? 'dark' : 'light')
        }
    }
}
</script> 