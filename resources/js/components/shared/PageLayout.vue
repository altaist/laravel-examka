<template>
    <div>
        <page-header
            :title="title"
            :left-btn-icon="leftBtnIcon"
            :left-btn-route="leftBtnRoute"
            :left-btn-go-back="leftBtnGoBack"
            :right-btn-icon="rightBtnIcon"
            :right-btn-route="rightBtnRoute"

            @click:left="emit('click:header:left')"
            @click:right="emit('click:header:right')"
            />

        <page-container class="q-px-md" style="margin-bottom: 100px;">    
            <slot/>
        </page-container>

        <page-footer 
            :title="footerText"
            :is-sticky="true"
            :menu="footerMenu || localMenu"

            @click:left="emit('click:footer:left')"
            @click:right="emit('click:footer:right')"
            @click:menu="emit('click:footer:menu', $event)"
        />
    </div>
</template>
<script setup>
import PageContainer from '@/components/shared/PageContainer.vue';


const props = defineProps({
    title: {
        type: String,
        default: ''
    },
    footerText: {
        type: String,
        default: ''
    },
    footerMenu: {
        type: Array,
        default: null
    },

    leftBtnIcon: {
        type: String,
        default: "fa-solid fa-home"
    },
    leftBtnRoute: {
        type: String
    },
    leftBtnGoBack: {
        type: Boolean,
        default: true
    },
    rightBtnIcon: {
        type: String,
        default: 'fa-solid fa-user'
    },
    rightBtnRoute: {
        type: String,
        default: 'dashboard'
    },
});

const emit = defineEmits(['click:header:left', 'click:header:right', 'click:header:title', 'click:footer:left', 'click:footer:right', 'click:footer:title', 'click:footer:menu']);

const model = defineModel({
    type: String,
});

const localMenu = [
    {
        id: 1,
        label: 'Menu1',
        icon: 'fa-solid fa-home'
    },
    {
        id: 2,
        label: 'Menu2',
        icon: 'fa-solid fa-shop'
    },
    {
        id: 3,
        label: 'Menu3',
        icon: 'fa-solid fa-user'
    }
];

</script>
