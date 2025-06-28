<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Добавить метеостанцию</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-text>
                            <v-form @submit.prevent="saveStation" ref="form">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.serial_number"
                                            label="Серийный номер"
                                            :rules="[rules.required]"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-select
                                            v-model="form.field_id"
                                            :items="fields"
                                            item-title="name"
                                            item-value="id"
                                            label="Поле"
                                            :rules="[rules.required]"
                                            required
                                        ></v-select>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.api_token"
                                            label="API токен"
                                            :rules="[rules.required]"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-switch
                                            v-model="form.is_active"
                                            label="Активна"
                                        ></v-switch>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12">
                                        <v-textarea
                                            v-model="form.expected_location"
                                            label="Ожидаемое местоположение (JSON)"
                                            hint="Координаты в формате: {lat: 55.7558, lng: 37.6176}"
                                            persistent-hint
                                        ></v-textarea>
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
    name: 'StationsCreate',
    data() {
        return {
            form: {
                serial_number: '',
                field_id: null,
                api_token: '',
                is_active: true,
                expected_location: ''
            },
            fields: [],
            loading: false,
            rules: {
                required: v => !!v || 'Это поле обязательно'
            }
        }
    },
    async mounted() {
        await this.fetchFields()
    },
    methods: {
        async fetchFields() {
            try {
                const response = await axios.get('/api/v1/fields')
                this.fields = response.data
            } catch (error) {
                console.error('Ошибка загрузки полей:', error)
            }
        },
        async saveStation() {
            if (!this.form.serial_number || !this.form.field_id || !this.form.api_token) {
                alert('Пожалуйста, заполните все обязательные поля')
                return
            }

            this.loading = true
            try {
                const data = { ...this.form }
                if (data.expected_location) {
                    try {
                        data.expected_location = JSON.parse(data.expected_location)
                    } catch (e) {
                        data.expected_location = null
                    }
                }

                await axios.post('/api/v1/stations', data)
                this.$router.push({ name: 'stations.index' })
            } catch (error) {
                console.error('Ошибка сохранения станции:', error)
                alert('Ошибка при сохранении станции')
            } finally {
                this.loading = false
            }
        }
    }
}
</script> 