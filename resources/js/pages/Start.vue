<template>
  <MainLayout>
    <q-page class="q-pa-md">
      <div class="text-h4 q-mb-lg text-center">Создать запрос</div>
      <div class="row q-col-gutter-md">
        <div class="col-12">
          <q-card class="q-pa-md">
            <q-card-section>
              <q-input
                v-model="requestText"
                label="Введите ваш запрос"
                type="textarea"
                rows="4"
                outlined
              />
              <div class="text-center q-mt-md">
                <q-btn
                  color="primary"
                  label="Отправить"
                  @click="submitRequest"
                />
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </q-page>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const requestText = ref('')

const submitRequest = () => {
  // Здесь можно добавить логику отправки запроса
  console.log('Отправка запроса:', requestText.value)
  // Например, перенаправление на страницу личного кабинета после отправки
  router.visit('/lk')
}
</script> 