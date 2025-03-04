<template v-if="breaktime != null">
    <div class=" flex gap-3 items-center">
        <div class=" text-white">
            Использовано перерывов: {{ formatedBreaktime }}
        </div>
        <div v-if="currentAction == 'end'" @click="start" class="btn-violet">
            Начать
        </div>
        <div class="flex gap-3">
            <div v-if="currentAction == 'start' || currentAction == 'continue'" @click="pause" class="btn-violet">
                Перерыв
            </div>
            <div v-if="currentAction == 'start' || currentAction == 'continue'" @click="end" class="btn-violet">
                Завершить
            </div>
        </div>
        <div v-if="currentAction == 'pause'" @click="continueDay" class="btn-violet">
            Продолжить
        </div>
    </div>
</template>
<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    props: {},
    data() {
        let user = this.$page.props.user;

        return {
            user: user,
            loading: false,
            currentAction: user.lastAction?.action || 'end',
            breaktime: 0,
            breakTimer: null,
            formatedBreaktime: null,
        };
    },
    mounted() {
        this.loadBreaktime();
        this.startBreakTimer();
        this.formatedBreaktime = this.formatBreaktime(this.breaktime);
    },
    methods: {
        testAction(action) {
            router.post(route('time-check.action'), {
                action: action,
            });
        },
        async loadBreaktime() {
            try {
                const response = await axios.post(route('time-check.breaktime'), {}, {
                    withCredentials: true,
                });

                this.breaktime = response.data.breaktime || 0;
            } catch (error) {
                alert(error.response?.data?.error || 'Ошибка при загрузке времени перерыва');
            }
        },
        startBreakTimer() {
            if (!this.breakTimer && this.currentAction == 'pause') {
                this.breakTimer = setInterval(() => {
                    this.breaktime += 1;
                }, 1000);
            }
        },
        stopBreakTimer() {
            if (this.breakTimer) {
                clearInterval(this.breakTimer);
                this.breakTimer = null;
            }
        },
        async start() {
            try {
                const response = await axios.post(
                    route('time-check.action'),
                    { action: 'start' },
                    { withCredentials: true }
                );

                this.currentAction = 'start';
                this.stopBreakTimer();
            } catch (error) {
                alert(error.response?.data?.error || 'Ошибка начала рабочего дня');
            }
        },
        async pause() {
            try {
                const response = await axios.post(
                    route('time-check.action'),
                    { action: 'pause' },
                    { withCredentials: true }
                );

                this.currentAction = 'pause';
                this.startBreakTimer();
            } catch (error) {
                alert(error.response?.data?.error || 'Ошибка при паузе');
            }
        },
        async continueDay() {
            try {
                const response = await axios.post(
                    route('time-check.action'),
                    { action: 'continue' },
                    { withCredentials: true }
                );

                this.breaktime = response.data.seconds

                this.currentAction = 'start';
                this.stopBreakTimer();
            } catch (error) {
                alert(error.response?.data?.error || 'Ошибка при продолжении');
            }
        },
        async end() {
            try {
                const response = await axios.post(
                    route('time-check.action'),
                    { action: 'end' },
                    { withCredentials: true }
                );

                this.currentAction = 'end';
                this.stopBreakTimer(); // Останавливаем таймер при завершении
            } catch (error) {
                alert(error.response?.data?.error || 'Ошибка при завершении');
            }
        },
        formatBreaktime(seconds){            
            if(seconds == 0){
                return '00:00:00';
            }else{
                return new Date(seconds * 1000).toISOString().substr(11, 8);
            }
        }
    },
    watch: {
        breaktime(seconds) {            
            this.formatedBreaktime = this.formatBreaktime(seconds);
        },
        currentAction(newAction) {
            if (newAction === 'pause') {
                this.startBreakTimer();
            } else {
                this.stopBreakTimer();
            }
        },
    },
};
</script>