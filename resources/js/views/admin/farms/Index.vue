<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col cols="12" class="d-flex justify-space-between align-center">
                    <h1 class="text-h4">Фермы</h1>
                    <v-btn
                        color="primary"
                        @click="$router.push({ name: 'farms.create' })"
                    >
                        Добавить ферму
                    </v-btn>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-data-table
                                :headers="headers"
                                :items="farms"
                                :loading="loading"
                                :search="search"
                                class="elevation-1"
                            >
                                <template v-slot:top>
                                    <v-text-field
                                        v-model="search"
                                        label="Поиск"
                                        prepend-inner-icon="mdi-magnify"
                                        single-line
                                        hide-details
                                        class="mx-4"
                                    ></v-text-field>
                                </template>

                                <template v-slot:item.actions="{ item }">
                                    <v-btn
                                        icon
                                        small
                                        @click="viewFarm(item)"
                                        class="mr-2"
                                    >
                                        <v-icon>mdi-eye</v-icon>
                                    </v-btn>
                                    <v-btn
                                        icon
                                        small
                                        @click="editFarm(item)"
                                        class="mr-2"
                                    >
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn
                                        icon
                                        small
                                        color="error"
                                        @click="deleteFarm(item)"
                                    >
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </template>

                                <template v-slot:item.fields_count="{ item }">
                                    <v-chip
                                        :color="item.fields_count > 0 ? 'success' : 'grey'"
                                        small
                                    >
                                        {{ item.fields_count }} полей
                                    </v-chip>
                                </template>
                            </v-data-table>
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
    name: 'FarmsIndex',
    data() {
        return {
            farms: [],
            loading: false,
            search: '',
            headers: [
                { title: 'ID', key: 'id', sortable: true },
                { title: 'Название', key: 'name', sortable: true },
                { title: 'Телефон', key: 'contact_phone', sortable: false },
                { title: 'Количество полей', key: 'fields_count', sortable: false },
                { title: 'Действия', key: 'actions', sortable: false }
            ]
        }
    },
    async mounted() {
        await this.fetchFarms()
    },
    methods: {
        async fetchFarms() {
            this.loading = true
            try {
                const response = await axios.get('/api/v1/farms')
                this.farms = response.data.map(farm => ({
                    ...farm,
                    fields_count: farm.fields ? farm.fields.length : 0
                }))
            } catch (error) {
                console.error('Ошибка загрузки ферм:', error)
            } finally {
                this.loading = false
            }
        },
        viewFarm(farm) {
            this.$router.push({ name: 'farms.show', params: { id: farm.id } })
        },
        editFarm(farm) {
            this.$router.push({ name: 'farms.edit', params: { id: farm.id } })
        },
        async deleteFarm(farm) {
            if (confirm(`Вы уверены, что хотите удалить ферму "${farm.name}"?`)) {
                try {
                    await axios.delete(`/api/v1/farms/${farm.id}`)
                    await this.fetchFarms()
                } catch (error) {
                    console.error('Ошибка удаления фермы:', error)
                    alert('Ошибка при удалении фермы')
                }
            }
        }
    }
}
</script> 