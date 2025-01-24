<template>
    <div class="flex flex-col w-full mb-6">
        <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div class="bg-gray-800 p-2 rounded-md text-white font-semibform text-xl">
                Контрагент
            </div>
            <div class="flex flex-col gap-4 p-2 mt-2">
                <div class="flex flex-col gap-2">
                    <div class="text-xl font-semibform">Контрагент</div>

                    <!-- Выбор физ/юр лица -->
                    <div class="grid grid-cols-2 w-fit gap-5">
                        <label class="cursor-pointer">
                            <input type="radio" value="0" v-model="form.clientType" name="client_type" /> Физическое
                            лицо
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" value="1" v-model="form.clientType" name="client_type" /> Юридическое
                            лицо
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        form: {
            type: Object,
            required: true,
        },
        valid: {
            type: Boolean,
            required: true,
        },
    }, 
    
};
</script>