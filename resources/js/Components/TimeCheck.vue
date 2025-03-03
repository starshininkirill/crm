<template>
    <div>
        <div v-if="currentAction == 'end'" @click="testAction('start')" class="btn-violet">
            Начать
        </div>
        <div class="flex gap-3">
            <div v-if="currentAction == 'start'" @click="testAction('pause')" class="btn-violet">
                Перерыв
            </div>
            <div v-if="currentAction == 'start'" @click="continue" class="btn-violet">
                Завершить
            </div>
        </div>
        <div v-if="currentAction == 'pause'" @click="end" class="btn-violet">
            Продолжить
        </div>
    </div>
</template>
<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import axios from 'axios';


export default {
    props: {

    },
    data() {
        let user = this.$page.props.user;
        
        return {
            user: user,
            loading: false,
            currentAction: user.lastAction?.action || 'end',
        }
    },
    methods: {
        testAction(action) {
            router.post(route('time-check.action'), {
                action: action,
                onSuccess() {
                    console.log('test');
                },
                onError() {
                    console.log('error');
                },
                onFinish() {
                    console.log('finish');
                }
            });
        },
        async start(day) {
            try {
                const response = await axios.post(route('time-check.action'), {
                    action: 'start',
                },
                    {
                        withCredentials: true
                    });

                console.log('test');

                this.currentAction = 'start';

            } catch (error) {
                alert(error.response.data.error);
            }
        },
        async pause(day) {
            try {
                const response = await axios.post(route('time-check.action'), {
                    action: 'pause',
                },
                    {
                        withCredentials: true
                    });

                this.currentAction = 'pause';

            } catch (error) {
                alert(error.response.data.error);
            }
        },
        async continue(day) {
            try {
                const response = await axios.post(route('time-check.action'), {
                    action: 'continue',
                },
                    {
                        withCredentials: true
                    });

                this.currentAction = 'end';

            } catch (error) {
                alert(error.response.data.error);
            }
        },
        async end(day) {
            try {
                const response = await axios.post(route('time-check.action'), {
                    action: 'end',
                },
                    {
                        withCredentials: true
                    });                

                this.currentAction = 'end';

            } catch (error) {
                alert(error.response.data.error);
            }
        },

    }
}

</script>