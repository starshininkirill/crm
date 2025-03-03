<template>
    <SettingsLayout>

        <Head title="Основные настройки" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Основные настройки</h1>

            <div class="grid grid-cols-2 gap-4 gap-y-7">
                <CategoryForm :title="'Основные категории услуг (Первая услуга в генераторе документов)'"
                    :categories="serviceCategories" :optionObject="mainCategoriesOption"
                    name="contract_generator_main_categories" />

                <CategoryForm :title="'Дополнительные категории услуг ( 2 и далее услуги в генераторе документов )'"
                    :categories="serviceCategories" :optionObject="secondaryCategoriesOption"
                    name="contract_generator_secondary_categories" />

                <CategoryForm :title="'Услуги, которым нужно кол-во страниц'" :categories="services"
                    :optionObject="needSeoPages" name="contract_generator_need_seo_pages" />

                <div>
                    <div class="text-xl font-semibold mb-3">
                        НДС
                    </div>
                    <form @submit.prevent="submitForm(ndsForm, 'nds')">
                        <FormInput v-model="ndsForm.value" type="number" name="value" placeholder="20%"
                            label="Ставка НДС (%)" required />
                        <button class="btn mt-3">Изменить</button>
                    </form>
                </div>

                <div>
                    <div class="text-xl font-semibold mb-3">
                        Шаблон для Юр лица генератора Платежей по умолчанию
                    </div>
                    <form @submit.prevent="submitForm(defaultLawTemplateForm, 'lawTemplate')">
                        <FormInput v-model="defaultLawTemplateForm.value" type="number" name="value" placeholder="198"
                            label="Шаблон по умолчанию" required />
                        <button class="btn mt-3">Изменить</button>
                    </form>
                </div>

                <div>
                </div>

                <div>
                    <div class="text-xl font-semibold mb-3">
                        Описание условий для сделки с РК
                    </div>
                    <form @submit.prevent="submitForm(contractRkTextForm, 'contractRkText')">
                        <Editor v-model="contractRkTextForm.value" :init="editorConfig" />
                        <button class="btn mt-3">Изменить</button>
                    </form>
                </div>
                <div>
                    <div class="text-xl font-semibold mb-3">
                        ID Шаблонов для генератора договора
                    </div>
                </div>

            </div>
        </div>
    </SettingsLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import SettingsLayout from '../Layouts/SettingsLayout.vue';
import CategoryForm from '../../../Components/OptionCategoryForm.vue';
import FormInput from '../../../Components/FormInput.vue';
import Editor from '@tinymce/tinymce-vue';
import { editorConfig } from '../../../utils/editorConfig';

export default {
    components: {
        Head,
        CategoryForm,
        FormInput,
        Editor,
        SettingsLayout
    },
    props: {
        serviceCategories: Array,
        services: Array,
        mainCategoriesOption: Object,
        secondaryCategoriesOption: Object,
        needSeoPages: Object,
        taxNds: Object,
        paymentDefaultLawTemplate: Object,
        contractRkText: Object,
    },
    setup(props) {
        const ndsForm = useForm({
            'name': 'tax_nds',
            'value': props.taxNds?.value || 0,
            'object': props.taxNds,
        });

        const defaultLawTemplateForm = useForm({
            'name': 'payment_generator_default_law_template',
            'value': props.paymentDefaultLawTemplate?.value || 0,
            'object': props.paymentDefaultLawTemplate,
        });

        const contractRkTextForm = useForm({
            'name': 'contract_generator_rk_text',
            'value': props.contractRkText?.value || '',
            'object': props.contractRkText,
        });


        const submitForm = (submitionForm, formType) => {
            if (Boolean(submitionForm.object?.value)) {
                submitionForm.put(route('option.update', { option: submitionForm.object.id }));
            } else {
                submitionForm.post(route('option.store'), {
                    onFinish: () => {
                        if (formType === 'nds') {
                            submitionForm.object = props.taxNds;
                        } else if (formType == 'lawTemplate') {
                            submitionForm.object = props.paymentDefaultLawTemplate;
                        } else if (formType == 'contractRkText') {
                            submitionForm.object = props.contractRkText;
                        }
                    },
                });
            }
        };

        return {
            ndsForm,
            defaultLawTemplateForm,
            contractRkTextForm,
            submitForm,
            editorConfig
        };
    }
}


</script>