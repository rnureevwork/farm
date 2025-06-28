<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Редактировать поле</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-text>
                            <v-form @submit.prevent="updateField" ref="form">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.name"
                                            label="Название поля"
                                            :rules="[rules.required]"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-select
                                            v-model="form.farm_id"
                                            :items="farms"
                                            item-title="name"
                                            item-value="id"
                                            label="Ферма"
                                            :rules="[rules.required]"
                                            required
                                        ></v-select>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.area_hectares"
                                            label="Площадь (га)"
                                            type="number"
                                            :rules="[rules.required, rules.positive]"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-textarea
                                            v-model="form.boundary"
                                            label="Границы (JSON массив координат)"
                                            hint="Пример: [[55.75,37.61],[55.76,37.62]]"
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
    name: 'FieldsEdit',
    data() {
        return {
            form: {
                name: '',
                farm_id: null,
                area_hectares: '',
                boundary: ''
            },
            farms: [],
            loading: false,
            rules: {
                required: v => !!v || 'Это поле обязательно',
                positive: v => v > 0 || 'Значение должно быть больше 0'
            }
        }
    },
    async mounted() {
        await this.fetchFarms()
        await this.fetchField()
    },
    methods: {
        async fetchFarms() {
            try {
                const response = await axios.get('/api/v1/farms')
                this.farms = response.data
            } catch (error) {
                console.error('Ошибка загрузки ферм:', error)
            }
        },
        async fetchField() {
            this.loading = true
            try {
                const response = await axios.get(`/api/v1/fields/${this.$route.params.id}`)
                const data = response.data
                this.form.name = data.name
                this.form.farm_id = data.farm_id
                this.form.area_hectares = data.area_hectares
                this.form.boundary = data.boundary ? JSON.stringify(data.boundary) : ''
            } catch (error) {
                console.error('Ошибка загрузки поля:', error)
                alert('Ошибка загрузки поля')
            } finally {
                this.loading = false
            }
        },
        async updateField() {
            if (!this.form.name || !this.form.farm_id || !this.form.area_hectares) {
                alert('Пожалуйста, заполните все обязательные поля')
                return
            }
            this.loading = true
            try {
                const data = { ...this.form }
                if (data.boundary) {
                    try {
                        // Если boundary - строка, пытаемся распарсить как JSON
                        if (typeof data.boundary === 'string') {
                            data.boundary = JSON.parse(data.boundary)
                        }
                        // Если boundary уже массив, оставляем как есть
                    } catch (e) {
                        console.warn('Ошибка парсинга boundary:', e)
                        data.boundary = null
                    }
                }
                await axios.put(`/api/v1/fields/${this.$route.params.id}`, data)
                this.$router.push({ name: 'fields.index' })
            } catch (error) {
                console.error('Ошибка сохранения поля:', error)
                alert('Ошибка при сохранении поля')
            } finally {
                this.loading = false
            }
        }
    }
}
</script> 