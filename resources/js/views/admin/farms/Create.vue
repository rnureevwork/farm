<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Добавить ферму</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-text>
                            <v-form @submit.prevent="saveFarm" ref="form">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.name"
                                            label="Название фермы"
                                            :rules="[rules.required]"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.contact_phone"
                                            label="Контактный телефон"
                                            hint="+7 (999) 123-45-67"
                                            persistent-hint
                                        ></v-text-field>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" class="d-flex justify-end">
                                        <v-btn
                                            text
                                            @click="$router.go(-1)"
                                            class="mr-4"
                                        >
                                            Отмена
                                        </v-btn>
                                        <v-btn
                                            color="primary"
                                            type="submit"
                                            :loading="loading"
                                        >
                                            Сохранить
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'FarmsCreate',
    data() {
        return {
            form: {
                name: '',
                contact_phone: ''
            },
            loading: false,
            rules: {
                required: v => !!v || 'Это поле обязательно'
            }
        }
    },
    methods: {
        async saveFarm() {
            if (!this.form.name) {
                alert('Пожалуйста, заполните название фермы')
                return
            }
            this.loading = true
            try {
                await axios.post('/api/v1/farms', this.form)
                this.$router.push({ name: 'farms.index' })
            } catch (error) {
                console.error('Ошибка сохранения фермы:', error)
                alert('Ошибка при сохранении фермы')
            } finally {
                this.loading = false
            }
        }
    }
}
</script> 