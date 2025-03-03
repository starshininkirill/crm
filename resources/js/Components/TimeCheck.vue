<template>
    <div>
        <div class="btn-violet">
            Начать
        </div>
    </div>
</template>
<script> 

export default {
    props: {

    },
    data(){
        console.log(this.$page.props.user);
        
        
        return {
            user: this.$page.props.user,
        }
    }
}

</script>