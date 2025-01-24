<template>

    <Head title="Генератор документов" />

    <h1 class=" text-4xl font-bold mb-5">
        Создание Договора
    </h1>

    <form ref="form" method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-2 gap-4 max-w-xl mb-6">
            <FormInput v-model="form.leed" type="number" name="leed" placeholder="Лид" label="Лид" required />
            <FormInput v-model="form.number" type="number" name="number" placeholder="Номер договора"
                label="Номер договора" required />
            <FormInput v-model="form.contact_fio" type="text" name="contact_fio" placeholder="ФИО представителя"
                label="ФИО представителя" required />
            <FormInput v-model="form.contact_phone" type="tel" name="contact_phone" placeholder="Телефон"
                label="Телефон" required />
        </div>
        <div>
            <div v-show="currentStep === 1">
                <AgentInfo :form="form" v-model:valid="stepsValid[0]" />
            </div>
        </div>

    </form>


</template>
<script>
import { Head, useForm } from '@inertiajs/vue3';
import LkLayout from '../../../Layouts/LkLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import AgentInfo from './AgentInfo.vue';

export default {
    components: {
        Head,
        FormInput,
        AgentInfo
    },
    props: {
        props: {
            cats: {
                type: Array,
            },
            mainCats: {
                type: Array,
            },
            secondaryCats: {
                type: Array,
            },
            rkText: {
                type: String,
            },
            // link: {
            //     type: String,
            // },
            // file: {
            //     type: String,
            // }
        },
    },
    layout: LkLayout,

    data() {
        return {
            stepsValid: [false, false],
            currentStep: 1,
        };
    },

    setup(props) {
        const form = useForm({
            'leed': null,
            'number': null,
            'contact_fio': null,
            'contact_phone': null
        })

        return {
            form
        }
    },
}


</script>