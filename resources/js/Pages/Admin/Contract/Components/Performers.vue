<template>
    <form @submit.prevent="sumbitForm" class="flex flex-col gap-3">
        <Error />

        <div class="text-2xl font-semibold">
            Исполнители:
        </div>

        <div v-for="(role, roleIndex) in form.performersData" :key="roleIndex" class="flex flex-col gap-2">
            <div class="font-semibold">
                {{ role.name }}
            </div>
            <div v-for="(performer, performerIndex) in role.performers" :key="performerIndex"
                class="flex gap-3 items-center w-full">
                <VueSelect v-model="form.performersData[roleIndex].performers[performerIndex].id" :options="users"
                    :reduce="user => user.id" label="full_name" class="full-vue-select" />
                <div @click="removePerformer(roleIndex, performerIndex)"
                    class="flex items-center justify-center rounded-full w-8 h-8 bg-red-400 flex-shrink-0 cursor-pointer">
                    <span class="w-1/4 h-0.5 bg-white"></span>
                </div>
            </div>
            <div class="text-sm text-green-500 font-semibold cursor-pointer" @click="addPerformer(roleIndex)">
                Добавить исполнителя
            </div>
        </div>
        <button type="submit" class="btn">
            Сохранить
        </button>
    </form>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import Error from '../../../../Components/Error.vue';


export default {
    components: {
        VueSelect,
        Error
    },
    props: {
        contract: {
            type: Object,
            required: true,
        },
        users: {
            type: Array,
            required: true,
        },
    },
    data() {
        let performersData = this.contract.performers.map(function (role) {
            let performers = role.performers

            if (!role.performers.length) {
                performers = [
                    {
                        'id': null,
                        'full_name': null
                    }
                ]
            } else {
                performers = role.performers.map(function (performer) {
                    return {
                        'id': performer.id,
                        'full_name': performer.full_name
                    }
                })
            }

            return {
                id: role.id,
                name: role.name,
                performers
            }
        })

        let form = useForm({
            performersData
        })
        return {
            form
        }
    },
    methods: {
        sumbitForm() {
            this.form.transform(data => ({
                performersData: data.performersData.map(role => ({
                    id: role.id,
                    performers: role.performers
                        .filter(performer => performer.id != null)
                        .map(performer => performer.id),
                })),
            })).post(route('admin.contract.attach-performer', this.contract.id));
        },
        addPerformer(roleIndex) {
            this.form.performersData[roleIndex].performers.push({
                'id': null,
                'full_name': null
            })
        },
        removePerformer(roleIndex, performerIndex) {
            if (
                this.form.performersData[roleIndex] &&
                this.form.performersData[roleIndex].performers &&
                performerIndex >= 0 &&
                performerIndex < this.form.performersData[roleIndex].performers.length
            ) {
                this.form.performersData[roleIndex].performers.splice(performerIndex, 1);
            }
        }
    }
}

</script>