<template>
    <div class=" flex flex-col gap-4 col-span-2 max-w-lg">
        <div class=" pb-4 border-b flex flex-col gap-2">
            <div class="text-2xl font-semibold">
                Основная информация
            </div>
            <div class=" flex flex-col gap-2">
                <div class=" font-semibold grid grid-cols-2">
                    <span class="text-lg">
                        Телефон:
                    </span>
                    <span>
                        {{ contract.phone }}
                    </span>
                </div>
                <div class=" font-semibold grid grid-cols-2">
                    <span class="text-lg">
                        Цена:
                    </span>
                    <span>
                        {{ contract.price }}
                    </span>
                </div>
                <div class=" font-semibold grid grid-cols-2">
                    <span class="text-lg">
                        Скидка:
                    </span>
                    <span>
                        {{ contract.sale }}
                    </span>
                </div>
                <div class=" font-semibold grid grid-cols-2">
                    <span class="text-lg">
                        Дата создания:
                    </span>
                    <span>
                        {{ contract.created_at }}
                    </span>
                </div>
                <div v-if="contract.parent" class=" font-semibold grid grid-cols-2">
                    <span class="text-lg">
                        Родительский договор:
                    </span>
                    <Link class=" text-blue-400 hover:text-blue-500"
                        :href="route('admin.contract.show', contract.parent.id)">
                    № {{ contract.parent.number }}
                    </Link>
                </div>
            </div>
        </div>
        <div v-if="contract.client" class=" pb-4 border-b flex flex-col gap-2">
            <div class="text-2xl font-semibold">
                Информация о клиенте
            </div>
            <div class=" flex flex-col gap-2">
                <div class=" font-semibold grid grid-cols-2">
                    <span class="text-lg">
                        Тип:
                    </span>
                    <span>
                        {{ contract.client.type == 0 ? 'Физическое лицо' : 'Юридическое лицо' }}
                    </span>
                </div>
                <template v-if="contract.client.type == 0">
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            ФИО:
                        </span>
                        <span>
                            {{ contract.client.fio }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            Данные паспорта:
                        </span>
                        <span>
                            {{ contract.client.passport_series }} {{ contract.client.passport_number }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            Кем выдан:
                        </span>
                        <span>
                            {{ contract.client.passport_issued }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            Адресс:
                        </span>
                        <span>
                            {{ contract.client.physical_address }}
                        </span>
                    </div>
                </template>
                <template v-else>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            Краткое наименование организации:
                        </span>
                        <span>
                            {{ contract.client.organization_short_name }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            Полное наименование организации:
                        </span>
                        <span>
                            {{ contract.client.organization_name }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            Номер ОГРН/ОГРНИП:
                        </span>
                        <span>
                            {{ contract.client.register_number }}
                        </span>
                    </div>
                    <div v-if="contract.client.director_name" class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            ФИО Ген.дира:
                        </span>
                        <span>
                            {{ contract.client.director_name }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            Юридический адресс:
                        </span>
                        <span>
                            {{ contract.client.legal_address }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            ИНН/КПП:
                        </span>
                        <span>
                            {{ contract.client.inn }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            Расчётный счёт:
                        </span>
                        <span>
                            {{ contract.client.current_account }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            Корреспондентский счёт:
                        </span>
                        <span>
                            {{ contract.client.correspondent_account }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            Наименование банка:
                        </span>
                        <span>
                            {{ contract.client.bank_name }}
                        </span>
                    </div>
                    <div class=" font-semibold grid grid-cols-2">
                        <span class="text-lg">
                            БИК Банка:
                        </span>
                        <span>
                            {{ contract.client.bank_bik }}
                        </span>
                    </div>
                </template>
            </div>
        </div>
        <div class=" pb-4 border-b flex flex-col gap-2">
            <div class="text-2xl font-semibold mb-2">
                Услуги
            </div>
            <div class=" flex flex-col gap-2">
                <div v-for="(service, key) in contract.services" class=" font-semibold grid grid-cols-2">
                    <span class="text-lg">
                        Услуга №{{ key + 1 }}:
                    </span>
                    <span>
                        {{ service.name }} <br>Цена {{ service.price }}
                    </span>
                </div>
            </div>
        </div>
        <div class=" pb-4 border-b flex flex-col gap-2">
            <div class="text-2xl font-semibold mb-2">
                Платежи
            </div>
            <div class=" flex flex-col gap-2">
                <div v-for="(payment, key) in contract.payments" class=" font-semibold grid grid-cols-2">
                    <span class="text-lg">
                        Платёж №{{ key + 1 }}:
                    </span>
                    <Link class=" text-blue-400 hover:text-blue-500" :class="payment.is_close ? ' text-green-400' : ''"
                        :href="route('admin.payment.show', payment.id)">
                    {{ payment.value }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        contract: {
            type: Object,
            required: true,
        }
    }
}
</script>