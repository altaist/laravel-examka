<template>
  <MainLayout>
    <q-page class="q-pa-md">
      <div class="text-h4 q-mb-lg text-center">Добро пожаловать в сервис написания курсовых работ</div>
      
      <div class="row q-col-gutter-md">
        <div class="col-12">
          <q-card class="q-pa-md">
            <q-card-section>
              <div class="text-h6 q-mb-md">О нашем сервисе</div>
              <p class="text-body1">
                Мы предлагаем профессиональную помощь в написании курсовых работ любой сложности. 
                Наша команда опытных специалистов готова помочь вам с:
              </p>
              <ul class="q-ml-md">
                <li>Написанием курсовых работ</li>
                <li>Подбором и анализом литературы</li>
                <li>Оформлением работы по ГОСТ</li>
                <li>Подготовкой презентаций</li>
                <li>Консультациями по защите</li>
              </ul>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 text-center q-mt-lg">
          <q-btn
            color="primary"
            size="lg"
            label="Создать запрос"
            @click="router.visit('/start')"
            class="q-mr-md"
          />
          <q-btn
            color="secondary"
            size="lg"
            label="Личный кабинет"
            @click="router.visit('/lk')"
          />
        </div>
      </div>
    </q-page>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue'
import { router } from '@inertiajs/vue3'
</script> 