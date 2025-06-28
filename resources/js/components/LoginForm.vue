<template>
    <v-container>
        <v-row justify="center" class="my-5">
            <v-col cols="12" md="6">
                <v-card class="pa-4">
                    <v-card-title class="text-center">{{ $t('login') }}</v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="submitLogin">
                            <v-text-field
                                v-model="loginForm.email"
                                :label="$t('email')"
                                type="email"
                                autofocus
                                autocomplete="username"
                                :error-messages="validationErrors?.email"
                            ></v-text-field>
                            <v-text-field
                                v-model="loginForm.password"
                                :label="$t('password')"
                                type="password"
                                autocomplete="current-password"
                                :error-messages="validationErrors?.password"
                            ></v-text-field>
                            <v-checkbox
                                v-model="loginForm.remember"
                                :label="$t('remember_me')"
                            ></v-checkbox>
                            <v-btn
                                type="submit"
                                color="primary"
                                block
                                :loading="processing"
                                :disabled="processing"
                            >
                                {{ $t('login') }}
                            </v-btn>
                        </v-form>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <router-link :to="{name: 'auth.forgot-password'}">{{ $t('forgot_password') }}</router-link>
                        </v-card-actions>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import useAuth from '@/composables/auth'

export default {
    name: 'LoginForm',
    data() {
        return {
            ...useAuth()
        }
    },
    methods: {
        submitLogin() {
            this.submitLogin()
        }
    }
}
</script> 